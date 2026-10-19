import { UserCircleIcon } from '@heroicons/react/24/outline';
import { useMemo, useRef, useState } from 'react';
import { Avatar, Button, Field, Input } from '@/Components/ui';
import type { Patient, Translate } from './types';

interface PatientLookupProps {
    patients: Patient[];
    query: string;
    selectedPatient: Patient | null;
    onQueryChange: (value: string) => void;
    onSelect: (patient: Patient) => void;
    error?: string;
    t: Translate;
}

/**
 * PatientLookup — the type-ahead that binds a surgery to a patient record.
 * The query and the selection both live with the page.
 */
export function PatientLookup({
    patients,
    query,
    selectedPatient,
    onQueryChange,
    onSelect,
    error,
    t,
}: PatientLookupProps) {
    const [open, setOpen] = useState(false);
    const blurTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

    const results = useMemo(() => {
        if (!query.trim()) {
            return patients.slice(0, 10);
        }

        const q = query.toLowerCase();

        return patients
            .filter(
                (patient) =>
                    `${patient.first_name} ${patient.last_name}`
                        .toLowerCase()
                        .includes(q) ||
                    patient.patient_uid.toLowerCase().includes(q),
            )
            .slice(0, 10);
    }, [patients, query]);

    return (
        <Field
            label={t('surgeries.create.patient_name')}
            htmlFor="patient_id"
            required
            error={error}
            hint={
                selectedPatient ? (
                    <a
                        href={`/patients/${selectedPatient.id}`}
                        className="font-medium text-accent-ink hover:underline"
                    >
                        {t('surgeries.create.view_profile', {
                            name: selectedPatient.first_name,
                        })}
                    </a>
                ) : null
            }
            className="md:col-span-2"
        >
            <div className="relative">
                <Input
                    id="patient_id"
                    value={query}
                    icon={UserCircleIcon}
                    invalid={Boolean(error)}
                    autoComplete="off"
                    placeholder={t(
                        'surgeries.create.patient_search_placeholder',
                    )}
                    onChange={(event) => {
                        onQueryChange(event.target.value);
                        setOpen(true);
                    }}
                    onFocus={() => setOpen(true)}
                    onBlur={() => {
                        blurTimer.current = setTimeout(
                            () => setOpen(false),
                            200,
                        );
                    }}
                />

                {open && results.length > 0 && (
                    <div className="absolute inset-x-0 top-full z-30 mt-1.5 max-h-60 overflow-y-auto rounded-lg border border-line bg-surface p-1 shadow-float">
                        {results.map((patient) => (
                            <Button
                                key={patient.id}
                                variant="ghost"
                                onClick={() => {
                                    if (blurTimer.current) {
                                        clearTimeout(blurTimer.current);
                                    }

                                    onSelect(patient);
                                    setOpen(false);
                                }}
                                className="flex h-auto w-full items-center justify-start gap-2.5 px-2 py-2 text-left"
                            >
                                <Avatar
                                    size="sm"
                                    name={`${patient.first_name} ${patient.last_name}`}
                                />
                                <span className="min-w-0">
                                    <span className="block truncate text-[13px] font-semibold text-ink">
                                        {patient.first_name} {patient.last_name}
                                    </span>
                                    <span className="block truncate font-mono text-[11px] text-ink-subtle">
                                        {patient.patient_uid} ·{' '}
                                        {patient.gender ?? '—'} ·{' '}
                                        {patient.date_of_birth ?? '—'}
                                    </span>
                                </span>
                            </Button>
                        ))}
                    </div>
                )}
            </div>

            <input type="hidden" value={selectedPatient?.id ?? ''} readOnly />
        </Field>
    );
}

interface DoctorLookupProps {
    id: string;
    label: string;
    required?: boolean;
    doctors: DoctorOption[];
    query: string;
    onQueryChange: (value: string) => void;
    onSelect: (doctor: DoctorOption) => void;
    onClearSelection: () => void;
    error?: string;
    placeholder: string;
}

export interface DoctorOption {
    id: number;
    name: string;
}

/**
 * DoctorLookup — the staff type-ahead shared by the surgeon and anesthetist
 * fields.
 */
export function DoctorLookup({
    id,
    label,
    required = false,
    doctors,
    query,
    onQueryChange,
    onSelect,
    onClearSelection,
    error,
    placeholder,
}: DoctorLookupProps) {
    const [open, setOpen] = useState(false);
    const blurTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

    const results = useMemo(() => {
        if (!query.trim()) {
            return doctors;
        }

        const q = query.toLowerCase();

        return doctors.filter((doctor) =>
            doctor.name.toLowerCase().includes(q),
        );
    }, [doctors, query]);

    return (
        <Field label={label} htmlFor={id} required={required} error={error}>
            <div className="relative">
                <Input
                    id={id}
                    value={query}
                    icon={UserCircleIcon}
                    invalid={Boolean(error)}
                    autoComplete="off"
                    placeholder={placeholder}
                    onChange={(event) => {
                        onQueryChange(event.target.value);
                        setOpen(true);

                        if (!event.target.value) {
                            onClearSelection();
                        }
                    }}
                    onFocus={() => setOpen(true)}
                    onBlur={() => {
                        blurTimer.current = setTimeout(
                            () => setOpen(false),
                            200,
                        );
                    }}
                />

                {open && results.length > 0 && (
                    <div className="absolute inset-x-0 top-full z-30 mt-1.5 max-h-48 overflow-y-auto rounded-lg border border-line bg-surface p-1 shadow-float">
                        {results.map((doctor) => (
                            <Button
                                key={doctor.id}
                                variant="ghost"
                                onClick={() => {
                                    if (blurTimer.current) {
                                        clearTimeout(blurTimer.current);
                                    }

                                    onSelect(doctor);
                                    setOpen(false);
                                }}
                                className="h-auto w-full justify-start gap-2.5 px-2 py-2 text-left"
                            >
                                <Avatar size="xs" name={doctor.name} />
                                <span className="truncate text-[13px] font-medium text-ink">
                                    {doctor.name}
                                </span>
                            </Button>
                        ))}
                    </div>
                )}
            </div>
        </Field>
    );
}
