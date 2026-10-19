import { XMarkIcon } from '@heroicons/react/24/outline';
import { Avatar, Badge, Button, KeyValue } from '@/Components/ui';
import type { Patient, Translate } from './types';

interface PatientSidebarProps {
    patient: Patient | null;
    onClearPatient: () => void;
    onClose?: () => void;
    t: Translate;
}

/**
 * PatientSidebar — the identity rail of the consultation workspace: who the
 * note is for, how to reach them and whether they carry allergies.
 */
export function PatientSidebar({
    patient,
    onClearPatient,
    onClose,
    t,
}: PatientSidebarProps) {
    return (
        <>
            {onClose && (
                <div className="flex items-center justify-between border-b border-line px-4 py-3">
                    <h2 className="text-[13px] font-semibold tracking-tight text-ink">
                        {t('searchPatient')}
                    </h2>
                    <Button
                        variant="ghost"
                        size="icon"
                        icon={XMarkIcon}
                        aria-label={t('searchPatient')}
                        onClick={onClose}
                    />
                </div>
            )}

            <div className="flex-1 overflow-y-auto p-5">
                {patient ? (
                    <div className="space-y-5">
                        <div className="flex flex-col items-center text-center">
                            <Avatar
                                size="lg"
                                name={`${patient.first_name} ${patient.last_name}`}
                            />
                            <h2 className="mt-3 text-[15px] font-semibold tracking-tight text-ink">
                                {patient.first_name} {patient.last_name}
                            </h2>
                            <div className="mt-2 flex flex-wrap items-center justify-center gap-1.5">
                                <Badge tone="neutral" variant="outline">
                                    {patient.age !== null
                                        ? `${patient.age} ${t('yrs')}`
                                        : t('na')}
                                </Badge>
                                <Badge tone="neutral" variant="outline">
                                    {patient.gender || t('unknown')}
                                </Badge>
                            </div>
                            <Button
                                variant="ghost"
                                size="sm"
                                className="mt-2 h-auto px-0 text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase hover:bg-transparent hover:text-accent-ink"
                                onClick={onClearPatient}
                            >
                                {t('changePatient')}
                            </Button>
                        </div>

                        <KeyValue
                            tile
                            label={t('contact')}
                            value={patient.phone || t('noContactProvided')}
                        />

                        <KeyValue
                            tile
                            label={t('allergies')}
                            value={
                                <span className="flex items-center gap-2">
                                    <Badge
                                        tone={
                                            patient.allergies
                                                ? 'critical'
                                                : 'positive'
                                        }
                                        dot
                                    >
                                        {patient.allergies
                                            ? t('allergies')
                                            : t('noKnownAllergies')}
                                    </Badge>
                                    <span className="truncate text-[12px] text-ink-muted">
                                        {patient.allergies || ''}
                                    </span>
                                </span>
                            }
                        />
                    </div>
                ) : (
                    <div className="flex h-full flex-col items-center justify-center text-center text-ink-subtle">
                        <Badge tone="muted">{t('selectPatient')}</Badge>
                    </div>
                )}
            </div>
        </>
    );
}

interface PatientBannerProps {
    patient: Patient;
    onOpen: () => void;
    t: Translate;
}

/**
 * PatientBanner — the compact mobile strip that keeps the patient visible while
 * the drawer is closed.
 */
export function PatientBanner({ patient, onOpen, t }: PatientBannerProps) {
    return (
        <div className="flex items-center gap-3 border-b border-line bg-surface px-4 py-2 lg:hidden">
            <Avatar
                size="sm"
                name={`${patient.first_name} ${patient.last_name}`}
            />
            <div className="min-w-0 flex-1">
                <p className="truncate text-[13px] font-semibold text-ink">
                    {patient.first_name} {patient.last_name}
                </p>
                <p className="truncate text-[11px] text-ink-subtle">
                    {[
                        patient.age !== null
                            ? `${patient.age} ${t('yrs')}`
                            : null,
                        patient.phone,
                    ]
                        .filter(Boolean)
                        .join(' · ')}
                </p>
            </div>
            {patient.allergies && (
                <Badge tone="critical" dot className="shrink-0">
                    {t('allergies')}
                </Badge>
            )}
            <Button
                variant="ghost"
                size="sm"
                className="shrink-0 px-2 text-[12px] font-semibold text-accent-ink hover:bg-transparent"
                onClick={onOpen}
            >
                {t('view')}
            </Button>
        </div>
    );
}
