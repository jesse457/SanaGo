import { UserCircleIcon } from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';
import { useMemo } from 'react';
import { Avatar, Field, Input, Select } from '@/Components/ui';

export interface EmergencyPatient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
    date_of_birth: string | null;
    gender: string | null;
}

export function emergencyPatientName(patient: EmergencyPatient): string {
    return `${patient.first_name} ${patient.last_name}`;
}

interface PatientSearchFieldProps {
    patients: EmergencyPatient[];
    query: string;
    onQueryChange: (value: string) => void;
    open: boolean;
    onOpenChange: (open: boolean) => void;
    selected: EmergencyPatient | null;
    onSelect: (patient: EmergencyPatient) => void;
    /** Hidden input mirroring the committed form value. */
    patientId: string;
    label: string;
    placeholder: string;
    viewProfileLabel: string;
    error?: string;
}

/**
 * PatientSearchField — the intake patient picker. Splits the search text (local
 * UI state) from the committed `patient_id` (form value) so typing never writes
 * to the payload until a real patient is chosen.
 */
export function PatientSearchField({
    patients,
    query,
    onQueryChange,
    open,
    onOpenChange,
    selected,
    onSelect,
    patientId,
    label,
    placeholder,
    viewProfileLabel,
    error,
}: PatientSearchFieldProps) {
    const matches = useMemo(() => {
        const term = query.trim().toLowerCase();

        if (!term) {
            return patients.slice(0, 10);
        }

        return patients
            .filter(
                (patient) =>
                    emergencyPatientName(patient)
                        .toLowerCase()
                        .includes(term) ||
                    patient.patient_uid.toLowerCase().includes(term),
            )
            .slice(0, 10);
    }, [patients, query]);

    return (
        <Field
            label={label}
            htmlFor="patient_search"
            required
            error={error}
            hint={
                selected && (
                    <Link
                        href={`/patients/${selected.id}`}
                        className="font-medium text-accent-ink hover:underline"
                    >
                        {viewProfileLabel}
                    </Link>
                )
            }
            className="sm:col-span-2"
        >
            <div className="relative">
                <Input
                    id="patient_search"
                    autoComplete="off"
                    icon={UserCircleIcon}
                    value={query}
                    placeholder={placeholder}
                    invalid={Boolean(error)}
                    onChange={(event) => {
                        onQueryChange(event.target.value);
                        onOpenChange(true);
                    }}
                    onFocus={() => onOpenChange(true)}
                    onBlur={() => setTimeout(() => onOpenChange(false), 200)}
                />

                {open && matches.length > 0 && (
                    <div className="absolute inset-x-0 top-full z-30 mt-1.5 max-h-60 overflow-y-auto rounded-lg border border-line bg-surface p-1 shadow-float">
                        {matches.map((patient) => (
                            <button
                                key={patient.id}
                                type="button"
                                onClick={() => onSelect(patient)}
                                className="flex w-full items-center gap-2.5 rounded-md px-2 py-2 text-left transition-colors duration-150 ease-premium hover:bg-surface-hover focus-visible:bg-surface-hover focus-visible:outline-none"
                            >
                                <Avatar
                                    size="sm"
                                    name={emergencyPatientName(patient)}
                                />
                                <span className="min-w-0">
                                    <span className="block truncate text-[13px] font-semibold text-ink">
                                        {emergencyPatientName(patient)}
                                    </span>
                                    <span className="block truncate font-mono text-[11px] text-ink-subtle">
                                        {[
                                            patient.patient_uid,
                                            patient.gender,
                                            patient.date_of_birth,
                                        ]
                                            .filter(Boolean)
                                            .join(' · ')}
                                    </span>
                                </span>
                            </button>
                        ))}
                    </div>
                )}

                <input type="hidden" value={patientId} readOnly />
            </div>
        </Field>
    );
}

interface TriageLevelFieldProps {
    id?: string;
    label: string;
    value: string;
    onChange: (value: string) => void;
    error?: string;
    options: { value: string; label: string }[];
    hint?: string;
}

/**
 * TriageLevelField — the priority selector. Priority is the highest-stakes
 * field on the form, so it gets its own control plus the tone legend.
 */
export function TriageLevelField({
    id = 'triage_level',
    label,
    value,
    onChange,
    error,
    options,
    hint,
}: TriageLevelFieldProps) {
    return (
        <Field label={label} htmlFor={id} required error={error} hint={hint}>
            <Select
                id={id}
                name={id}
                value={value}
                onChange={(event) => onChange(event.target.value)}
                invalid={Boolean(error)}
            >
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </Select>
        </Field>
    );
}
