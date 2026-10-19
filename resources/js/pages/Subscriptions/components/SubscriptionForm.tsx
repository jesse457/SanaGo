import {
    BuildingOfficeIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    CurrencyDollarIcon,
    DocumentTextIcon,
} from '@heroicons/react/24/outline';
import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Alert,
    Badge,
    Button,
    Field,
    FieldSet,
    Input,
    SectionCard,
    Select,
} from '@/Components/ui';

export interface SubscriptionFormValues {
    tenant_id: string;
    plan_name: string;
    billing_cycle: string;
    price: string;
    start_date: string;
    end_date: string;
    status: string;
}

export interface PlanOption {
    id: number;
    name: string;
    monthly_price: string | null;
    yearly_price: string | null;
    features_json: { feature_label: string; is_highlighted: boolean }[];
}

export interface TenantOption {
    id: string;
    name: string;
}

interface SubscriptionFormProps {
    t: (key: string) => string;
    initialValues: SubscriptionFormValues;
    submit: 'post' | 'put';
    action: string;
    submitLabel: string;
    submittingLabel: string;
    cancelHref: string;
    cancelLabel: string;
    tenants: TenantOption[];
    /** Only the create flow picks a plan package; edit works on the record. */
    plans?: PlanOption[];
    onPlanSelect?: (planId: string) => void;
    /** Controlled plan id for the create flow's plan picker. */
    planId?: string;
    onSuccess?: () => void;
    successMessage?: string | null;
}

/**
 * SubscriptionForm — the single subscription form shared by create and edit:
 * tenant, plan metadata, term dates, price and status in one payload.
 */
export function SubscriptionForm({
    t,
    initialValues,
    submit,
    action,
    submitLabel,
    submittingLabel,
    cancelHref,
    cancelLabel,
    tenants,
    plans,
    onPlanSelect,
    planId,
    onSuccess,
    successMessage,
}: SubscriptionFormProps) {
    const { data, setData, post, put, processing, errors } =
        useForm<SubscriptionFormValues>(initialValues);

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

            <SectionCard title={t('tenantAndPlan')} icon={BuildingOfficeIcon}>
                <FieldSet columns={2}>
                    <Field
                        label={t('tenant')}
                        htmlFor="tenant_id"
                        required
                        error={errors.tenant_id}
                    >
                        <Select
                            id="tenant_id"
                            name="tenant_id"
                            value={data.tenant_id}
                            onChange={(event) =>
                                setData('tenant_id', event.target.value)
                            }
                            invalid={Boolean(errors.tenant_id)}
                        >
                            <option value="">{t('selectTenant')}</option>
                            {tenants.map((tenant) => (
                                <option key={tenant.id} value={tenant.id}>
                                    {tenant.name}
                                </option>
                            ))}
                        </Select>
                    </Field>

                    {plans && (
                        <Field label={t('plan')} htmlFor="plan_id">
                            <Select
                                id="plan_id"
                                name="plan_id"
                                value={planId ?? ''}
                                onChange={(event) =>
                                    onPlanSelect?.(event.target.value)
                                }
                            >
                                <option value="">{t('customManual')}</option>
                                {plans.map((plan) => (
                                    <option key={plan.id} value={plan.id}>
                                        {plan.name}
                                    </option>
                                ))}
                            </Select>
                        </Field>
                    )}
                </FieldSet>
            </SectionCard>

            <SectionCard title={t('billingDetails')} icon={CurrencyDollarIcon}>
                <FieldSet columns={2} className="gap-y-4">
                    <Field
                        label={t('planName')}
                        htmlFor="plan_name"
                        error={errors.plan_name}
                    >
                        <Input
                            id="plan_name"
                            name="plan_name"
                            icon={DocumentTextIcon}
                            value={data.plan_name}
                            placeholder={t('planName')}
                            onChange={(event) =>
                                setData('plan_name', event.target.value)
                            }
                            invalid={Boolean(errors.plan_name)}
                        />
                    </Field>

                    <Field
                        label={t('billingCycle')}
                        htmlFor="billing_cycle"
                        error={errors.billing_cycle}
                    >
                        <Select
                            id="billing_cycle"
                            name="billing_cycle"
                            value={data.billing_cycle}
                            onChange={(event) =>
                                setData('billing_cycle', event.target.value)
                            }
                        >
                            <option value="monthly">{t('monthly')}</option>
                            <option value="yearly">{t('yearly')}</option>
                        </Select>
                    </Field>

                    <Field
                        label={`${t('price')} ($)`}
                        htmlFor="price"
                        required
                        error={errors.price}
                    >
                        <Input
                            id="price"
                            name="price"
                            type="number"
                            step="0.01"
                            min="0"
                            icon={CurrencyDollarIcon}
                            value={data.price}
                            placeholder="0.00"
                            onChange={(event) =>
                                setData('price', event.target.value)
                            }
                            invalid={Boolean(errors.price)}
                        />
                    </Field>

                    <Field
                        label={t('status')}
                        htmlFor="status"
                        error={errors.status}
                    >
                        <Select
                            id="status"
                            name="status"
                            value={data.status}
                            onChange={(event) =>
                                setData('status', event.target.value)
                            }
                        >
                            <option value="active">{t('active')}</option>
                            <option value="inactive">{t('inactive')}</option>
                            <option value="cancelled">{t('cancelled')}</option>
                        </Select>
                    </Field>

                    <Field
                        label={t('startDate')}
                        htmlFor="start_date"
                        required
                        error={errors.start_date}
                    >
                        <Input
                            id="start_date"
                            name="start_date"
                            type="date"
                            icon={CalendarDaysIcon}
                            value={data.start_date}
                            onChange={(event) =>
                                setData('start_date', event.target.value)
                            }
                            invalid={Boolean(errors.start_date)}
                        />
                    </Field>

                    <Field
                        label={t('endDate')}
                        htmlFor="end_date"
                        error={errors.end_date}
                    >
                        <Input
                            id="end_date"
                            name="end_date"
                            type="date"
                            icon={CalendarDaysIcon}
                            value={data.end_date}
                            onChange={(event) =>
                                setData('end_date', event.target.value)
                            }
                            invalid={Boolean(errors.end_date)}
                        />
                    </Field>
                </FieldSet>
            </SectionCard>

            <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                <Button variant="secondary" href={cancelHref}>
                    {cancelLabel}
                </Button>
                <Button type="submit" loading={processing}>
                    {processing ? submittingLabel : submitLabel}
                </Button>
            </div>
        </form>
    );
}

/**
 * PlanFeatureList — the highlighted features of the picked plan, shown under
 * the plan picker so the operator sees what the tenant is buying.
 */
export function PlanFeatureList({
    plan,
    label,
}: {
    plan: PlanOption;
    label: string;
}) {
    const highlights = plan.features_json.filter((f) => f.is_highlighted);

    if (highlights.length === 0) {
        return null;
    }

    return (
        <div className="mt-4 rounded-lg border border-accent/25 bg-accent-soft px-4 py-3.5">
            <p className="mb-2 flex items-center gap-1.5 text-[11px] font-semibold tracking-[0.1em] text-accent-ink uppercase">
                <CheckCircleIcon className="h-3.5 w-3.5" />
                {plan.name} {label}
            </p>
            <div className="flex flex-wrap gap-1.5">
                {highlights.map((feature, index) => (
                    <Badge key={index} tone="accent" variant="outline">
                        {feature.feature_label}
                    </Badge>
                ))}
            </div>
        </div>
    );
}
