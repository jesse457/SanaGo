import { XMarkIcon } from '@heroicons/react/24/outline';
import {
    Badge,
    Button,
    Input,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';

/** One analysed parameter of a lab result. */
export interface LabResultParameter {
    key: string;
    name: string;
    value: string;
    unit: string;
    referenceRange: string;
}

/** Three-state flag drives the row's badge tone. */
export type ParameterFlag = 'normal' | 'abnormal' | 'pending';

const flagMeta: Record<ParameterFlag, { label: string; tone: BadgeTone }> = {
    normal: { label: 'Normal', tone: 'positive' },
    abnormal: { label: 'Abnormal', tone: 'critical' },
    pending: { label: 'Pending', tone: 'muted' },
};

/**
 * Derives a flag from the typed value against its reference range. Ranges
 * arrive as `3.5 - 5.5`, `< 200`, `> 12` or plain text (`Negative`).
 */
export function parameterFlag(parameter: LabResultParameter): ParameterFlag {
    const rawValue = parameter.value.trim();

    if (rawValue === '') {
        return 'pending';
    }

    const reference = parameter.referenceRange.trim();

    if (reference === '') {
        return 'normal';
    }

    const numeric = Number(rawValue);
    const bounds = reference.match(/-?\d+(?:\.\d+)?/g);

    if (Number.isNaN(numeric) || !bounds || bounds.length === 0) {
        // Qualitative reference (e.g. "Negative"): exact text match is normal.
        return 'normal';
    }

    if (bounds.length >= 2) {
        const low = Number(bounds[0]);
        const high = Number(bounds[1]);

        return numeric < low || numeric > high ? 'abnormal' : 'normal';
    }

    const limit = Number(bounds[0]);

    if (reference.startsWith('<')) {
        return numeric < limit ? 'normal' : 'abnormal';
    }

    if (reference.startsWith('>')) {
        return numeric > limit ? 'normal' : 'abnormal';
    }

    return 'normal';
}

export function ParameterFlagBadge({ flag }: { flag: ParameterFlag }) {
    const { label, tone } = flagMeta[flag];

    return (
        <Badge
            tone={tone}
            dot
            variant={flag === 'pending' ? 'outline' : 'soft'}
        >
            {label}
        </Badge>
    );
}

let parameterSeed = 0;

/**
 * Turns free-text findings into grid rows. Lines look like
 * `WBC: 6.8 x10^3/uL` or `HGB: 12.5 g/dL (13 - 17)`; anything unrecognised
 * still becomes a row so no entered value is lost.
 */
export function parseResultParameters(text: string): LabResultParameter[] {
    return text
        .split('\n')
        .map((line) => line.trim())
        .filter((line) => line !== '')
        .map((line) => {
            const match = line.match(/^([^:]+):\s*(.*)$/);
            const name = (match ? match[1] : line).trim();
            const rest = (match ? match[2] : '').trim();
            const rangeMatch = rest.match(/^(.*?)\s*\(([^)]*)\)\s*$/);
            const body = rangeMatch ? rangeMatch[1] : rest;
            const referenceRange = rangeMatch ? rangeMatch[2].trim() : '';
            const valueMatch = body.match(/^([-+]?\d*\.?\d+)\s*(.*)$/);

            parameterSeed += 1;

            return {
                key: `param-${parameterSeed}`,
                name,
                value: valueMatch ? valueMatch[1] : body,
                unit: valueMatch ? valueMatch[2].trim() : '',
                referenceRange,
            };
        });
}

interface ParameterResultsTableProps {
    parameters: LabResultParameter[];
    onChange: (
        index: number,
        field: 'value' | 'unit' | 'referenceRange',
        value: string,
    ) => void;
    onClearRow: (index: number) => void;
}

/**
 * ParameterResultsTable — the dense clinical grid: one row per analysed
 * parameter with value, unit and reference range, plus the derived result flag.
 * The raw findings text stays the editable source of truth; the grid is the
 * structured read of it.
 */
export function ParameterResultsTable({
    parameters,
    onChange,
    onClearRow,
}: ParameterResultsTableProps) {
    return (
        <div className="overflow-hidden rounded-lg border border-line">
            <Table bare>
                <TableHeader>
                    <TableRow className="hover:bg-transparent">
                        <TableHeaderCell>Parameter</TableHeaderCell>
                        <TableHeaderCell numeric>Value</TableHeaderCell>
                        <TableHeaderCell>Unit</TableHeaderCell>
                        <TableHeaderCell>Reference range</TableHeaderCell>
                        <TableHeaderCell>Flag</TableHeaderCell>
                        <TableHeaderCell numeric>
                            <span className="sr-only">Actions</span>
                        </TableHeaderCell>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {parameters.map((parameter, index) => (
                        <TableRow
                            key={parameter.key}
                            className="hover:bg-transparent"
                        >
                            <TableCell>
                                <span className="text-[13px] font-medium text-ink">
                                    {parameter.name}
                                </span>
                            </TableCell>
                            <TableCell className="w-32">
                                <Input
                                    aria-label={`${parameter.name} value`}
                                    value={parameter.value}
                                    inputMode="decimal"
                                    placeholder="—"
                                    onChange={(event) =>
                                        onChange(
                                            index,
                                            'value',
                                            event.target.value,
                                        )
                                    }
                                    className="h-9 text-right text-[13px] tabular-nums"
                                />
                            </TableCell>
                            <TableCell className="w-28">
                                <Input
                                    aria-label={`${parameter.name} unit`}
                                    value={parameter.unit}
                                    onChange={(event) =>
                                        onChange(
                                            index,
                                            'unit',
                                            event.target.value,
                                        )
                                    }
                                    className="h-9 text-[13px] text-ink-muted"
                                />
                            </TableCell>
                            <TableCell className="w-40">
                                <Input
                                    aria-label={`${parameter.name} reference range`}
                                    value={parameter.referenceRange}
                                    onChange={(event) =>
                                        onChange(
                                            index,
                                            'referenceRange',
                                            event.target.value,
                                        )
                                    }
                                    className="h-9 text-[13px] text-ink-muted tabular-nums"
                                />
                            </TableCell>
                            <TableCell>
                                <ParameterFlagBadge
                                    flag={parameterFlag(parameter)}
                                />
                            </TableCell>
                            <TableCell numeric>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    aria-label={`Clear ${parameter.name}`}
                                    icon={XMarkIcon}
                                    onClick={() => onClearRow(index)}
                                />
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}

/** Footer summary for the grid: total rows and how many fall outside range. */
export function ParameterResultsSummary({
    parameters,
}: {
    parameters: LabResultParameter[];
}) {
    const abnormalCount = parameters.filter(
        (parameter) => parameterFlag(parameter) === 'abnormal',
    ).length;
    const pendingCount = parameters.filter(
        (parameter) => parameterFlag(parameter) === 'pending',
    ).length;

    return (
        <p className="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11.5px] text-ink-subtle">
            <span className="tabular-nums">
                {parameters.length}{' '}
                {parameters.length === 1 ? 'parameter' : 'parameters'}
            </span>
            {abnormalCount > 0 && (
                <span className="font-semibold text-critical-ink tabular-nums">
                    {} outside range
                </span>
            )}
            {pendingCount > 0 && (
                <span className="tabular-nums">{pendingCount} pending</span>
            )}
        </p>
    );
}
