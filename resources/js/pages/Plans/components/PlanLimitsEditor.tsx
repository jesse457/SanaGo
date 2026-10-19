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

export interface PlanLimitInput {
    feature_label: string;
    is_highlighted: boolean;
}

interface PlanLimitsEditorProps {
    /** Row id (limit key) used by the caller to read/write the value. */
    limits: { id: string; label: string; unit: string }[];
    values: Record<string, string | number>;
    onChange: (id: string, value: string) => void;
    errors: Record<string, string>;
    /** Highlighted feature rows ("limits") for the plan's feature list. */
    features: PlanLimitInput[];
    onFeatureChange: (features: PlanLimitInput[]) => void;
    addLabel: string;
    removeLabel?: string;
    emptyMessage: string;
    featureLabelPlaceholder: string;
    highlightLabel: string;
    t: (key: string) => string;
}

/**
 * PlanLimitsEditor — the quota matrix shared by the plan create and edit
 * forms. Renders each limit as a table row with an editable value cell, and
 * manages the plan's highlighted feature list underneath.
 */
export function PlanLimitsEditor({
    limits,
    values,
    onChange,
    errors,
    features,
    onFeatureChange,
    addLabel,
    removeLabel = 'Remove',
    emptyMessage,
    featureLabelPlaceholder,
    highlightLabel,
    t,
}: PlanLimitsEditorProps) {
    const updateFeature = (index: number, patch: Partial<PlanLimitInput>) => {
        onFeatureChange(
            features.map((feature, i) =>
                i === index ? { ...feature, ...patch } : feature,
            ),
        );
    };

    return (
        <div className="space-y-6">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHeaderCell>{t('feature')}</TableHeaderCell>
                        <TableHeaderCell numeric>{t('value')}</TableHeaderCell>
                        <TableHeaderCell>{t('unit')}</TableHeaderCell>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {limits.map((limit) => (
                        <TableRow key={limit.id}>
                            <TableCell>
                                <span className="font-medium">
                                    {limit.label}
                                </span>
                            </TableCell>
                            <TableCell className="w-40">
                                <Input
                                    name={limit.id}
                                    type="number"
                                    inputMode="numeric"
                                    min="0"
                                    value={values[limit.id] ?? ''}
                                    onChange={(event) =>
                                        onChange(limit.id, event.target.value)
                                    }
                                    invalid={Boolean(errors[limit.id])}
                                    aria-label={`${limit.label} ${t('value')}`}
                                    className="text-right tabular-nums"
                                />
                                {errors[limit.id] && (
                                    <p className="mt-1 text-right text-[12px] font-medium text-critical-ink">
                                        {errors[limit.id]}
                                    </p>
                                )}
                            </TableCell>
                            <TableCell muted className="text-[12px]">
                                {limit.unit}
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>

            <div className="space-y-3">
                <div className="flex items-center justify-between gap-3">
                    <p className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                        {t('features')}
                    </p>
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
                                <TableHeaderCell>
                                    {t('feature')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {highlightLabel}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    <span className="sr-only">
                                        {removeLabel}
                                    </span>
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
        </div>
    );
}
