import {
    BeakerIcon,
    CalendarDaysIcon,
    DocumentTextIcon,
} from '@heroicons/react/24/outline';
import { Badge, Button, Card, EmptyState } from '@/Components/ui';
import type { DispensePatient, Prescription } from './types';

interface PrescriptionPanelProps {
    patient: DispensePatient;
    onDispense: (prescription: Prescription) => void;
    labels: {
        id: string;
        activePrescriptions: string;
        dr: string;
        dispensePrescription: string;
        noActivePrescriptions: string;
    };
}

/**
 * PatientSummary — who is being served, in one hairline strip: monogram, name,
 * identifier and the count of open prescriptions.
 */
function PatientSummary({
    patient,
    idLabel,
    activeLabel,
}: {
    patient: DispensePatient;
    idLabel: string;
    activeLabel: string;
}) {
    const initials = `${patient.first_name?.[0] ?? ''}${patient.last_name?.[0] ?? ''}`;

    return (
        <Card className="flex flex-col items-start justify-between gap-4 p-4 sm:flex-row sm:items-center sm:p-5">
            <div className="flex min-w-0 items-center gap-3.5">
                <span
                    aria-hidden="true"
                    className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-accent text-[15px] font-semibold text-white"
                >
                    {initials}
                </span>
                <div className="min-w-0">
                    <h2 className="truncate text-[17px] font-semibold tracking-tight text-ink">
                        {patient.first_name} {patient.last_name}
                    </h2>
                    <p className="mt-1 font-mono text-[11px] text-ink-subtle">
                        {idLabel}: {patient.patient_uid}
                    </p>
                </div>
            </div>

            <Badge tone="accent" dot>
                {patient.prescriptions.length} {activeLabel}
            </Badge>
        </Card>
    );
}

/**
 * PrescriptionPanel — the prescription flow's main stage: the patient summary
 * and one card per open prescription with its dispense action.
 */
export function PrescriptionPanel({
    patient,
    onDispense,
    labels,
}: PrescriptionPanelProps) {
    return (
        <div className="space-y-4">
            <PatientSummary
                patient={patient}
                idLabel={labels.id}
                activeLabel={labels.activePrescriptions}
            />

            {patient.prescriptions.length === 0 ? (
                <EmptyState
                    icon={DocumentTextIcon}
                    title={labels.noActivePrescriptions}
                />
            ) : (
                <div className="grid gap-4 xl:grid-cols-2">
                    {patient.prescriptions.map((prescription) => (
                        <Card key={prescription.id} className="flex flex-col">
                            <div className="flex items-center gap-3 border-b border-line px-4 py-3">
                                <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-accent">
                                    <CalendarDaysIcon className="h-4.5 w-4.5" />
                                </span>
                                <div className="min-w-0">
                                    <p className="text-[13px] font-semibold text-ink tabular-nums">
                                        {new Date(
                                            prescription.prescription_date,
                                        ).toLocaleDateString()}
                                    </p>
                                    <p className="truncate text-[11.5px] text-ink-subtle">
                                        {labels.dr} {prescription.doctor?.name}
                                    </p>
                                </div>
                            </div>

                            <div className="flex-1 space-y-2 px-4 py-3">
                                {prescription.items.map((item) => (
                                    <div
                                        key={item.id}
                                        className="flex items-center justify-between gap-3 border-b border-line pb-2 text-[12.5px] last:border-0 last:pb-0"
                                    >
                                        <span className="flex min-w-0 items-center gap-2">
                                            <BeakerIcon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                                            <span className="truncate text-ink-muted">
                                                {item.medication.name}
                                            </span>
                                        </span>
                                        <span className="flex shrink-0 flex-col items-end">
                                            <span className="font-semibold text-ink tabular-nums">
                                                x{item.quantity}
                                            </span>
                                            <span className="text-[10.5px] text-ink-subtle">
                                                {item.dosage}
                                            </span>
                                        </span>
                                    </div>
                                ))}
                            </div>

                            <div className="border-t border-line bg-surface-muted/60 px-4 py-3">
                                <Button
                                    type="button"
                                    variant="primary"
                                    fullWidth
                                    onClick={() => onDispense(prescription)}
                                >
                                    {labels.dispensePrescription}
                                </Button>
                            </div>
                        </Card>
                    ))}
                </div>
            )}
        </div>
    );
}
