import {
    MagnifyingGlassIcon,
    PlusIcon,
    UserCircleIcon,
} from '@heroicons/react/24/outline';
import type { ReactNode } from 'react';
import { EmptyState, Input } from '@/Components/ui';
import { cn } from '@/lib/utils';
import type { DispenseMedication, DispensePatient } from './types';

interface DispensePickerProps {
    mode: 'prescription' | 'walkin';
    search: string;
    onSearchChange: (value: string) => void;
    searchPlaceholder: string;
    heading: string;
    patients: DispensePatient[];
    medications: DispenseMedication[];
    selectedPatient: DispensePatient | null;
    onSelectPatient: (patient: DispensePatient) => void;
    onAddMedication: (medication: DispenseMedication) => void;
    noPatientsLabel: string;
    noMedicationsLabel: string;
    stockLabel: string;
    idLabel: string;
}

function RowShell({
    children,
    className,
    onClick,
}: {
    children: ReactNode;
    className?: string;
    onClick: () => void;
}) {
    return (
        <button
            type="button"
            onClick={onClick}
            className={cn(
                'w-full rounded-lg px-3 py-2.5 text-left transition-colors duration-150 ease-premium',
                'focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:ring-offset-1 focus-visible:ring-offset-surface focus-visible:outline-none',
                className,
            )}
        >
            {children}
        </button>
    );
}

/**
 * DispensePicker — the left rail: search plus the live list of patients or
 * medications for the current flow. Selection styling is the only difference
 * between the two modes.
 */
export function DispensePicker({
    mode,
    search,
    onSearchChange,
    searchPlaceholder,
    heading,
    patients,
    medications,
    selectedPatient,
    onSelectPatient,
    onAddMedication,
    noPatientsLabel,
    noMedicationsLabel,
    stockLabel,
    idLabel,
}: DispensePickerProps) {
    const isPrescriptionFlow = mode === 'prescription';

    return (
        <aside className="flex h-[500px] flex-col rounded-xl border border-line bg-surface lg:h-[650px]">
            <div className="space-y-2.5 border-b border-line bg-surface-muted px-4 py-3.5">
                <h2 className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                    {heading}
                </h2>
                <Input
                    icon={MagnifyingGlassIcon}
                    type="search"
                    name="search"
                    value={search}
                    onChange={(event) => onSearchChange(event.target.value)}
                    placeholder={searchPlaceholder}
                    aria-label={searchPlaceholder}
                />
            </div>

            <div className="flex-1 space-y-0.5 overflow-y-auto p-2">
                {isPrescriptionFlow ? (
                    patients.length > 0 ? (
                        patients.map((patient) => {
                            const active = selectedPatient?.id === patient.id;

                            return (
                                <RowShell
                                    key={patient.id}
                                    onClick={() => onSelectPatient(patient)}
                                    className={
                                        active
                                            ? 'bg-accent-soft ring-1 ring-accent/30'
                                            : 'hover:bg-surface-hover'
                                    }
                                >
                                    <p className="truncate text-[13px] font-semibold text-ink">
                                        {patient.first_name} {patient.last_name}
                                    </p>
                                    <p className="mt-0.5 font-mono text-[11px] text-ink-subtle">
                                        {idLabel}: {patient.patient_uid}
                                    </p>
                                </RowShell>
                            );
                        })
                    ) : (
                        <p className="px-3 py-8 text-center text-[13px] text-ink-subtle">
                            {noPatientsLabel}
                        </p>
                    )
                ) : medications.length > 0 ? (
                    medications.map((medication) => (
                        <RowShell
                            key={medication.id}
                            onClick={() => onAddMedication(medication)}
                            className="group flex items-center justify-between gap-3 hover:bg-surface-hover"
                        >
                            <span className="min-w-0">
                                <span className="block truncate text-[13px] font-semibold text-ink">
                                    {medication.name}
                                </span>
                                <span className="mt-0.5 block text-[11px] text-ink-subtle">
                                    {stockLabel}:{' '}
                                    <span className="font-medium text-ink-muted tabular-nums">
                                        {medication.total_stock}
                                    </span>{' '}
                                    {medication.dosage_units}
                                </span>
                            </span>
                            <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-md border border-line bg-surface-muted text-ink-subtle transition-colors duration-150 ease-premium group-hover:border-accent/40 group-hover:bg-accent-soft group-hover:text-accent-ink">
                                <PlusIcon className="h-3.5 w-3.5" />
                            </span>
                        </RowShell>
                    ))
                ) : (
                    <p className="px-3 py-8 text-center text-[13px] text-ink-subtle">
                        {noMedicationsLabel}
                    </p>
                )}
            </div>
        </aside>
    );
}

/** Stand-in for the picker while there is nothing to pick yet. */
export function PickerEmptyState({
    title,
    description,
}: {
    title: string;
    description: string;
}) {
    return (
        <div className="flex h-[500px] items-center justify-center rounded-xl border border-line bg-surface lg:h-[650px]">
            <EmptyState
                panel={false}
                icon={UserCircleIcon}
                title={title}
                description={description}
            />
        </div>
    );
}
