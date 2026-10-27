import {
    BeakerIcon,
    BuildingOfficeIcon,
    CheckIcon,
    CreditCardIcon,
    GlobeAltIcon,
    KeyIcon,
    TagIcon,
    UserCircleIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useMemo, useState } from 'react';
import {
    Alert,
    Badge,
    Button,
    Checkbox,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SectionCard,
    Select,
} from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { formatCurrency } from '@/pages/Platform/components/format';
import type {
    PlanOption,
    TenantStorePayload,
} from '@/pages/Platform/components/types';

interface TenantsCreateProps {
    plans: PlanOption[];
    defaultTrialDays: number;
}

const SLUG_PATTERN = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;

/** URL-safe slug from a hospital name (`Hôpital Central` → `hopital-central`). */
function slugify(value: string): string {
    return value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .slice(0, 64);
}

/**
 * Tenants/Create — provisioning a hospital.
 *
 * The controller sends the sellable plans and the platform trial default. Only
 * `slug`, `name` and `domain` are validated by `StoreTenantRequest` today; the
 * rest (`plan_id`, `billing_cycle`, `trial`, `demo_data`, admin credentials)
 * is what `TenantController::store()` reads through `validated()`, so it is
 * submitted for the backend to adopt without a frontend change.
 */
export default function TenantsCreate({
    plans,
    defaultTrialDays,
}: TenantsCreateProps) {
    const { data, setData, post, processing, errors } =
        useForm<TenantStorePayload>({
            name: '',
            slug: '',
            domain: '',
            admin_name: '',
            admin_email: '',
            admin_password: '',
            plan_id: '',
            billing_cycle: 'monthly',
            trial: true,
            demo_data: false,
        });

    const [slugTouched, setSlugTouched] = useState(false);

    const selectedPlan = useMemo(
        () => plans.find((plan) => String(plan.id) === data.plan_id) ?? null,
        [plans, data.plan_id],
    );

    const planPrice = selectedPlan
        ? data.billing_cycle === 'yearly'
            ? selectedPlan.yearly_price
            : selectedPlan.monthly_price
        : null;

    const trialDays = selectedPlan?.trial_days ?? defaultTrialDays;

    const handleName = (value: string) => {
        if (slugTouched) {
            setData('name', value);

            return;
        }

        const slug = slugify(value);

        setData((previous) => ({
            ...previous,
            name: value,
            slug,
            // Keep the conventionally-derived domain in step, but never
            // overwrite a domain the operator typed themselves.
            domain:
                previous.domain === '' ||
                previous.domain === `${previous.slug}.sanago.cm`
                    ? slug
                        ? `${slug}.sanago.cm`
                        : ''
                    : previous.domain,
        }));
    };

    const slugLooksValid = data.slug === '' || SLUG_PATTERN.test(data.slug);

    const submit = (event: FormEvent) => {
        event.preventDefault();
        post('/tenants', { preserveScroll: true });
    };

    const hasErrors = Object.keys(errors).length > 0;

    return (
        <SuperAdminLayout
            headerTitle="New hospital"
            headerSubtitle="Provision a tenant on the platform"
        >
            <Head title="New hospital" />

            <div className="space-y-5 pb-12">
                <PageHeader
                    title="New hospital"
                    description="Provision a hospital: its slug and domain route all of its traffic, and the administrator account is created inside its own database."
                    breadcrumbs={[
                        { label: 'Hospitals', href: '/tenants' },
                        { label: 'New hospital' },
                    ]}
                />

                {hasErrors && (
                    <Alert tone="critical" title="Some fields need attention">
                        Check the highlighted fields below and submit again.
                    </Alert>
                )}

                <form onSubmit={submit} className="space-y-5">
                    <SectionCard
                        title="Hospital identity"
                        description="The slug becomes the tenant key; the domain routes every request."
                        icon={BuildingOfficeIcon}
                    >
                        <FieldSet columns={2}>
                            <Field
                                label="Hospital name"
                                htmlFor="name"
                                required
                                error={errors.name}
                                className="sm:col-span-2"
                            >
                                <Input
                                    id="name"
                                    name="name"
                                    value={data.name}
                                    placeholder="Hôpital Central de Yaoundé"
                                    onChange={(event) =>
                                        handleName(event.target.value)
                                    }
                                    invalid={Boolean(errors.name)}
                                    autoComplete="off"
                                    autoFocus
                                />
                            </Field>

                            <Field
                                label="Slug"
                                htmlFor="slug"
                                required
                                error={
                                    errors.slug ??
                                    (slugLooksValid
                                        ? null
                                        : 'Lowercase letters, numbers and single dashes only.')
                                }
                                hint="Used as the tenant key in the database. Cannot be changed later."
                            >
                                <Input
                                    id="slug"
                                    name="slug"
                                    icon={TagIcon}
                                    value={data.slug}
                                    placeholder="hopital-central-yaounde"
                                    onChange={(event) => {
                                        setSlugTouched(true);
                                        setData('slug', event.target.value);
                                    }}
                                    invalid={
                                        Boolean(errors.slug) || !slugLooksValid
                                    }
                                    autoComplete="off"
                                    spellCheck={false}
                                />
                            </Field>

                            <Field
                                label="Domain"
                                htmlFor="domain"
                                required
                                error={errors.domain}
                                hint="Full host, no protocol. Must be unique across the platform."
                            >
                                <Input
                                    id="domain"
                                    name="domain"
                                    icon={GlobeAltIcon}
                                    value={data.domain}
                                    placeholder="hopital-central.sanago.cm"
                                    onChange={(event) =>
                                        setData('domain', event.target.value)
                                    }
                                    invalid={Boolean(errors.domain)}
                                    autoComplete="off"
                                    spellCheck={false}
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    <SectionCard
                        title="Administrator account"
                        description="Created inside the hospital's own database with the admin role."
                        icon={UserCircleIcon}
                    >
                        <FieldSet columns={2}>
                            <Field
                                label="Administrator name"
                                htmlFor="admin_name"
                                error={errors.admin_name}
                            >
                                <Input
                                    id="admin_name"
                                    name="admin_name"
                                    icon={UserCircleIcon}
                                    value={data.admin_name}
                                    placeholder="Dr. Amina Ngo"
                                    onChange={(event) =>
                                        setData(
                                            'admin_name',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.admin_name)}
                                    autoComplete="off"
                                />
                            </Field>

                            <Field
                                label="Administrator email"
                                htmlFor="admin_email"
                                error={errors.admin_email}
                                hint="This becomes their login."
                            >
                                <Input
                                    id="admin_email"
                                    name="admin_email"
                                    type="email"
                                    value={data.admin_email}
                                    placeholder="admin@hopital-central.cm"
                                    onChange={(event) =>
                                        setData(
                                            'admin_email',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.admin_email)}
                                    autoComplete="off"
                                    spellCheck={false}
                                />
                            </Field>

                            <Field
                                label="Temporary password"
                                htmlFor="admin_password"
                                error={errors.admin_password}
                                hint="Share it securely — the administrator should change it after first sign-in."
                                className="sm:col-span-2"
                            >
                                <Input
                                    id="admin_password"
                                    name="admin_password"
                                    type="password"
                                    icon={KeyIcon}
                                    value={data.admin_password}
                                    placeholder="At least 12 characters"
                                    onChange={(event) =>
                                        setData(
                                            'admin_password',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.admin_password)}
                                    autoComplete="new-password"
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    <SectionCard
                        title="Plan and billing"
                        description="Optional — you can assign or change the plan later from the hospital page."
                        icon={CreditCardIcon}
                    >
                        <FieldSet columns={2} className="gap-y-4">
                            <Field
                                label="Plan"
                                htmlFor="plan_id"
                                error={errors.plan_id}
                            >
                                <Select
                                    id="plan_id"
                                    name="plan_id"
                                    value={data.plan_id}
                                    onChange={(event) =>
                                        setData('plan_id', event.target.value)
                                    }
                                    invalid={Boolean(errors.plan_id)}
                                >
                                    <option value="">
                                        No plan yet (assign later)
                                    </option>
                                    {plans.map((plan) => (
                                        <option key={plan.id} value={plan.id}>
                                            {plan.name} —{' '}
                                            {formatCurrency(
                                                plan.monthly_price,
                                                plan.currency,
                                            )}
                                            /mo
                                        </option>
                                    ))}
                                </Select>
                            </Field>

                            <Field
                                label="Billing cycle"
                                htmlFor="billing_cycle"
                                error={errors.billing_cycle}
                                hint={
                                    selectedPlan
                                        ? undefined
                                        : 'Pick a plan first to choose a cycle.'
                                }
                            >
                                <Select
                                    id="billing_cycle"
                                    name="billing_cycle"
                                    value={data.billing_cycle}
                                    onChange={(event) =>
                                        setData(
                                            'billing_cycle',
                                            event.target.value as
                                                | 'monthly'
                                                | 'yearly',
                                        )
                                    }
                                    disabled={!selectedPlan}
                                >
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </Select>
                            </Field>

                            {selectedPlan && (
                                <div className="rounded-lg border border-line bg-surface-muted px-3.5 py-3 sm:col-span-2">
                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                        <div className="flex items-center gap-2">
                                            <Badge tone="accent">
                                                {selectedPlan.name}
                                            </Badge>
                                            <span className="text-[13px] font-semibold text-ink tabular-nums">
                                                {formatCurrency(
                                                    planPrice,
                                                    selectedPlan.currency,
                                                )}{' '}
                                                / {data.billing_cycle}
                                            </span>
                                        </div>
                                        {data.trial && trialDays > 0 && (
                                            <span className="text-[12px] text-ink-subtle">
                                                {trialDays} day trial before the
                                                first invoice
                                            </span>
                                        )}
                                    </div>
                                </div>
                            )}

                            <div className="space-y-3 sm:col-span-2">
                                <Checkbox
                                    name="trial"
                                    checked={data.trial}
                                    onChange={(event) =>
                                        setData('trial', event.target.checked)
                                    }
                                    label={
                                        trialDays > 0
                                            ? `Start on a ${trialDays}-day trial`
                                            : 'Start on a trial'
                                    }
                                    description="The hospital can use the platform before the first invoice is raised."
                                />

                                <Checkbox
                                    name="demo_data"
                                    checked={data.demo_data}
                                    onChange={(event) =>
                                        setData(
                                            'demo_data',
                                            event.target.checked,
                                        )
                                    }
                                    label="Seed demo patients/appointments — do not use in production"
                                    description="Creates sample records so the hospital can explore the app immediately. Delete them before going live."
                                />
                            </div>
                        </FieldSet>
                    </SectionCard>

                    {data.demo_data && (
                        <Alert tone="warning" title="Demo data will be created">
                            Sample patients and appointments are written into
                            this hospital's database. Use this only for
                            demonstrations or pilots — never for a live
                            hospital.
                        </Alert>
                    )}

                    <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <Button variant="ghost" href="/tenants">
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            variant="primary"
                            icon={CheckIcon}
                            loading={processing}
                        >
                            {processing
                                ? 'Provisioning…'
                                : 'Provision hospital'}
                        </Button>
                    </div>
                </form>

                <div className="flex items-start gap-2.5 rounded-lg border border-line bg-surface-muted px-3.5 py-3">
                    <BeakerIcon className="mt-0.5 h-4 w-4 shrink-0 text-ink-subtle" />
                    <p className="text-[12px] leading-relaxed text-ink-subtle">
                        Provisioning creates a separate database for this
                        hospital and can take a few seconds — do not close the
                        tab while the submit button is spinning.
                    </p>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
