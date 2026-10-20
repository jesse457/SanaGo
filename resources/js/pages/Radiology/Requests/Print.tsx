import { ArrowLeftIcon, PrinterIcon } from '@heroicons/react/24/outline';
import { Head, usePage } from '@inertiajs/react';
import { Button, PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { RadiologyRequestRecord, TenantIdentity } from '@/types';

interface Props {
    /** `RadiologyRequestController@print` loads `patient` and `requestedBy`. */
    request: RadiologyRequestRecord;
}

function humanise(value?: string | null): string {
    if (!value) {
        return '—';
    }

    return value
        .replace(/[_-]+/g, ' ')
        .replace(/\b\w/g, (character) => character.toUpperCase());
}

function formatDate(value?: string | null): string {
    if (!value) {
        return '—';
    }

    const parsed = new Date(value);

    if (Number.isNaN(parsed.getTime())) {
        return value;
    }

    return parsed.toLocaleDateString(undefined, {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}

/**
 * One labelled field on the printed sheet. Kept black-on-white so photocopies
 * and PDF exports never inherit the app's tinted surfaces.
 */
function PrintField({
    label,
    value,
    className,
}: {
    label: string;
    value: React.ReactNode;
    className?: string;
}) {
    return (
        <div className={className}>
            <p className="text-[9.5px] font-semibold tracking-[0.14em] text-black/55 uppercase">
                {label}
            </p>
            <p className="mt-1 text-[13px] leading-snug font-medium text-black">
                {value === null || value === undefined || value === ''
                    ? '—'
                    : value}
            </p>
        </div>
    );
}

/**
 * Radiology/Requests/Print — the A4 requisition sheet handed to the imaging
 * department. App chrome is hidden at print time; the sheet itself is pure
 * black on white.
 */
export default function RadiologyRequestsPrint({ request }: Props) {
    const { t } = useTranslation();
    const { props } = usePage();
    const tenant = (props as { tenant?: TenantIdentity | null }).tenant;

    const patient = request.patient;
    const patientName = patient
        ? `${patient.first_name} ${patient.last_name}`.trim()
        : '—';

    const demographics = [
        patient?.age ? `${patient.age}` : null,
        patient?.gender ? humanise(patient.gender) : null,
    ]
        .filter(Boolean)
        .join(' · ');

    return (
        <AppLayout headerTitle={t('page.radiology_print.head_title')}>
            <Head title={t('page.radiology_print.head_title')} />

            <div className="mx-auto flex max-w-3xl flex-col gap-6 print:block print:max-w-none">
                <div className="print:hidden">
                    <PageHeader
                        title={t('page.radiology_print.title')}
                        description={`#${request.id} · ${humanise(request.imaging_type)} — ${humanise(request.body_part)}`}
                        breadcrumbs={[
                            { label: t('common.home'), href: '/dashboard' },
                            {
                                label: t('page.radiology_print.head_title'),
                                href: route('radiology.requests.index'),
                            },
                            {
                                label: `#${request.id}`,
                                href: route(
                                    'radiology.requests.show',
                                    request.id,
                                ),
                            },
                            { label: t('page.radiology_print.title') },
                        ]}
                        actions={
                            <>
                                <Button
                                    variant="secondary"
                                    icon={ArrowLeftIcon}
                                    href={route(
                                        'radiology.requests.show',
                                        request.id,
                                    )}
                                >
                                    {t('page.radiology_print.back')}
                                </Button>
                                <Button
                                    icon={PrinterIcon}
                                    onClick={() => window.print()}
                                >
                                    {t('page.radiology_print.print')}
                                </Button>
                            </>
                        }
                    />
                </div>

                {/* ── The A4 sheet ── */}
                <article className="rounded-xl border border-line bg-white px-8 py-9 text-black shadow-surface print:rounded-none print:border-0 print:px-0 print:py-0 print:shadow-none">
                    <header className="flex items-start justify-between gap-6 border-b-2 border-black pb-5">
                        <div className="min-w-0">
                            <p className="text-[17px] leading-tight font-bold tracking-tight text-black uppercase">
                                {tenant?.name ??
                                    t(
                                        'page.radiology_print.hospital_placeholder',
                                    )}
                            </p>
                            <p className="mt-1 text-[10.5px] tracking-[0.16em] text-black/55 uppercase">
                                {t(
                                    'page.radiology_print.head_title',
                                    'Radiology',
                                )}{' '}
                                ·{' '}
                                {t(
                                    'page.radiology_print.document_title',
                                ).toLowerCase()}
                            </p>
                        </div>
                        <div className="shrink-0 text-right">
                            <p className="text-[9.5px] font-semibold tracking-[0.14em] text-black/55 uppercase">
                                {t('page.radiology_print.request_number')}
                            </p>
                            <p className="font-mono text-[13px] font-semibold text-black tabular-nums">
                                RAD-{String(request.id).padStart(6, '0')}
                            </p>
                        </div>
                    </header>

                    <h1 className="mt-6 text-center text-[15px] font-bold tracking-[0.1em] text-black uppercase">
                        {t('page.radiology_print.document_title')}
                    </h1>

                    <section className="mt-6">
                        <h2 className="mb-2.5 border-b border-black/20 pb-1 text-[10px] font-bold tracking-[0.16em] text-black/70 uppercase">
                            {t('page.radiology_print.patient_block')}
                        </h2>
                        <div className="grid grid-cols-2 gap-x-8 gap-y-3">
                            <PrintField
                                label={t('page.radiology_print.patient_name')}
                                value={patientName}
                            />
                            <PrintField
                                label={t('page.radiology_print.patient_uid')}
                                value={patient?.patient_uid}
                            />
                            <PrintField
                                label={t('page.radiology_print.age')}
                                value={
                                    patient?.date_of_birth
                                        ? `${demographics} · ${formatDate(patient.date_of_birth)}`
                                        : demographics
                                }
                            />
                            <PrintField
                                label={t('page.radiology_print.gender')}
                                value={humanise(patient?.gender)}
                            />
                        </div>
                    </section>

                    <section className="mt-6">
                        <h2 className="mb-2.5 border-b border-black/20 pb-1 text-[10px] font-bold tracking-[0.16em] text-black/70 uppercase">
                            {t('page.radiology_print.examination_block')}
                        </h2>
                        <div className="grid grid-cols-2 gap-x-8 gap-y-3">
                            <PrintField
                                label={t('page.radiology_print.imaging_type')}
                                value={humanise(request.imaging_type)}
                            />
                            <PrintField
                                label={t('page.radiology_print.body_part')}
                                value={humanise(request.body_part)}
                            />
                            <PrintField
                                label={t('page.radiology_print.requested_on')}
                                value={formatDate(request.created_at)}
                            />
                            <PrintField
                                label={t('page.radiology_print.status')}
                                value={humanise(request.status)}
                            />
                        </div>
                    </section>

                    <section className="mt-6">
                        <h2 className="mb-2.5 border-b border-black/20 pb-1 text-[10px] font-bold tracking-[0.16em] text-black/70 uppercase">
                            {t('page.radiology_print.clinical_notes')}
                        </h2>
                        <p className="min-h-[4.5rem] border border-black/20 px-3.5 py-3 text-[12.5px] leading-relaxed whitespace-pre-line text-black">
                            {request.clinical_notes ||
                                t('page.radiology_print.no_clinical_notes')}
                        </p>
                    </section>

                    <section className="mt-6 grid grid-cols-2 gap-8">
                        <PrintField
                            label={t('page.radiology_print.ordering_doctor')}
                            value={request.requestedBy?.name}
                        />
                        <div>
                            <p className="text-[9.5px] font-semibold tracking-[0.14em] text-black/55 uppercase">
                                {t('page.radiology_print.ordering_doctor')}
                            </p>
                            <p className="mt-1 text-[13px] font-medium text-black">
                                {request.requestedBy?.name ?? '—'}
                            </p>
                            <div className="mt-8 border-t border-black/40 pt-1">
                                <p className="text-[9.5px] tracking-[0.14em] text-black/55 uppercase">
                                    {t('page.radiology_print.signature')}
                                </p>
                            </div>
                        </div>
                    </section>

                    <div className="mt-8 grid grid-cols-2 gap-8 border-t border-black/20 pt-6">
                        <div>
                            <p className="text-[9.5px] font-semibold tracking-[0.14em] text-black/55 uppercase">
                                {t('page.radiology_print.signature_name')}
                            </p>
                            <div className="mt-6 border-t border-black/40" />
                        </div>
                        <div>
                            <p className="text-[9.5px] font-semibold tracking-[0.14em] text-black/55 uppercase">
                                {t('page.radiology_print.signature_date')}
                            </p>
                            <div className="mt-6 border-t border-black/40" />
                        </div>
                    </div>

                    <footer className="mt-6 border-t border-black/20 pt-4 text-[10px] leading-relaxed text-black/55">
                        {t('page.radiology_print.footer_note')}
                    </footer>
                </article>
            </div>
        </AppLayout>
    );
}
