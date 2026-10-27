import {
    BuildingOfficeIcon,
    CheckIcon,
    GlobeAltIcon,
    InformationCircleIcon,
    TagIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Alert,
    Button,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SectionCard,
} from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface TenantEditProps {
    tenant: { id: string; slug: string; name: string; domains: string[] };
}

/**
 * Tenants/Edit — the two things an operator can correct about a hospital:
 * its display name and its primary domain.
 *
 * `UpdateTenantRequest` currently validates `name` only, but
 * `TenantController::update()` also rewrites the first domain when one is
 * submitted, so the field is wired for that contract.
 */
export default function TenantsEdit({ tenant }: TenantEditProps) {
    const { data, setData, put, processing, errors, isDirty } = useForm({
        name: tenant.name,
        domain: tenant.domains[0] ?? '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        put(`/tenants/${tenant.id}`, { preserveScroll: true });
    };

    return (
        <SuperAdminLayout
            headerTitle={`Edit ${tenant.name}`}
            headerSubtitle="Hospital details"
        >
            <Head title={`Edit ${tenant.name}`} />

            <div className="mx-auto max-w-3xl space-y-5 pb-12">
                <PageHeader
                    title="Edit hospital"
                    description="The display name and primary domain. The slug is the tenant key and cannot change."
                    breadcrumbs={[
                        { label: 'Hospitals', href: '/tenants' },
                        { label: tenant.name, href: `/tenants/${tenant.id}` },
                        { label: 'Edit' },
                    ]}
                />

                <form onSubmit={submit} className="space-y-5">
                    <SectionCard
                        title="Identity"
                        description="Shown across the console and on invoices."
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
                                    onChange={(event) =>
                                        setData('name', event.target.value)
                                    }
                                    invalid={Boolean(errors.name)}
                                    autoFocus
                                />
                            </Field>

                            <Field
                                label="Slug"
                                htmlFor="slug"
                                hint="Immutable tenant key."
                            >
                                <Input
                                    id="slug"
                                    icon={TagIcon}
                                    value={tenant.slug}
                                    readOnly
                                    disabled
                                    className="font-mono"
                                />
                            </Field>

                            <Field
                                label="Primary domain"
                                htmlFor="domain"
                                error={errors.domain}
                                hint="Full host, no protocol."
                            >
                                <Input
                                    id="domain"
                                    name="domain"
                                    icon={GlobeAltIcon}
                                    value={data.domain}
                                    placeholder="hospital.sanago.cm"
                                    onChange={(event) =>
                                        setData('domain', event.target.value)
                                    }
                                    invalid={Boolean(errors.domain)}
                                    spellCheck={false}
                                />
                            </Field>
                        </FieldSet>

                        {tenant.domains.length > 1 && (
                            <Alert
                                tone="info"
                                title="Additional domains"
                                className="mt-4"
                            >
                                This hospital also answers on{' '}
                                {tenant.domains.slice(1).join(', ')}. Only the
                                primary domain is editable here.
                            </Alert>
                        )}
                    </SectionCard>

                    {Object.keys(errors).length > 0 && (
                        <Alert tone="critical" title="Could not save">
                            {Object.values(errors).join(' ')}
                        </Alert>
                    )}

                    <div className="flex flex-wrap items-center justify-between gap-2 border-t border-line pt-4">
                        <p className="flex items-center gap-1.5 text-[12px] text-ink-subtle">
                            <InformationCircleIcon className="h-3.5 w-3.5" />
                            Changes are recorded in the platform audit log.
                        </p>
                        <div className="flex items-center gap-2">
                            <Button
                                variant="secondary"
                                href={`/tenants/${tenant.id}`}
                            >
                                Cancel
                            </Button>
                            <Button
                                type="submit"
                                icon={CheckIcon}
                                loading={processing}
                                disabled={!isDirty}
                            >
                                Save changes
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </SuperAdminLayout>
    );
}
