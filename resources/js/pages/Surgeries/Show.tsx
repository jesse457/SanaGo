import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    PencilSquareIcon,
    Squares2X2Icon,
    BanknotesIcon,
    UserIcon,
    BeakerIcon,
    MapPinIcon,
    DocumentTextIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import {
    Alert,
    Avatar,
    Badge,
    Button,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Surgery {
    id: number;
    patient?: { id: number; first_name: string; last_name: string };
    surgeon?: { id: number; name: string };
    anesthetist?: { id: number; name: string } | null;
    bed?: { id: number; name: string; ward?: { name: string } } | null;
    surgery_name: string;
    description?: string;
    scheduled_at: string;
    completed_at?: string;
    estimated_duration_minutes?: number;
    urgency: string;
    status: string;
    surgery_fee: number;
    notes?: string;
    complications?: string;
}

interface Props {
    surgery: Surgery;
}

const URGENCY_TONE: Record<string, BadgeTone> = {
    emergency: 'critical',
    urgent: 'warning',
    routine: 'accent',
};

const STATUS_TONE: Record<string, BadgeTone> = {
    completed: 'positive',
    in_progress: 'warning',
    cancelled: 'critical',
    scheduled: 'accent',
};

/**
 * Surgeries/Show — the read-only surgical record: identity, theatre team, the
 * booked slot and the operative notes.
 */
export default function SurgeryShow({ surgery }: Props) {
    const { t } = useTranslation();
    const patientName = [
        surgery.patient?.first_name,
        surgery.patient?.last_name,
    ]
        .filter(Boolean)
        .join(' ');

    return (
        <AppLayout
            headerTitle={surgery.surgery_name}
            headerSubtitle={t('surgeries.show.subtitle')}
        >
            <Head
                title={t('surgeries.show.head_title', {
                    name: surgery.surgery_name,
                })}
            />

            <div className="mx-auto max-w-4xl space-y-5">
                <PageHeader
                    title={surgery.surgery_name}
                    description={t('surgeries.show.subtitle')}
                    breadcrumbs={[
                        { label: t('common.home'), href: route('home') },
                        {
                            label: t('surgeries.index.surgeries'),
                            href: route('surgeries.index'),
                        },
                        { label: `#${surgery.id}` },
                    ]}
                    meta={
                        <>
                            <Badge
                                tone={STATUS_TONE[surgery.status] ?? 'neutral'}
                                dot
                            >
                                {surgery.status.replace('_', ' ')}
                            </Badge>
                            <Badge
                                tone={
                                    URGENCY_TONE[
                                        surgery.urgency.toLowerCase()
                                    ] ?? 'accent'
                                }
                                variant="outline"
                            >
                                {surgery.urgency}
                            </Badge>
                            {surgery.estimated_duration_minutes
                                ? statusDuration(
                                      surgery.estimated_duration_minutes,
                                      t,
                                  )
                                : null}
                        </>
                    }
                    actions={
                        <>
                            <Button
                                href={route('surgeries.index')}
                                variant="secondary"
                                icon={ArrowLeftIcon}
                            >
                                {t('common.back')}
                            </Button>
                            <Button
                                href={route('surgeries.edit', surgery.id)}
                                icon={PencilSquareIcon}
                            >
                                {t('common.edit')}
                            </Button>
                        </>
                    }
                />

                <SectionCard
                    icon={Squares2X2Icon}
                    title={t('surgeries.show.subtitle')}
                >
                    <KeyValueGrid columns={3}>
                        <KeyValue
                            label={t('surgeries.show.patient')}
                            icon={UserIcon}
                            value={
                                surgery.patient ? (
                                    <span className="flex items-center gap-2">
                                        <Avatar size="xs" name={patientName} />
                                        <span className="truncate">
                                            {patientName}
                                        </span>
                                    </span>
                                ) : null
                            }
                        />
                        <KeyValue
                            label={t('surgeries.show.surgeon')}
                            icon={UserIcon}
                            value={surgery.surgeon?.name}
                        />
                        <KeyValue
                            label={t('surgeries.show.anesthetist')}
                            icon={BeakerIcon}
                            value={surgery.anesthetist?.name || t('common.na')}
                        />
                        <KeyValue
                            label={t('surgeries.show.bed_ward')}
                            icon={MapPinIcon}
                            value={
                                <>
                                    {surgery.bed?.name || t('common.na')}{' '}
                                    {surgery.bed?.ward?.name &&
                                        `(${surgery.bed.ward.name})`}
                                </>
                            }
                        />
                        <KeyValue
                            label={t('surgeries.show.scheduled')}
                            icon={CalendarDaysIcon}
                            value={new Date(
                                surgery.scheduled_at,
                            ).toLocaleString()}
                        />
                        <KeyValue
                            label={t('surgeries.show.status_urgency')}
                            value={
                                <span className="capitalize">
                                    {surgery.status.replace('_', ' ')} —{' '}
                                    {surgery.urgency}
                                </span>
                            }
                        />
                        <KeyValue
                            label={t('surgeries.show.fee')}
                            icon={BanknotesIcon}
                            value={`${surgery.surgery_fee.toLocaleString()} FCFA`}
                        />
                        <KeyValue
                            label={t('surgeries.show.duration')}
                            icon={ClockIcon}
                            value={
                                surgery.estimated_duration_minutes
                                    ? t('surgeries.show.duration_minutes', {
                                          minutes:
                                              surgery.estimated_duration_minutes,
                                      })
                                    : t('common.na')
                            }
                        />
                    </KeyValueGrid>
                </SectionCard>

                {surgery.description && (
                    <SectionCard
                        icon={DocumentTextIcon}
                        title={t('surgeries.create.description')}
                    >
                        <p className="text-[13px] leading-relaxed text-ink-muted">
                            {surgery.description}
                        </p>
                    </SectionCard>
                )}

                {surgery.notes && (
                    <SectionCard
                        icon={DocumentTextIcon}
                        title={t('surgeries.show.notes')}
                    >
                        <p className="text-[13px] leading-relaxed text-ink-muted">
                            {surgery.notes}
                        </p>
                    </SectionCard>
                )}

                {surgery.complications && (
                    <Alert
                        tone="critical"
                        title={t('surgeries.show.complications')}
                    >
                        <span className="flex items-start gap-2">
                            <ExclamationTriangleIcon className="mt-0.5 h-4 w-4 shrink-0" />
                            {surgery.complications}
                        </span>
                    </Alert>
                )}
            </div>
        </AppLayout>
    );
}

function statusDuration(
    minutes: number,
    t: (key: string, replacements?: Record<string, string | number>) => string,
) {
    return (
        <span className="flex items-center gap-1.5">
            <ClockIcon className="h-3.5 w-3.5" />
            {t('surgeries.show.duration_minutes', { minutes })}
        </span>
    );
}
