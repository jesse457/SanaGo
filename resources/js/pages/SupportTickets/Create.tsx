import {
    BuildingOfficeIcon,
    DocumentTextIcon,
    TicketIcon,
    ArrowPathIcon,
    CheckIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
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
    const [form, setForm] = useState({
        tenant_id: '',
        subject: '',
        priority: 'medium',
        message: '',
    });
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        router.post('/support-tickets', form, {
            onError: (err) => {
                setErrors(err as any);
                setSubmitting(false);
            },
        });
    };

    return (
        <SuperAdminLayout
            headerTitle="New Ticket"
            headerSubtitle="Create a support ticket"
        >
            <Head title="New Ticket" />
            <div className="mx-auto max-w-6xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: 'Home', href: '/' },
                        { label: 'Support Tickets', href: '/support-tickets' },
                        { label: 'New' },
                    ]}
                />

                <PageSection title="New Support Ticket" icon={TicketIcon}>
                    <form onSubmit={handleSubmit} className="space-y-6 p-6">
                        <FormSelect
                            name="tenant_id"
                            label="Tenant"
                            icon={BuildingOfficeIcon}
                            required
                            errors={errors}
                            value={form.tenant_id}
                            onChange={(v) => setForm({ ...form, tenant_id: v })}
                            options={tenants.map((t) => ({
                                value: t.id,
                                label: t.name,
                            }))}
                            placeholder="Select tenant..."
                        />
                        <FormInput
                            name="subject"
                            label="Subject"
                            icon={DocumentTextIcon}
                            required
                            errors={errors}
                            value={form.subject}
                            onChange={(v) => setForm({ ...form, subject: v })}
                        />
                        <FormSelect
                            name="priority"
                            label="Priority"
                            required
                            value={form.priority}
                            onChange={(v) => setForm({ ...form, priority: v })}
                            options={[
                                { value: 'low', label: 'Low' },
                                { value: 'medium', label: 'Medium' },
                                { value: 'high', label: 'High' },
                                { value: 'urgent', label: 'Urgent' },
                            ]}
                        />
                        <FormTextarea
                            name="message"
                            label="Message"
                            required
                            errors={errors}
                            value={form.message}
                            onChange={(v) => setForm({ ...form, message: v })}
                            rows={5}
                        />
                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href="/support-tickets"
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={submitting}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {submitting ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />{' '}
                                        Creating...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" /> Create
                                        Ticket
                                    </span>
                                )}
                            </button>
                        </div>
                    </form>
                </PageSection>
            </div>
        </SuperAdminLayout>
    );
}
