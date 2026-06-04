import {
    TicketIcon,
    ArrowPathIcon,
    CheckIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

export default function SupportCreate() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        subject: '',
        priority: 'medium',
        message: '',
    });
    const submit = (e: FormEvent) => {
        e.preventDefault();
        post('/support');
    };

    return (
        <AppLayout
            headerTitle="New Support Ticket"
            headerSubtitle="Report an issue or ask for help"
        >
            <Head title="New Ticket" />
            <div className="mx-auto max-w-3xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: 'Support', href: '/support' },
                        { label: 'New' },
                    ]}
                />

                <PageSection title="Ticket Details" icon={TicketIcon}>
                    <form onSubmit={submit} className="space-y-6 p-6">
                        <FormInput
                            name="subject"
                            label="Subject"
                            required
                            value={data.subject}
                            onChange={(v) => setData('subject', v)}
                            errors={errors}
                        />

                        <FormSelect
                            name="priority"
                            label="Priority"
                            value={data.priority}
                            onChange={(v) => setData('priority', v)}
                            options={[
                                { value: 'low', label: 'Low' },
                                { value: 'medium', label: 'Medium' },
                                { value: 'high', label: 'High' },
                                { value: 'urgent', label: 'Urgent' },
                            ]}
                            errors={errors}
                        />

                        <FormTextarea
                            name="message"
                            label="Message"
                            required
                            value={data.message}
                            onChange={(v) => setData('message', v)}
                            rows={5}
                            errors={errors}
                        />

                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href="/support"
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('common.cancel')}
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />{' '}
                                        Submitting...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" /> Submit
                                        Ticket
                                    </span>
                                )}
                            </button>
                        </div>
                    </form>
                </PageSection>
            </div>
        </AppLayout>
    );
}
