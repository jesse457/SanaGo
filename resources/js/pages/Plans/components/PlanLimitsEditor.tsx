import { PlusIcon, TrashIcon } from '@heroicons/react/24/outline';
import {
    Button,
    Field,
    Input,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';

export interface PlanHighlightInput {
    feature_label: string;
    is_highlighted: boolean;
}

/** @deprecated kept as an alias so older imports keep compiling. */
export type PlanLimitInput = PlanHighlightInput;

interface PlanHighlightsEditorProps {
    /** Highlighted marketing bullets shown on the plan card and pricing page. */
    features: PlanHighlightInput[];
    onFeatureChange: (features: PlanHighlightInput[]) => void;
    errors: Record<string, string>;
    addLabel: string;
    emptyMessage: string;
    featureLabelPlaceholder: string;
    highlightLabel: string;
    removeLabel?: string;
    t: (key: string) => string;
}

/**
 * PlanHighlightsEditor — the marketing bullet list of a plan (the
 * `features_json` column: a label plus a "highlight" flag).
 *
 * This is deliberately *not* the capability matrix: the enforced flags live in
 * `feature_flags_json` and are edited by `PlanFeatureGrid`. Mixing the two was
 * the original bug — flags could never be edited because the limit editor had
 * no `features`/`onFeatureChange` wiring.
 */
export function PlanHighlightsEditor({
    features,
    onFeatureChange,
    errors,
    addLabel,
    emptyMessage,
    featureLabelPlaceholder,
    highlightLabel,
    removeLabel = 'Remove',
    t,
}: PlanHighlightsEditorProps) {
    const updateFeature = (
        index: number,
        patch: Partial<PlanHighlightInput>,
    ) => {
        onFeatureChange(
            features.map((feature, i) =>
                i === index ? { ...feature, ...patch } : feature,
            ),
        );
    };

    return (
        <div className="space-y-3">
            <div className="flex items-center justify-between gap-3">
                <div>
                    <p className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                        {t('features')}
                    </p>
                    <p className="mt-0.5 text-[12px] text-ink-subtle">
                        Marketing bullets shown on the plan card. Highlighted
                        ones are emphasised on the pricing page.
                    </p>
                </div>
                <Button
                    type="button"
                    variant="secondary"
                    size="sm"
                    icon={PlusIcon}
                    onClick={() =>
                        onFeatureChange([
                            ...features,
                            { feature_label: '', is_highlighted: false },
                        ])
                    }
                >
                    {addLabel}
                </Button>
            </div>

            {features.length === 0 ? (
                <p className="rounded-lg border border-dashed border-line bg-surface-muted px-4 py-6 text-center text-[13px] text-ink-subtle">
                    {emptyMessage}
                </p>
            ) : (
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHeaderCell>{t('feature')}</TableHeaderCell>
                            <TableHeaderCell>{highlightLabel}</TableHeaderCell>
                            <TableHeaderCell>
                                <span className="sr-only">{removeLabel}</span>
                            </TableHeaderCell>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {features.map((feature, index) => (
                            <TableRow key={index}>
                                <TableCell>
                                    <Field
                                        error={
                                            errors[
                                                `features_json.${index}.feature_label`
                                            ]
                                        }
                                    >
                                        <Input
                                            name={`features_json.${index}.feature_label`}
                                            value={feature.feature_label}
                                            placeholder={
                                                featureLabelPlaceholder
                                            }
                                            onChange={(event) =>
                                                updateFeature(index, {
                                                    feature_label:
                                                        event.target.value,
                                                })
                                            }
                                            invalid={Boolean(
                                                errors[
                                                    `features_json.${index}.feature_label`
                                                ],
                                            )}
                                        />
                                    </Field>
                                </TableCell>
                                <TableCell>
                                    <input
                                        type="checkbox"
                                        checked={feature.is_highlighted}
                                        onChange={(event) =>
                                            updateFeature(index, {
                                                is_highlighted:
                                                    event.target.checked,
                                            })
                                        }
                                        aria-label={`${highlightLabel} ${feature.feature_label || index + 1}`}
                                        className="h-4 w-4 cursor-pointer rounded-xs border border-line-strong bg-surface accent-accent focus-visible:ring-2 focus-visible:ring-accent/40"
                                    />
                                </TableCell>
                                <TableCell className="text-right">
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        icon={TrashIcon}
                                        aria-label={`${removeLabel} ${feature.feature_label || index + 1}`}
                                        onClick={() =>
                                            onFeatureChange(
                                                features.filter(
                                                    (_, i) => i !== index,
                                                ),
                                            )
                                        }
                                    />
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            )}
        </div>
    );
}
