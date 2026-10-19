import {
    ChevronDownIcon,
    ClipboardDocumentIcon,
    ComputerDesktopIcon,
} from '@heroicons/react/24/outline';
import { useState } from 'react';
import {
    Alert,
    Badge,
    Button,
    Dialog,
    KeyValue,
    KeyValueGrid,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import type { Activity } from '@/pages/Settings/Activities';
import { ActivityChanges } from '@/pages/Settings/components/ActivityChanges';
import {
    formatEventName,
    formatModelName,
    getActivityTone,
    getFriendlyUserAgent,
} from '@/pages/Settings/components/activityFormatters';

const TONE_TO_BADGE: Record<string, BadgeTone> = {
    positive: 'positive',
    accent: 'accent',
    warning: 'warning',
    critical: 'critical',
    neutral: 'neutral',
};

/**
 * ActivityDetailsDialog — the full read-out of one audit entry: who, when,
 * what changed, the network/browser context and the raw metadata payload.
 */
export function ActivityDetailsDialog({
    activity,
    onClose,
}: {
    activity: Activity | null;
    onClose: () => void;
}) {
    const { t } = useTranslation();
    const [showAdvanced, setShowAdvanced] = useState(false);
    const [copiedField, setCopiedField] = useState<string | null>(null);

    const handleCopy = async (text: string, field: string) => {
        try {
            await navigator.clipboard.writeText(text);
            setCopiedField(field);
            setTimeout(() => setCopiedField(null), 2000);
        } catch {
            setCopiedField(null);
        }
    };

    const handleClose = () => {
        setShowAdvanced(false);
        setCopiedField(null);
        onClose();
    };

    return (
        <Dialog
            open={activity !== null}
            onClose={handleClose}
            size="xl"
            title={t('page.activities.log_details')}
            footer={
                <Button
                    variant="secondary"
                    icon={ClipboardDocumentIcon}
                    onClick={() =>
                        handleCopy(JSON.stringify(activity, null, 2), 'json')
                    }
                >
                    {copiedField === 'json'
                        ? t('page.activities.copied')
                        : t('page.activities.copy_raw_json')}
                </Button>
            }
        >
            {activity && (
                <div className="space-y-5">
                    <KeyValueGrid columns={2}>
                        <KeyValue
                            label={t('page.activities.col_event')}
                            value={
                                <Badge
                                    tone={
                                        TONE_TO_BADGE[
                                            getActivityTone(
                                                activity.activity_type,
                                            )
                                        ]
                                    }
                                    dot
                                >
                                    {formatEventName(activity.activity_type)}
                                </Badge>
                            }
                        />
                        <KeyValue
                            label={t('page.activities.timestamp')}
                            value={new Date(activity.created_at).toLocaleString(
                                undefined,
                                {
                                    month: 'short',
                                    day: 'numeric',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                },
                            )}
                        />
                        <KeyValue
                            label={t('page.activities.user')}
                            value={
                                activity.user
                                    ? `${activity.user.name} (${activity.user.email})`
                                    : t('page.activities.system')
                            }
                        />
                        <KeyValue
                            label={t('page.activities.col_activity')}
                            value={
                                activity.subject_type
                                    ? `${formatModelName(activity.subject_type)} (ID #${activity.subject_id})`
                                    : t('page.activities.na')
                            }
                        />
                        <KeyValue
                            label={t('page.activities.ip_address')}
                            value={activity.ip_address || '—'}
                            mono
                        />
                        <KeyValue
                            label={t('page.activities.user_agent')}
                            value={
                                <span className="inline-flex items-center gap-1.5">
                                    <ComputerDesktopIcon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                                    {getFriendlyUserAgent(activity.user_agent)}
                                </span>
                            }
                        />
                    </KeyValueGrid>

                    <Alert
                        tone="info"
                        title={t('page.activities.full_description')}
                    >
                        {activity.description}
                    </Alert>

                    <ActivityChanges activity={activity} translate={t} />

                    <div>
                        <button
                            type="button"
                            onClick={() =>
                                setShowAdvanced((previous) => !previous)
                            }
                            aria-expanded={showAdvanced}
                            className="flex w-full items-center justify-between rounded-md bg-surface-muted px-3.5 py-2 text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase transition-colors duration-150 ease-premium hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                        >
                            <span>Raw metadata (JSON)</span>
                            <ChevronDownIcon
                                className={`h-4 w-4 transition-transform duration-200 ease-premium ${showAdvanced ? 'rotate-180' : ''}`}
                            />
                        </button>
                        {showAdvanced && activity.metadata && (
                            <pre className="animate-fade-in mt-2 overflow-x-auto rounded-lg border border-line bg-ink p-3.5 font-mono text-[11.5px] leading-relaxed text-accent-ink">
                                {JSON.stringify(activity.metadata, null, 2)}
                            </pre>
                        )}
                    </div>
                </div>
            )}
        </Dialog>
    );
}
