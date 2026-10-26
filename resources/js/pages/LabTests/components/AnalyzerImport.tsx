import {
    ArrowPathIcon,
    BeakerIcon,
    CheckBadgeIcon,
    CpuChipIcon,
    DocumentArrowUpIcon,
    ExclamationTriangleIcon,
} from '@heroicons/react/24/outline';
import { useRef, useState } from 'react';
import {
    Alert,
    Badge,
    Button,
    Field,
    Select,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Textarea,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { swal } from '@/Components/ui/sweetAlert';
import { useTranslation } from '@/hooks/useTranslation';
import { postJson } from '@/lib/http';

/** One analyte as the analyzer reported it. */
interface Analyte {
    code: string;
    name: string;
    value: string;
    unit: string | null;
    reference_range: string | null;
    flag: string;
    measured_at: string | null;
}

/** The server's read of a frame, from `lab.analyzer.preview`. */
interface AnalyzerPreview {
    format: string;
    analyzer_name: string | null;
    sample_id: string | null;
    patient_name: string | null;
    measured_at: string | null;
    status: 'received' | 'unmatched' | 'rejected';
    error: string | null;
    request: {
        id: number;
        accession_number: string | null;
        patient: {
            first_name: string;
            last_name: string;
            patient_uid: string | null;
        } | null;
        test_definition: { test_name: string; code: string | null } | null;
    } | null;
    analytes: Analyte[];
    results_text: string;
}

interface AnalyzerImportProps {
    /** Applies the read values to the result form the tech is filling in. */
    onApply: (values: { resultsText: string; status?: string }) => void;
}

/** Abnormal flags map to the badge tones the results grid already uses. */
const FLAG_TONE: Record<string, BadgeTone> = {
    N: 'positive',
    H: 'critical',
    L: 'warning',
    HH: 'critical',
    LL: 'critical',
    A: 'critical',
};

const FORMATS = [
    { value: '', label: 'Detect automatically' },
    { value: 'astm', label: 'ASTM E1394 (serial / USB)' },
    { value: 'hl7', label: 'HL7 v2 — ORU^R01' },
    { value: 'csv', label: 'CSV / TSV export' },
];

/**
 * AnalyzerImport — read a run off hospital hardware.
 *
 * The lab bench analyzer (Mindray, Sysmex, Biosystems, Genrui, URIT, Rayto)
 * prints or prints-and-files its run over a serial port using ASTM E1394; some
 * models push HL7 instead, and most can export a CSV report to a USB stick. All
 * three land here: paste the frame, or pick the exported file, and the server
 * reads it and resolves the sample against this hospital's lab orders.
 *
 * Nothing is written from this panel. The tech sees the numbers — flagged
 * abnormal and critical — and then chooses to copy them into the result form,
 * where they are verified and saved like any hand-typed value.
 */
export function AnalyzerImport({ onApply }: AnalyzerImportProps) {
    const { t } = useTranslation();
    const fileInput = useRef<HTMLInputElement>(null);

    const [frame, setFrame] = useState('');
    const [format, setFormat] = useState('');
    const [reading, setReading] = useState(false);
    const [fileName, setFileName] = useState<string | null>(null);
    const [preview, setPreview] = useState<AnalyzerPreview | null>(null);

    const read = async () => {
        if (frame.trim() === '') {
            await swal.warning(
                t(
                    'lab_tests.results.enter.analyzer_empty',
                    'Nothing to read yet',
                ),
                t(
                    'lab_tests.results.enter.analyzer_empty_hint',
                    'Paste the analyzer frame or choose the file the instrument exported.',
                ),
            );

            return;
        }

        setReading(true);

        try {
            const result = await postJson<AnalyzerPreview>(
                route('lab.analyzer.preview'),
                { payload: frame, format: format === '' ? null : format },
            );

            setPreview(result);

            if (result.error !== null) {
                await swal.error(
                    t(
                        'lab_tests.results.enter.analyzer_unreadable',
                        'That frame could not be read',
                    ),
                    result.error,
                );
            }
        } catch (error) {
            await swal.error(
                t(
                    'lab_tests.results.enter.analyzer_failed',
                    'The analyzer frame could not be read',
                ),
                error instanceof Error ? error.message : undefined,
            );
        } finally {
            setReading(false);
        }
    };

    const readFile = async (file: File | undefined) => {
        if (!file) {
            return;
        }

        const text = await file.text();
        setFrame(text);
        setFileName(file.name);
        setPreview(null);
    };

    const apply = () => {
        if (preview === null || preview.analytes.length === 0) {
            return;
        }

        const critical = preview.analytes.some((analyte) =>
            ['HH', 'LL'].includes(analyte.flag),
        );
        const abnormal = preview.analytes.some((analyte) =>
            ['H', 'L', 'HH', 'LL', 'A'].includes(analyte.flag),
        );

        onApply({
            resultsText: preview.results_text,
            // The instrument's own flags decide the status; a panic value can
            // never be filed as normal.
            status: critical ? 'critical' : abnormal ? 'abnormal' : undefined,
        });

        void swal.toast(
            t(
                'lab_tests.results.enter.analyzer_applied',
                'Analyzer values loaded — review and save the result',
            ),
        );
    };

    const unmatched = preview !== null && preview.request === null;

    return (
        <div className="space-y-5">
            <div className="grid gap-4 sm:grid-cols-[1fr_auto] sm:items-end">
                <Field
                    label={t(
                        'lab_tests.results.enter.select_hardware',
                        'Analyzer format',
                    )}
                    htmlFor="analyzer_format"
                >
                    <Select
                        id="analyzer_format"
                        value={format}
                        onChange={(event) => setFormat(event.target.value)}
                    >
                        {FORMATS.map((option) => (
                            <option key={option.value} value={option.value}>
                                {option.value === ''
                                    ? t(
                                          'lab_tests.results.enter.analyzer_auto',
                                          option.label,
                                      )
                                    : option.label}
                            </option>
                        ))}
                    </Select>
                </Field>

                <input
                    ref={fileInput}
                    type="file"
                    accept=".astm,.txt,.hl7,.csv,.tsv,text/plain,text/csv"
                    className="hidden"
                    onChange={(event) => void readFile(event.target.files?.[0])}
                />
                <Button
                    type="button"
                    variant="secondary"
                    icon={DocumentArrowUpIcon}
                    onClick={() => fileInput.current?.click()}
                >
                    {fileName ??
                        t(
                            'lab_tests.results.enter.analyzer_choose_file',
                            'Choose file',
                        )}
                </Button>
            </div>

            <Field
                label={t(
                    'lab_tests.results.enter.analyzer_frame',
                    'Analyzer frame',
                )}
                htmlFor="analyzer_frame"
                hint={t(
                    'lab_tests.results.enter.analyzer_frame_hint',
                    'Paste what the instrument sent — ASTM, HL7 or a CSV run export — or choose the exported file.',
                )}
            >
                <Textarea
                    id="analyzer_frame"
                    rows={5}
                    value={frame}
                    onChange={(event) => {
                        setFrame(event.target.value);
                        setPreview(null);
                    }}
                    placeholder={
                        'H|\\^&|||Mindray^BC-5150…\rR|1|^^^WBC|6.8|10^3/uL|4.0-11.0|N'
                    }
                    className="font-mono text-[11.5px]"
                />
            </Field>

            <div className="flex flex-wrap items-center gap-2">
                <Button
                    type="button"
                    variant="accent"
                    icon={CpuChipIcon}
                    loading={reading}
                    disabled={reading}
                    onClick={() => void read()}
                >
                    {t('lab_tests.results.enter.fetch_data', 'Read results')}
                </Button>
                {preview !== null && preview.analytes.length > 0 && (
                    <Button
                        type="button"
                        variant="primary"
                        icon={CheckBadgeIcon}
                        onClick={apply}
                    >
                        {t(
                            'lab_tests.results.enter.analyzer_apply',
                            'Use these values',
                        )}
                    </Button>
                )}
                {frame !== '' && (
                    <Button
                        type="button"
                        variant="ghost"
                        icon={ArrowPathIcon}
                        onClick={() => {
                            setFrame('');
                            setFileName(null);
                            setPreview(null);
                        }}
                    >
                        {t('common.reset', 'Reset')}
                    </Button>
                )}
            </div>

            {preview !== null && (
                <div className="space-y-4">
                    <div className="flex flex-wrap items-center gap-x-5 gap-y-1.5 rounded-lg border border-line bg-surface-muted px-3.5 py-2.5 text-[12px] text-ink-muted">
                        <span className="inline-flex items-center gap-1.5 font-medium text-ink">
                            <BeakerIcon className="h-3.5 w-3.5" />
                            {preview.analyzer_name ??
                                t(
                                    'lab_tests.results.enter.analyzer_unknown',
                                    'Unknown analyzer',
                                )}
                        </span>
                        <span className="tracking-[0.08em] uppercase">
                            {preview.format}
                        </span>
                        {preview.sample_id && (
                            <span>
                                {t(
                                    'lab_tests.results.enter.analyzer_sample',
                                    'Sample',
                                )}
                                :{' '}
                                <span className="font-mono text-ink">
                                    {preview.sample_id}
                                </span>
                            </span>
                        )}
                        {preview.measured_at && (
                            <span className="tabular-nums">
                                {preview.measured_at}
                            </span>
                        )}
                        <span className="tabular-nums">
                            {preview.analytes.length}{' '}
                            {t(
                                'lab_tests.results.enter.analyzer_analytes',
                                'analytes',
                            )}
                        </span>
                    </div>

                    {unmatched ? (
                        <Alert
                            tone="warning"
                            title={t(
                                'lab_tests.results.enter.analyzer_unmatched',
                                'No matching lab order',
                            )}
                        >
                            {t(
                                'lab_tests.results.enter.analyzer_unmatched_hint',
                                'No order carries this sample id or patient. The values can still be loaded into this result by hand.',
                            )}
                        </Alert>
                    ) : (
                        <div className="flex items-center gap-2 text-[12.5px] text-ink-muted">
                            <CheckBadgeIcon className="h-4 w-4 text-positive" />
                            <span>
                                {preview.request?.patient
                                    ? `${preview.request.patient.first_name} ${preview.request.patient.last_name}`
                                    : preview.patient_name}
                                {preview.request?.test_definition && (
                                    <>
                                        {' — '}
                                        {
                                            preview.request.test_definition
                                                .test_name
                                        }
                                    </>
                                )}
                                {preview.request?.accession_number && (
                                    <span className="ml-1.5 font-mono text-[11.5px] text-ink-subtle">
                                        {preview.request.accession_number}
                                    </span>
                                )}
                            </span>
                        </div>
                    )}

                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>
                                    {t(
                                        'lab_tests.results.enter.analyte',
                                        'Analyte',
                                    )}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t(
                                        'lab_tests.results.enter.result',
                                        'Result',
                                    )}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('lab_tests.results.enter.unit', 'Unit')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t(
                                        'lab_tests.results.enter.reference',
                                        'Reference',
                                    )}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('lab_tests.results.enter.flag', 'Flag')}
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {preview.analytes.map((analyte) => (
                                <TableRow
                                    key={`${analyte.code}-${analyte.name}`}
                                >
                                    <TableCell>
                                        <span className="font-medium text-ink">
                                            {analyte.name}
                                        </span>
                                        <span className="ml-2 font-mono text-[11px] text-ink-subtle">
                                            {analyte.code}
                                        </span>
                                    </TableCell>
                                    <TableCell
                                        numeric
                                        className="font-semibold"
                                    >
                                        {analyte.value}
                                    </TableCell>
                                    <TableCell muted>
                                        {analyte.unit ?? '—'}
                                    </TableCell>
                                    <TableCell muted className="tabular-nums">
                                        {analyte.reference_range ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            tone={
                                                FLAG_TONE[analyte.flag] ??
                                                'neutral'
                                            }
                                            variant="soft"
                                        >
                                            {analyte.flag === 'N'
                                                ? t(
                                                      'lab_tests.results.enter.flag_normal',
                                                      'Normal',
                                                  )
                                                : analyte.flag}
                                        </Badge>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>

                    {preview.analytes.some((analyte) =>
                        ['HH', 'LL'].includes(analyte.flag),
                    ) && (
                        <Alert
                            tone="critical"
                            title={t(
                                'lab_tests.results.enter.analyzer_critical',
                                'Critical value',
                            )}
                        >
                            <span className="inline-flex items-center gap-1.5">
                                <ExclamationTriangleIcon className="h-3.5 w-3.5" />
                                {t(
                                    'lab_tests.results.enter.analyzer_critical_hint',
                                    'The analyzer flagged a panic value. Notify the requesting doctor before saving.',
                                )}
                            </span>
                        </Alert>
                    )}
                </div>
            )}
        </div>
    );
}
