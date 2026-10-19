import {
    MagnifyingGlassIcon,
    UserCircleIcon,
} from '@heroicons/react/24/outline';
import { useMemo, useState } from 'react';
import { Avatar, Button, EmptyState, Field, Input } from '@/Components/ui';
import type { Patient, Translate } from './types';

interface PatientSearchPanelProps {
    patients: Patient[];
    query: string;
    onQueryChange: (value: string) => void;
    onSelect: (patient: Patient) => void;
    error?: string;
    t: Translate;
}

/**
 * PatientSearchPanel — the search field and result list at the top of the
 * patient rail. The query lives with the page so the desktop rail and the
 * mobile drawer stay in sync.
 */
export function PatientSearchPanel({
    patients,
    query,
    onQueryChange,
    onSelect,
    error,
    t,
}: PatientSearchPanelProps) {
    const [touched, setTouched] = useState(false);

    const results = useMemo(() => {
        if (query.length < 2) {
            return [];
        }

        const q = query.toLowerCase();

        return patients.filter(
            (patient) =>
                `${patient.first_name} ${patient.last_name}`
                    .toLowerCase()
                    .includes(q) ||
                patient.patient_uid?.toLowerCase().includes(q) ||
                patient.phone?.includes(q),
        );
    }, [query, patients]);

    const showResults = touched && results.length > 0;

    return (
        <div className="border-b border-line bg-surface-muted p-4">
            <Field
                label={t('searchPatient')}
                htmlFor="patient_search"
                error={error}
            >
                <div className="relative">
                    <Input
                        id="patient_search"
                        value={query}
                        icon={MagnifyingGlassIcon}
                        invalid={Boolean(error)}
                        autoComplete="off"
                        placeholder={t('nameOrId')}
                        onChange={(event) => onQueryChange(event.target.value)}
                        onFocus={() => setTouched(true)}
                        onBlur={() => setTimeout(() => setTouched(false), 180)}
                    />

                    {showResults && (
                        <div className="absolute inset-x-0 top-full z-30 mt-1.5 max-h-64 overflow-y-auto rounded-lg border border-line bg-surface p-1 shadow-float">
                            {results.map((patient) => (
                                <Button
                                    key={patient.id}
                                    variant="ghost"
                                    onClick={() => onSelect(patient)}
                                    className="h-auto w-full justify-start gap-2.5 px-2 py-2"
                                >
                                    <Avatar
                                        size="sm"
                                        name={`${patient.first_name} ${patient.last_name}`}
                                    />
                                    <span className="min-w-0 text-left">
                                        <span className="block truncate text-[13px] font-semibold text-ink">
                                            {patient.first_name}{' '}
                                            {patient.last_name}
                                        </span>
                                        <span className="block truncate font-mono text-[11px] text-ink-subtle">
                                            {patient.patient_uid || t('noUid')}
                                        </span>
                                    </span>
                                </Button>
                            ))}
                        </div>
                    )}
                </div>
            </Field>
        </div>
    );
}

interface ConsultationEmptyWorkspaceProps {
    onSearch: () => void;
    t: Translate;
}

/**
 * ConsultationEmptyWorkspace — the canvas before a patient is chosen.
 */
export function ConsultationEmptyWorkspace({
    onSearch,
    t,
}: ConsultationEmptyWorkspaceProps) {
    return (
        <div className="flex flex-1 flex-col items-center justify-center p-6">
            <EmptyState
                panel={false}
                icon={UserCircleIcon}
                title={t('selectPatientToBegin')}
                description={t('selectPatientToBeginDesc')}
                action={
                    <Button
                        icon={MagnifyingGlassIcon}
                        onClick={onSearch}
                        className="lg:hidden"
                    >
                        {t('searchPatient')}
                    </Button>
                }
            />
        </div>
    );
}
