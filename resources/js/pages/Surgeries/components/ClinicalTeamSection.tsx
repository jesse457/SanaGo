import { BriefcaseIcon, UserIcon } from '@heroicons/react/24/outline';
import type { ReactNode } from 'react';
import { Button, Field, Input, SectionCard } from '@/Components/ui';
import { BedLookup } from './BedLookup';
import { DoctorLookup } from './PatientLookup';
import type { Bed, Doctor, SetSurgeryData, Translate } from './types';

interface ClinicalTeamSectionProps {
    doctors: Doctor[];
    beds: Bed[];
    isExternalSurgeon: boolean;
    isExternalAnesthetist: boolean;
    surgeonQuery: string;
    anesthetistQuery: string;
    surgeonName: string;
    anesthetistName: string;
    bedQuery: string;
    surgeonId: string;
    anesthetistId: string;
    errors: Partial<Record<string, string>>;
    onSurgeonQueryChange: (value: string) => void;
    onSurgeonSelect: (doctor: Doctor) => void;
    onToggleExternalSurgeon: () => void;
    onAnesthetistQueryChange: (value: string) => void;
    onAnesthetistSelect: (doctor: Doctor) => void;
    onToggleExternalAnesthetist: () => void;
    onBedQueryChange: (value: string) => void;
    onBedSelect: (bed: Bed) => void;
    /** The patient selector, owned by the page and dropped into this card. */
    patientField: ReactNode;
    setData: SetSurgeryData;
    t: Translate;
}

/**
 * ClinicalTeamSection — who operates, who anesthetises and where the patient
 * recovers. Hospital staff come from the directory; external clinicians are
 * recorded by name.
 */
export function ClinicalTeamSection({
    doctors,
    beds,
    isExternalSurgeon,
    isExternalAnesthetist,
    surgeonQuery,
    anesthetistQuery,
    surgeonName,
    anesthetistName,
    bedQuery,
    surgeonId,
    anesthetistId,
    errors,
    onSurgeonQueryChange,
    onSurgeonSelect,
    onToggleExternalSurgeon,
    onAnesthetistQueryChange,
    onAnesthetistSelect,
    onToggleExternalAnesthetist,
    onBedQueryChange,
    onBedSelect,
    patientField,
    setData,
    t,
}: ClinicalTeamSectionProps) {
    return (
        <SectionCard
            icon={BriefcaseIcon}
            title={t('surgeries.create.section_clinical_team')}
        >
            <div className="grid grid-cols-1 gap-x-4 gap-y-5 md:grid-cols-2">
                {patientField}

                <div className="min-w-0">
                    {isExternalSurgeon ? (
                        <Field
                            label={t('surgeries.create.surgeon')}
                            htmlFor="surgeon_name"
                            required
                            error={errors.surgeon_name}
                        >
                            <Input
                                id="surgeon_name"
                                value={surgeonName}
                                icon={UserIcon}
                                invalid={Boolean(errors.surgeon_name)}
                                onChange={(event) =>
                                    setData('surgeon_name', event.target.value)
                                }
                                placeholder={t(
                                    'surgeries.create.surgeon_name_placeholder',
                                )}
                            />
                        </Field>
                    ) : (
                        <DoctorLookup
                            id="surgeon_id"
                            label={t('surgeries.create.surgeon')}
                            required
                            doctors={doctors}
                            query={surgeonQuery}
                            onQueryChange={(value) => {
                                onSurgeonQueryChange(value);

                                if (!value) {
                                    setData('surgeon_id', '');
                                }
                            }}
                            onSelect={onSurgeonSelect}
                            onClearSelection={() => setData('surgeon_id', '')}
                            error={errors.surgeon_id ?? errors.surgeon_name}
                            placeholder={t(
                                'surgeries.create.surgeon_placeholder',
                            )}
                        />
                    )}

                    <input type="hidden" name="surgeon_id" value={surgeonId} />

                    <Button
                        variant="ghost"
                        size="sm"
                        className="mt-1 h-auto px-0 text-[12px] font-semibold text-accent-ink hover:bg-transparent hover:text-accent-hover"
                        onClick={onToggleExternalSurgeon}
                    >
                        {isExternalSurgeon
                            ? t('surgeries.create.select_hospital_surgeon')
                            : t('surgeries.create.external_surgeon')}
                    </Button>
                </div>

                <div className="min-w-0">
                    {isExternalAnesthetist ? (
                        <Field
                            label={t('surgeries.create.anesthetist')}
                            htmlFor="anesthetist_name"
                            error={errors.anesthetist_name}
                        >
                            <Input
                                id="anesthetist_name"
                                value={anesthetistName}
                                icon={UserIcon}
                                invalid={Boolean(errors.anesthetist_name)}
                                onChange={(event) =>
                                    setData(
                                        'anesthetist_name',
                                        event.target.value,
                                    )
                                }
                                placeholder={t(
                                    'surgeries.create.anesthetist_name_placeholder',
                                )}
                            />
                        </Field>
                    ) : (
                        <DoctorLookup
                            id="anesthetist_id"
                            label={t('surgeries.create.anesthetist')}
                            doctors={doctors}
                            query={anesthetistQuery}
                            onQueryChange={(value) => {
                                onAnesthetistQueryChange(value);

                                if (!value) {
                                    setData('anesthetist_id', '');
                                }
                            }}
                            onSelect={onAnesthetistSelect}
                            onClearSelection={() =>
                                setData('anesthetist_id', '')
                            }
                            error={
                                errors.anesthetist_id ?? errors.anesthetist_name
                            }
                            placeholder={t(
                                'surgeries.create.anesthetist_placeholder',
                            )}
                        />
                    )}

                    <input
                        type="hidden"
                        name="anesthetist_id"
                        value={anesthetistId}
                    />

                    <Button
                        variant="ghost"
                        size="sm"
                        className="mt-1 h-auto px-0 text-[12px] font-semibold text-accent-ink hover:bg-transparent hover:text-accent-hover"
                        onClick={onToggleExternalAnesthetist}
                    >
                        {isExternalAnesthetist
                            ? t('surgeries.create.select_hospital_anesthetist')
                            : t('surgeries.create.external_anesthetist')}
                    </Button>
                </div>

                <BedLookup
                    beds={beds}
                    query={bedQuery}
                    onQueryChange={onBedQueryChange}
                    onSelect={onBedSelect}
                    onClearSelection={() => setData('bed_id', '')}
                    error={errors.bed_id}
                    t={t}
                />
            </div>
        </SectionCard>
    );
}
