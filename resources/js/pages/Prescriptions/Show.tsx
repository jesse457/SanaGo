import { DocumentTextIcon, UserIcon } from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    swal,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { Prescription } from '@/types';
import { PrescriptionStatus } from '@/types/enums';

interface Props {
    prescription: Prescription;
}

export default function PrescriptionsShow({ prescription }: Props) {
    const { t } = useTranslation();

    const statusTone = (status: string): BadgeTone => {
        switch (status) {
            case 'active':
            case 'new':
                return 'accent';
            case 'dispensed':
            case 'completed':
                return 'positive';
            case 'cancelled':
                return 'critical';
            default:
                return 'neutral';
        }
    };

    const handleDispense = async () => {
        const confirmed = await swal.confirm({
            tone: 'info',
            title: t(
                'page.prescriptions.dispense_title',
                'Dispense this prescription?',
            ),
            message: t(
                'page.prescriptions.dispense_message',
                'Stock leaves the pharmacy and the prescription closes.',
            ),
            confirmLabel: t('common.continue', 'Continue'),
            cancelLabel: t('common.cancel', 'Cancel'),
        });

        if (!confirmed) {
            return;
        }

        router.post(route('prescriptions.dispense', prescription.id));
    };

    const canDispense =
        prescription.status !== PrescriptionStatus.COMPLETED &&
        prescription.status !== PrescriptionStatus.CANCELLED;

    return (
        <AppLayout
            headerTitle={t('Prescription Detail')}
            headerSubtitle={`${t('Prescribed by')} ${prescription.doctor?.name ?? '—'}`}
        >
            <Head title={t('Prescription Detail')} />
            <div className="mx-auto max-w-5xl space-y-5 pb-12">
                <PageHeader
                    eyebrow={`#${prescription.id}`}
                    title={t('Prescription Detail')}
                    description={`${t('Prescribed by')} ${prescription.doctor?.name ?? '—'}`}
                    breadcrumbs={[
                        { label: t('Clinical') },
                        {
                            label: t('Prescriptions'),
                            href: route('prescriptions.index'),
                        },
                        { label: `#${prescription.id}` },
                    ]}
                    actions={
                        <>
                            <Button
                                href={route('prescriptions.index')}
                                variant="secondary"
                                size="sm"
                            >
                                {t('Back to Prescriptions')}
                            </Button>
                            {canDispense && (
                                <Button
                                    variant="accent"
                                    size="sm"
                                    onClick={handleDispense}
                                >
                                    {t('Dispense')}
                                </Button>
                            )}
                        </>
                    }
                    meta={
                        <Badge
                            tone={statusTone(prescription.status)}
                            variant="outline"
                            dot
                        >
                            {t(prescription.status)}
                        </Badge>
                    }
                />

                <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <Card className="p-5 sm:p-6">
                        <h2 className="flex items-center gap-2 text-[15px] font-semibold tracking-tight text-ink">
                            <UserIcon className="h-4 w-4 text-ink-subtle" />
                            {t('Patient Information')}
                        </h2>
                        <KeyValueGrid columns={2} className="mt-4">
                            <KeyValue
                                label={t('Patient')}
                                value={
                                    prescription.patient
                                        ? `${prescription.patient.first_name} ${prescription.patient.last_name}`
                                        : '—'
                                }
                            />
                            <KeyValue
                                label={t('Doctor')}
                                value={prescription.doctor?.name ?? '—'}
                            />
                            <KeyValue
                                label={t('Date')}
                                value={new Date(
                                    prescription.prescription_date,
                                ).toLocaleDateString()}
                            />
                            <KeyValue
                                label={t('Total')}
                                value={`${Number(prescription.total_amount).toLocaleString()} XAF`}
                            />
                        </KeyValueGrid>
                    </Card>

                    {prescription.notes && (
                        <Card className="p-5 sm:p-6">
                            <h2 className="flex items-center gap-2 text-[15px] font-semibold tracking-tight text-ink">
                                <DocumentTextIcon className="h-4 w-4 text-ink-subtle" />
                                {t('Notes')}
                            </h2>
                            <p className="mt-3 text-[13px] leading-relaxed text-ink-muted">
                                {prescription.notes}
                            </p>
                        </Card>
                    )}
                </div>

                <Card>
                    <div className="border-b border-line px-5 py-4 sm:px-6">
                        <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                            {t('Medications')}
                        </h2>
                    </div>

                    {prescription.items && prescription.items.length > 0 ? (
                        <Table bare>
                            <TableHeader>
                                <TableRow>
                                    <TableHeaderCell>
                                        {t('Medication')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('Dosage')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('Frequency')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('Duration')}
                                    </TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        {t('Qty')}
                                    </TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {prescription.items.map((item) => (
                                    <TableRow key={item.id}>
                                        <TableCell>{item.drug_name}</TableCell>
                                        <TableCell muted>
                                            {item.dosage}
                                        </TableCell>
                                        <TableCell muted>
                                            {item.frequency}
                                        </TableCell>
                                        <TableCell muted>
                                            {item.duration}
                                        </TableCell>
                                        <TableCell numeric muted>
                                            {item.quantity}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    ) : (
                        <div className="p-5 sm:p-6">
                            <EmptyState
                                panel={false}
                                title={t(
                                    'No medications in this prescription.',
                                )}
                            />
                        </div>
                    )}
                </Card>
            </div>
        </AppLayout>
    );
}
