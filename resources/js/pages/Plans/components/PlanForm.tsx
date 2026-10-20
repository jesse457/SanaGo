import {
    ArrowPathIcon,
    CreditCardIcon,
    CurrencyDollarIcon,
    InformationCircleIcon,
    KeyIcon,
    Squares2X2Icon,
    TagIcon,
} from '@heroicons/react/24/outline';
import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Alert,
    Badge,
    Button,
    Checkbox,
    Field,
    FieldSet,
    Input,
    SectionCard,
    Select,
    Textarea,
} from '@/Components/ui';
import {
    PlanFeatureGrid,
    PlanQuotaTable,
    QuotaCaption,
} from '@/pages/Plans/components/PlanFeatureGrid';
import type { QuotaField } from '@/pages/Plans/components/PlanFeatureGrid';
import { PlanHighlightsEditor } from '@/pages/Plans/components/PlanLimitsEditor';
import type { PlanHighlightInput } from '@/pages/Plans/components/PlanLimitsEditor';
import {
    CURRENCY_OPTIONS,
    formatCurrency,
} from '@/pages/Platform/components/format';
import type { FeatureOption } from '@/pages/Platform/components/types';

export interface PlanFormValues {
    name: string;
    slug: string;
    description: string;
    monthly_price: string;
    yearly_price: string;
    currency: string;
    is_active: boolean;
    is_featured: boolean;
    sort_order: number | string;
    max_users_per_tenant: string;
    max_patients_per_tenant: string;
    storage_gb: string;
    trial_days: string;
    grace_days: string;
    api_rate_limit: string;
    features_json: PlanHighlightInput[];
    /** Capability matrix — `feature_flags[key] = true` enables a module. */
    feature_flags: Record<string, boolean>;
}

interface PlanFormProps {
    initialValues: PlanFormValues;
    featureOptions: FeatureOption[];
    /** `post` for create, `put` for update. */
    submit: 'post' | 'put';
    action: string;
    submitLabel: string;
    submittingLabel: string;
    cancelHref: string;
    cancelLabel: string;
    t: (key: string) => string;
    /** Subscriber count, edit screen only. */
    subscribers?: number | null;
}

const QUOTA_FIELDS: QuotaField[] = [
    {
        key: 'max_users_per_tenant',
        label: 'User seats',
        hint: 'Staff accounts the hospital may create.',
        unit: 'users',
        min: 1,
    },
    {
        key: 'max_patients_per_tenant',
        label: 'Patient records',
        hint: 'Patient files held in the tenant database.',
        unit: 'patients',
        min: 1,
    },
    {
        key: 'storage_gb',
        label: 'Storage',
        hint: 'Documents, images and attachments.',
        unit: 'GB',
        min: 0,
    },
    {
        key: 'trial_days',
        label: 'Trial length',
        hint: 'Days before the first invoice when starting on a trial.',
        unit: 'days',
        min: 0,
        max: 365,
    },
    {
        key: 'grace_days',
        label: 'Grace period',
        hint: 'Days of access after the period ends before lockout.',
        unit: 'days',
        min: 0,
        max: 90,
    },
    {
        key: 'api_rate_limit',
        label: 'API rate limit',
        hint: 'Requests per minute per tenant on the API.',
        unit: 'req/min',
        min: 10,
        max: 10000,
    },
];

/**
 * PlanForm — one implementation of the plan editor, shared by create and edit.
 *
 * Fixes the original bug where the flag matrix was never editable: the
 * capability grid is wired to `feature_flags` from `featureOptions`, while the
 * marketing bullets stay in `features_json`.
 */
export function PlanForm({
    initialValues,
    featureOptions,
    submit,
    action,
    submitLabel,
    submittingLabel,
    cancelHref,
    cancelLabel,
    t,
    subscribers,
}: PlanFormProps) {
    const { data, setData, post, put, processing, errors, reset, isDirty } =
        useForm<PlanFormValues>(initialValues);

    const submitForm = (event: FormEvent) => {
        event.preventDefault();

        if (submit === 'post') {
            post(action, { preserveScroll: true });
        } else {
            put(action, { preserveScroll: true });
        }
    };

    const currency = data.currency || 'XAF';

    const monthly = Number(data.monthly_price || 0);
    const yearly = Number(data.yearly_price || 0);
    const yearlyDiscount =
        monthly > 0 && yearly > 0
            ? Math.round(((monthly * 12 - yearly) / (monthly * 12)) * 100)
            : null;

    const quotaErrors = Object.fromEntries(
        QUOTA_FIELDS.map((field) => [
            field.key,
            (errors as Record<string, string>)[field.key],
        ]).filter(([, value]) => Boolean(value)) as [string, string][],
    );

    return (
        <form onSubmit={submitForm} className="space-y-5">
            {Object.keys(errors).length > 0 && (
                <Alert tone="critical" title="Some fields need attention">
                    Check the highlighted fields and submit again.
                </Alert>
            )}

            {/* ── Identity ── */}
            <SectionCard
                title="Plan identity"
                description="How the plan is named and described in the catalogue."
                icon={CreditCardIcon}
                actions={
                    subscribers !== null && subscribers !== undefined ? (
                        <Badge tone="muted">
                            {subscribers} subscriber
                            {subscribers === 1 ? '' : 's'}
                        </Badge>
                    ) : null
                }
            >
                <FieldSet columns={2} className="gap-y-4">
                    <Field
                        label={t('planName')}
                        htmlFor="name"
                        required
                        error={errors.name}
                    >
                        <Input
                            id="name"
                            name="name"
                            value={data.name}
                            placeholder="Professional"
                            onChange={(event) =>
                                setData('name', event.target.value)
                            }
                            invalid={Boolean(errors.name)}
                        />
                    </Field>

                    <Field
                        label={t('slug')}
                        htmlFor="slug"
                        required
                        error={errors.slug}
                        hint="Lowercase, dashes only. Seeds the default feature set."
                    >
                        <Input
                            id="slug"
                            name="slug"
                            icon={TagIcon}
                            value={data.slug}
                            placeholder="professional"
                            onChange={(event) =>
                                setData('slug', event.target.value)
                            }
                            invalid={Boolean(errors.slug)}
                            spellCheck={false}
                        />
                    </Field>

                    <Field
                        label={t('description')}
                        htmlFor="description"
                        error={errors.description}
                        className="sm:col-span-2"
                    >
                        <Textarea
                            id="description"
                            name="description"
                            rows={2}
                            value={data.description}
                            placeholder="For growing hospitals that need diagnostics and insurance billing."
                            onChange={(event) =>
                                setData('description', event.target.value)
                            }
                            invalid={Boolean(errors.description)}
                        />
                    </Field>

                    <Field
                        label="Sort order"
                        htmlFor="sort_order"
                        error={errors.sort_order}
                        hint="Lower numbers appear first in the catalogue and pickers."
                    >
                        <Input
                            id="sort_order"
                            name="sort_order"
                            type="number"
                            min="0"
                            value={data.sort_order}
                            onChange={(event) =>
                                setData('sort_order', event.target.value)
                            }
                            invalid={Boolean(errors.sort_order)}
                            className="tabular-nums"
                        />
                    </Field>

                    <div className="flex flex-col justify-end gap-2.5 pb-1">
                        <Checkbox
                            name="is_active"
                            checked={data.is_active}
                            onChange={(event) =>
                                setData('is_active', event.target.checked)
                            }
                            label="Available for new subscriptions"
                            description="Inactive plans stay on existing tenants but cannot be assigned."
                        />
                        <Checkbox
                            name="is_featured"
                            checked={data.is_featured}
                            onChange={(event) =>
                                setData('is_featured', event.target.checked)
                            }
                            label="Featured plan"
                            description="Badged as the recommended tier on the pricing page."
                        />
                    </div>
                </FieldSet>
            </SectionCard>

            {/* ── Pricing ── */}
            <SectionCard
                title="Pricing"
                description="Currency drives every billing display across the platform."
                icon={CurrencyDollarIcon}
            >
                <FieldSet columns={3} className="gap-y-4">
                    <Field
                        label="Currency"
                        htmlFor="currency"
                        error={errors.currency}
                    >
                        <Select
                            id="currency"
                            name="currency"
                            value={currency}
                            onChange={(event) =>
                                setData('currency', event.target.value)
                            }
                            invalid={Boolean(errors.currency)}
                        >
                            {CURRENCY_OPTIONS.map((option) => (
                                <option key={option.value} value={option.value}>
                                    {option.label}
                                </option>
                            ))}
                        </Select>
                    </Field>

                    <Field
                        label={t('monthlyPrice')}
                        htmlFor="monthly_price"
                        error={errors.monthly_price}
                        hint={
                            monthly > 0
                                ? formatCurrency(monthly, currency)
                                : 'Empty or 0 means no monthly option.'
                        }
                    >
                        <Input
                            id="monthly_price"
                            name="monthly_price"
                            type="number"
                            step="1"
                            min="0"
                            value={data.monthly_price}
                            placeholder="0"
                            onChange={(event) =>
                                setData('monthly_price', event.target.value)
                            }
                            invalid={Boolean(errors.monthly_price)}
                            className="tabular-nums"
                        />
                    </Field>

                    <Field
                        label={t('yearlyPrice')}
                        htmlFor="yearly_price"
                        error={errors.yearly_price}
                        hint={
                            yearly > 0
                                ? yearlyDiscount !== null && yearlyDiscount > 0
                                    ? `${formatCurrency(yearly, currency)} — ${yearlyDiscount}% off monthly`
                                    : formatCurrency(yearly, currency)
                                : 'Empty or 0 means no yearly option.'
                        }
                    >
                        <Input
                            id="yearly_price"
                            name="yearly_price"
                            type="number"
                            step="1"
                            min="0"
                            value={data.yearly_price}
                            placeholder="0"
                            onChange={(event) =>
                                setData('yearly_price', event.target.value)
                            }
                            invalid={Boolean(errors.yearly_price)}
                            className="tabular-nums"
                        />
                    </Field>
                </FieldSet>

                {monthly > 0 && yearly > 0 && yearly >= monthly * 12 && (
                    <Alert
                        tone="warning"
                        title="Yearly price is not a discount"
                        className="mt-4"
                    >
                        The yearly price is at or above twelve monthly payments
                        ({formatCurrency(monthly * 12, currency)}). Customers
                        have no reason to commit for a year.
                    </Alert>
                )}
            </SectionCard>

            {/* ── Quotas ── */}
            <SectionCard
                title="Quotas"
                description="Enforced per tenant when the plan is assigned."
                icon={Squares2X2Icon}
            >
                <div className="space-y-3">
                    <PlanQuotaTable
                        fields={QUOTA_FIELDS}
                        values={{
                            max_users_per_tenant: data.max_users_per_tenant,
                            max_patients_per_tenant:
                                data.max_patients_per_tenant,
                            storage_gb: data.storage_gb,
                            trial_days: data.trial_days,
                            grace_days: data.grace_days,
                            api_rate_limit: data.api_rate_limit,
                        }}
                        onChange={(key, value) =>
                            setData(key as keyof PlanFormValues, value as never)
                        }
                        errors={quotaErrors}
                    />
                    <QuotaCaption />
                </div>
            </SectionCard>

            {/* ── Capability flags ── */}
            <SectionCard
                title="Capabilities"
                description="Which modules this plan unlocks inside the hospital app."
                icon={KeyIcon}
                actions={
                    <Badge tone="accent" variant="outline">
                        feature_flags
                    </Badge>
                }
            >
                <PlanFeatureGrid
                    options={featureOptions}
                    value={data.feature_flags}
                    onChange={(next) => setData('feature_flags', next)}
                />
            </SectionCard>

            {/* ── Marketing bullets ── */}
            <SectionCard
                title="Marketing highlights"
                description="The readable bullet list shown on plan cards — separate from the enforced capabilities above."
                icon={InformationCircleIcon}
            >
                <PlanHighlightsEditor
                    t={t}
                    features={data.features_json}
                    onFeatureChange={(features) =>
                        setData('features_json', features)
                    }
                    errors={errors as unknown as Record<string, string>}
                    addLabel={t('addFeature')}
                    emptyMessage={t('noFeaturesAdded')}
                    featureLabelPlaceholder={t('featureLabelPlaceholder')}
                    highlightLabel={t('highlight')}
                />
            </SectionCard>

            <div className="flex flex-wrap items-center justify-between gap-2 border-t border-line pt-4">
                <p className="text-[12px] text-ink-subtle">
                    {submit === 'post'
                        ? 'New plans are immediately available if marked active.'
                        : 'Changes apply to new invoices; existing subscriptions keep their agreed price.'}
                </p>
                <div className="flex items-center gap-2">
                    {submit === 'post' && (
                        <Button
                            type="button"
                            variant="ghost"
                            icon={ArrowPathIcon}
                            disabled={!isDirty || processing}
                            onClick={() => reset()}
                        >
                            {t('reset')}
                        </Button>
                    )}
                    <Button variant="secondary" href={cancelHref}>
                        {cancelLabel}
                    </Button>
                    <Button
                        type="submit"
                        loading={processing}
                        disabled={processing}
                    >
                        {processing ? submittingLabel : submitLabel}
                    </Button>
                </div>
            </div>
        </form>
    );
}
