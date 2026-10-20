import {
    CheckCircleIcon,
    KeyIcon,
    Squares2X2Icon,
} from '@heroicons/react/24/outline';
import {
    Badge,
    Checkbox,
    Input,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Field,
} from '@/Components/ui';
import { cn } from '@/lib/utils';
import type { FeatureOption } from '@/pages/Platform/components/types';

/**
 * PlanFeatureGrid — the capability matrix of a plan.
 *
 * `featureOptions` is the authoritative list from `App\Enums\PlanFeature`
 * (value + label + description); `value` is the map the backend stores in
 * `feature_flags_json`. Checking a box writes `true` for that key and the
 * controller reduces the payload to the truthy keys, so a flag can finally be
 * turned *off* as well as on.
 */
export function PlanFeatureGrid({
    options,
    value,
    onChange,
    errors,
    disabled = false,
}: {
    options: FeatureOption[];
    value: Record<string, boolean>;
    onChange: (next: Record<string, boolean>) => void;
    errors?: Record<string, string>;
    disabled?: boolean;
}) {
    const enabledCount = options.filter((option) => value[option.value]).length;

    const toggle = (key: string, checked: boolean) => {
        onChange({ ...value, [key]: checked });
    };

    const setAll = (checked: boolean) => {
        const next: Record<string, boolean> = { ...value };

        for (const option of options) {
            next[option.value] = checked;
        }

        onChange(next);
    };

    if (options.length === 0) {
        return (
            <p className="rounded-lg border border-dashed border-line bg-surface-muted px-4 py-6 text-center text-[13px] text-ink-subtle">
                No capability flags are defined by the backend.
            </p>
        );
    }

    return (
        <div className="space-y-3">
            <div className="flex flex-wrap items-center justify-between gap-2">
                <div className="flex items-center gap-2">
                    <Badge tone={enabledCount > 0 ? 'accent' : 'muted'} dot>
                        {enabledCount} of {options.length} enabled
                    </Badge>
                    <span className="text-[12px] text-ink-subtle">
                        These flags gate modules inside the hospital app.
                    </span>
                </div>
                <div className="flex items-center gap-1">
                    <button
                        type="button"
                        disabled={disabled}
                        onClick={() => setAll(true)}
                        className="rounded-md px-2 py-1 text-[12px] font-medium text-ink-muted transition-colors duration-150 hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none disabled:opacity-45"
                    >
                        Enable all
                    </button>
                    <span aria-hidden="true" className="text-ink-subtle">
                        ·
                    </span>
                    <button
                        type="button"
                        disabled={disabled}
                        onClick={() => setAll(false)}
                        className="rounded-md px-2 py-1 text-[12px] font-medium text-ink-muted transition-colors duration-150 hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none disabled:opacity-45"
                    >
                        Clear all
                    </button>
                </div>
            </div>

            <div className="grid gap-x-4 gap-y-2.5 rounded-lg border border-line bg-surface-muted p-3.5 sm:grid-cols-2 xl:grid-cols-3">
                {options.map((option) => {
                    const checked = Boolean(value[option.value]);
                    const hasError = Boolean(errors?.[option.value]);

                    return (
                        <div
                            key={option.value}
                            className={cn(
                                'rounded-md px-2 py-1.5 transition-colors duration-150',
                                checked && 'bg-accent-soft/60',
                                hasError && 'ring-1 ring-critical/40',
                            )}
                        >
                            <Checkbox
                                id={`feature-flag-${option.value}`}
                                name={`feature_flags[${option.value}]`}
                                checked={checked}
                                disabled={disabled}
                                onChange={(event) =>
                                    toggle(option.value, event.target.checked)
                                }
                                label={
                                    <span className="flex items-center gap-1.5">
                                        {option.label}
                                        {checked && (
                                            <CheckCircleIcon className="h-3.5 w-3.5 shrink-0 text-accent" />
                                        )}
                                    </span>
                                }
                                description={option.description}
                            />
                        </div>
                    );
                })}
            </div>

            {errors && Object.keys(errors).length > 0 && (
                <p className="text-[12px] font-medium text-critical-ink">
                    {Object.values(errors).join(' ')}
                </p>
            )}
        </div>
    );
}

export interface QuotaField {
    key: string;
    label: string;
    hint: string;
    unit?: string;
    min?: number;
    max?: number;
    placeholder?: string;
}

/**
 * PlanQuotaTable — the enforced quotas of a plan as an editable table.
 * Empty means "unlimited" for users/patients, 0 means "no storage".
 */
export function PlanQuotaTable({
    fields,
    values,
    onChange,
    errors,
}: {
    fields: QuotaField[];
    values: Record<string, string>;
    onChange: (key: string, value: string) => void;
    errors: Record<string, string>;
}) {
    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHeaderCell>Quota</TableHeaderCell>
                    <TableHeaderCell numeric>Value</TableHeaderCell>
                    <TableHeaderCell>Unit</TableHeaderCell>
                </TableRow>
            </TableHeader>
            <TableBody>
                {fields.map((field) => (
                    <TableRow key={field.key}>
                        <TableCell>
                            <span className="flex items-center gap-1.5 font-medium">
                                <Squares2X2Icon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                                {field.label}
                            </span>
                            <span className="mt-0.5 block text-[11.5px] text-ink-subtle">
                                {field.hint}
                            </span>
                        </TableCell>
                        <TableCell className="w-44">
                            <Field error={errors[field.key]}>
                                <Input
                                    name={field.key}
                                    type="number"
                                    inputMode="numeric"
                                    min={field.min ?? 0}
                                    max={field.max}
                                    value={values[field.key] ?? ''}
                                    placeholder={
                                        field.placeholder ?? 'Unlimited'
                                    }
                                    onChange={(event) =>
                                        onChange(field.key, event.target.value)
                                    }
                                    invalid={Boolean(errors[field.key])}
                                    aria-label={field.label}
                                    className="text-right tabular-nums"
                                />
                            </Field>
                        </TableCell>
                        <TableCell muted className="text-[12px]">
                            {field.unit ?? '—'}
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

/** Small caption used above the quota table. */
export function QuotaCaption() {
    return (
        <p className="flex items-center gap-1.5 text-[12px] text-ink-subtle">
            <KeyIcon className="h-3.5 w-3.5" />
            Leave a quota empty for unlimited. Zero blocks the resource
            entirely.
        </p>
    );
}
