import {
    ArrowPathIcon,
    CreditCardIcon,
    CurrencyDollarIcon,
    TagIcon,
} from '@heroicons/react/24/outline';
import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Alert,
    Button,
    Field,
    FieldSet,
    Input,
    SectionCard,
    Textarea,
} from '@/Components/ui';
import { PlanLimitsEditor } from '@/pages/Plans/components/PlanLimitsEditor';
import type { PlanLimitInput } from '@/pages/Plans/components/PlanLimitsEditor';

export interface PlanFormValues {
    name: string;
    slug: string;
    description: string;
    monthly_price: string;
    yearly_price: string;
    is_active: boolean;
    sort_order: number;
    max_users_per_tenant: string | number;
    storage_gb: string | number;
    features_json: PlanLimitInput[];
}

interface PlanFormProps {
    initialValues: PlanFormValues;
    /** `post` for create, `put` for update. */
    submit: 'post' | 'put';
    action: string;
    submitLabel: string;
    submittingLabel: string;
    cancelHref: string;
    cancelLabel: string;
    t: (key: string) => string;
    onSuccess?: () => void;
    successMessage?: string | null;
}

/**
 * PlanForm — one implementation of the plan form, used by both the create and
 * the edit screens so every payload field and validation rule stays identical.
 */
export function PlanForm({
    initialValues,
    submit,
    action,
    submitLabel,
    submittingLabel,
    cancelHref,
    cancelLabel,
    t,
    onSuccess,
    successMessage,
}: PlanFormProps) {
    const { data, setData, post, put, processing, errors, reset } =
        useForm<PlanFormValues>(initialValues);

    const submitForm = (event: FormEvent) => {
        event.preventDefault();

        const options = onSuccess ? { onSuccess } : {};

        if (submit === 'post') {
            post(action, options);
        } else {
            put(action, options);
        }
    };

    return (
        <form onSubmit={submitForm} className="space-y-6">
            {successMessage && (
                <Alert tone="positive" title={t('success')}>
                    {successMessage}
                </Alert>
            )}

            <SectionCard
                title={t('planIdentity')}
                description={t('defineNewSubscriptionPlan')}
                icon={CreditCardIcon}
            >
                <FieldSet columns={2} className="gap-y-4">
                    <Field
                        label={t('planName')}
                        htmlFor="name"
                        required
                        error={errors.name}
                        className="sm:col-span-2"
                    >
                        <Input
                            id="name"
                            name="name"
                            icon={CreditCardIcon}
                            value={data.name}
                            placeholder={t('planNamePlaceholder')}
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
                        className="sm:col-span-2"
                    >
                        <Input
                            id="slug"
                            name="slug"
                            icon={TagIcon}
                            value={data.slug}
                            placeholder={t('slugPlaceholder')}
                            onChange={(event) =>
                                setData('slug', event.target.value)
                            }
                            invalid={Boolean(errors.slug)}
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
                            placeholder={t('descriptionPlaceholder')}
                            onChange={(event) =>
                                setData('description', event.target.value)
                            }
                            invalid={Boolean(errors.description)}
                        />
                    </Field>
                </FieldSet>
            </SectionCard>

            <SectionCard
                title={t('pricingAndLimits')}
                icon={CurrencyDollarIcon}
            >
                <FieldSet columns={2} className="gap-y-4">
                    <Field
                        label={t('monthlyPrice')}
                        htmlFor="monthly_price"
                        error={errors.monthly_price}
                    >
                        <Input
                            id="monthly_price"
                            name="monthly_price"
                            type="number"
                            step="0.01"
                            min="0"
                            icon={CurrencyDollarIcon}
                            value={data.monthly_price}
                            placeholder={t('monthlyPricePlaceholder')}
                            onChange={(event) =>
                                setData('monthly_price', event.target.value)
                            }
                            invalid={Boolean(errors.monthly_price)}
                        />
                    </Field>

                    <Field
                        label={t('yearlyPrice')}
                        htmlFor="yearly_price"
                        error={errors.yearly_price}
                    >
                        <Input
                            id="yearly_price"
                            name="yearly_price"
                            type="number"
                            step="0.01"
                            min="0"
                            icon={CurrencyDollarIcon}
                            value={data.yearly_price}
                            placeholder={t('yearlyPricePlaceholder')}
                            onChange={(event) =>
                                setData('yearly_price', event.target.value)
                            }
                            invalid={Boolean(errors.yearly_price)}
                        />
                    </Field>

                    <Field
                        label={t('active')}
                        htmlFor="is_active"
                        className="sm:col-span-2"
                    >
                        <label
                            htmlFor="is_active"
                            className="flex cursor-pointer items-center gap-2.5 text-[13px] font-medium text-ink"
                        >
                            <input
                                id="is_active"
                                name="is_active"
                                type="checkbox"
                                checked={data.is_active}
                                onChange={(event) =>
                                    setData('is_active', event.target.checked)
                                }
                                className="h-4 w-4 cursor-pointer rounded-xs border border-line-strong bg-surface accent-accent focus-visible:ring-2 focus-visible:ring-accent/40"
                            />
                            {t('active')}
                        </label>
                    </Field>
                </FieldSet>

                <div className="mt-6">
                    <PlanLimitsEditor
                        t={t}
                        values={{
                            max_users_per_tenant: data.max_users_per_tenant,
                            storage_gb: data.storage_gb,
                        }}
                        limits={[
                            {
                                id: 'max_users_per_tenant',
                                label: t('maxUsersPerTenant'),
                                unit: t('users'),
                            },
                            {
                                id: 'storage_gb',
                                label: t('storageGb'),
                                unit: 'GB',
                            },
                        ]}
                        onChange={(id, value) =>
                            setData(id as 'max_users_per_tenant', value)
                        }
                        errors={errors as unknown as Record<string, string>}
                        features={data.features_json}
                        onFeatureChange={(features) =>
                            setData('features_json', features)
                        }
                        addLabel={t('addFeature')}
                        emptyMessage={t('noFeaturesAdded')}
                        featureLabelPlaceholder={t('featureLabelPlaceholder')}
                        highlightLabel={t('highlight')}
                    />
                </div>
            </SectionCard>

            {Object.keys(errors).length > 0 && (
                <Alert tone="critical" title={t('error')}>
                    {t('someFieldsRequireAttention')}
                </Alert>
            )}

            <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                {submit === 'post' && (
                    <Button
                        type="button"
                        variant="ghost"
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
                    variant="primary"
                    loading={processing}
                    icon={processing ? ArrowPathIcon : undefined}
                >
                    {processing ? submittingLabel : submitLabel}
                </Button>
            </div>
        </form>
    );
}
