import { CheckIcon, TicketIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Button,
    Card,
    CardContent,
    Field,
    FieldSet,
    Input,
    PageHeader,
    Select,
    Textarea,
} from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface Tenant {
    id: string;
    name: string;
}

export default function SupportTicketsCreate({
    tenants,
}: {
    tenants: Tenant[];
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        tenant_id: '',
        subject: '',
        priority: 'medium',
        message: '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post('/support-tickets', {
            onSuccess: () => reset(),
        });
    };

    return (
        <SuperAdminLayout
            headerTitle="New Ticket"
            headerSubtitle="Create a support ticket"
        >
            <Head title="New Ticket" />
            <div className="mx-auto w-full max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageHeader
                    title="New support ticket"
                    eyebrow="Landlord"
                    description="Raise a ticket on behalf of a tenant."
                    icon={TicketIcon}
                    breadcrumbs={[
                        { label: 'Home', href: '/' },
                        { label: 'Support tickets', href: '/support-tickets' },
                        { label: 'New' },
                    ]}
                />

                <Card>
                    <CardContent className="py-5">
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <FieldSet title="Ticket details" columns={1}>
                                <Field
                                    label="Tenant"
                                    htmlFor="tenant_id"
                                    required
                                    error={errors.tenant_id}
                                >
                                    <Select
                                        id="tenant_id"
                                        value={data.tenant_id}
                                        onChange={(e) =>
                                            setData('tenant_id', e.target.value)
                                        }
                                        placeholder="Select tenant…"
                                        invalid={Boolean(errors.tenant_id)}
                                    >
                                        {tenants.map((tenant) => (
                                            <option
                                                key={tenant.id}
                                                value={tenant.id}
                                            >
                                                {tenant.name}
                                            </option>
                                        ))}
                                    </Select>
                                </Field>

                                <Field
                                    label="Subject"
                                    htmlFor="subject"
                                    required
                                    error={errors.subject}
                                >
                                    <Input
                                        id="subject"
                                        value={data.subject}
                                        onChange={(e) =>
                                            setData('subject', e.target.value)
                                        }
                                        invalid={Boolean(errors.subject)}
                                    />
                                </Field>

                                <Field
                                    label="Priority"
                                    htmlFor="priority"
                                    required
                                    error={errors.priority}
                                >
                                    <Select
                                        id="priority"
                                        value={data.priority}
                                        onChange={(e) =>
                                            setData('priority', e.target.value)
                                        }
                                        invalid={Boolean(errors.priority)}
                                    >
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </Select>
                                </Field>

                                <Field
                                    label="Message"
                                    htmlFor="message"
                                    required
                                    error={errors.message}
                                >
                                    <Textarea
                                        id="message"
                                        value={data.message}
                                        onChange={(e) =>
                                            setData('message', e.target.value)
                                        }
                                        rows={5}
                                        invalid={Boolean(errors.message)}
                                    />
                                </Field>
                            </FieldSet>

                            <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                                <Button
                                    href="/support-tickets"
                                    variant="secondary"
                                    size="sm"
                                >
                                    Cancel
                                </Button>
                                <Button
                                    type="submit"
                                    size="sm"
                                    icon={processing ? undefined : CheckIcon}
                                    loading={processing}
                                >
                                    {processing ? 'Creating…' : 'Create ticket'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </SuperAdminLayout>
    );
}
