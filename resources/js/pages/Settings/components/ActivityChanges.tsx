import { KeyValue, KeyValueGrid } from '@/Components/ui';
import { cn } from '@/lib/utils';
import {
    formatFieldName,
    formatFriendlyValue,
} from '@/pages/Settings/components/activityFormatters';
import type { Translate } from '@/pages/Settings/components/activityFormatters';

export interface ActivityAttributeChanges {
    attributes?: Record<string, any> | null;
    old?: Record<string, any> | null;
}

interface FieldGridProps {
    entries: [string, unknown][];
    caption: string;
    tone: 'positive' | 'critical' | 'neutral';
    translate: Translate;
    strikeThrough?: boolean;
}

const captionTone: Record<FieldGridProps['tone'], string> = {
    positive: 'text-positive-ink',
    critical: 'text-critical-ink',
    neutral: 'text-ink-subtle',
};

const cellTone: Record<FieldGridProps['tone'], string> = {
    positive: 'border-positive/25 bg-positive-soft',
    critical: 'border-critical/25 bg-critical-soft',
    neutral: 'border-line bg-surface-muted',
};

/** FieldGrid — the shared caption + definition-tile block for change groups. */
function FieldGrid({
    entries,
    caption,
    tone,
    translate,
    strikeThrough = false,
}: FieldGridProps) {
    return (
        <section className="space-y-2.5">
            <p
                className={cn(
                    'text-[11px] font-semibold tracking-[0.1em] uppercase',
                    captionTone[tone],
                )}
            >
                {caption}
            </p>
            <KeyValueGrid columns={2} tile={false}>
                {entries.map(([key, value]) => (
                    <div
                        key={key}
                        className={cn(
                            'rounded-lg border px-3.5 py-3',
                            cellTone[tone],
                        )}
                    >
                        <KeyValue
                            label={formatFieldName(key, translate)}
                            value={
                                <span
                                    className={cn(
                                        'block text-[13px] font-medium',
                                        strikeThrough
                                            ? 'text-ink-muted line-through'
                                            : 'text-ink',
                                    )}
                                >
                                    {formatFriendlyValue(key, value)}
                                </span>
                            }
                        />
                    </div>
                ))}
            </KeyValueGrid>
        </section>
    );
}

/**
 * ActivityChanges — renders Spatie's attribute changes for one activity:
 * created fields, deleted originals, or an old → new diff.
 */
export function ActivityChanges({
    activity,
    translate,
}: {
    activity: {
        activity_type: string;
        metadata?: Record<string, any>;
        attribute_changes?: ActivityAttributeChanges | null;
    };
    translate: Translate;
}) {
    const changes = activity.attribute_changes || {};
    const attributes = changes.attributes || activity.metadata?.attributes;
    const oldValues = changes.old || activity.metadata?.old;
    const type = activity.activity_type.toLowerCase();

    if (!attributes && !oldValues) {
        const customKeys = Object.keys(activity.metadata || {}).filter(
            (key) =>
                key !== 'ip' &&
                key !== 'user_agent' &&
                key !== 'attributes' &&
                key !== 'old',
        );

        if (customKeys.length === 0) {
            return null;
        }

        return (
            <FieldGrid
                caption="Additional Information"
                tone="neutral"
                translate={translate}
                entries={customKeys.map((key) => [
                    key,
                    activity.metadata?.[key],
                ])}
            />
        );
    }

    if (type === 'created' && attributes) {
        return (
            <FieldGrid
                caption="New Record Fields Saved"
                tone="positive"
                translate={translate}
                entries={Object.entries(attributes)}
            />
        );
    }

    if (type === 'deleted' && (oldValues || attributes)) {
        const data = oldValues || attributes || {};

        return (
            <FieldGrid
                caption="Removed Records Original Properties"
                tone="critical"
                translate={translate}
                strikeThrough
                entries={Object.entries(data)}
            />
        );
    }

    const allKeys = Array.from(
        new Set([
            ...Object.keys(attributes || {}),
            ...Object.keys(oldValues || {}),
        ]),
    );

    return (
        <section className="space-y-2.5">
            <p className="text-[11px] font-semibold tracking-[0.1em] text-warning-ink uppercase">
                Details of Modifications
            </p>
            <div className="space-y-2.5">
                {allKeys.map((key) => (
                    <div
                        key={key}
                        className="flex flex-col justify-between gap-2 rounded-lg border border-line bg-surface px-3.5 py-3 sm:flex-row sm:items-center"
                    >
                        <span className="text-[12px] font-semibold text-ink-muted">
                            {formatFieldName(key, translate)}
                        </span>
                        <div className="flex flex-wrap items-center gap-2 text-[12px]">
                            <span className="rounded-md bg-critical-soft px-2.5 py-1 font-medium text-critical-ink line-through">
                                {formatFriendlyValue(
                                    key,
                                    oldValues ? oldValues[key] : undefined,
                                )}
                            </span>
                            <span
                                aria-hidden="true"
                                className="text-ink-subtle"
                            >
                                &rarr;
                            </span>
                            <span className="rounded-md bg-positive-soft px-2.5 py-1 font-semibold text-positive-ink">
                                {formatFriendlyValue(
                                    key,
                                    attributes ? attributes[key] : undefined,
                                )}
                            </span>
                        </div>
                    </div>
                ))}
            </div>
        </section>
    );
}
