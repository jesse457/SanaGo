import { PaperAirplaneIcon, ArrowPathIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React from 'react';

import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import AppLayout from '@/Layouts/AppLayout';

export default function CreateFeedback() {
    // --- Form Hook ---
    const { data, setData, post, processing, errors, reset } = useForm({
        subject: '',
        category: 'dashboard',
        message: '',
    });

    // --- Submit Logic ---
    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/feedback/store', {
            onSuccess: () => reset(),
        });
    };

    const categoryOptions = [
        { value: 'dashboard', label: 'Dashboard' },
        { value: 'shift-management', label: 'Shift Management' },
        { value: 'revenue-report', label: 'Revenue Report' },
        { value: 'settings', label: 'Settings' },
        { value: 'other', label: 'Other System Issues' },
    ];

    return (
        <AppLayout
            headerTitle="Submit Feedback"
            headerSubtitle="Share your thoughts or report app issues"
        >
            <Head title="Submit Feedback" />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: 'Home', href: '/' },
                        { label: 'Feedbacks', href: '/feedback' },
                        { label: 'Submit Feedback' },
                    ]}
                />

                {/* Form */}
                <PageSection
                    title="New Feedback"
                    description="Provide details for your submission below."
                >
                    <form onSubmit={submit} className="space-y-8 p-6 sm:p-8">
                        <div className="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-2">
                            {/* Subject */}
                            <FormInput
                                name="subject"
                                label="Subject"
                                placeholder="Brief title of the issue/request"
                                required
                                value={data.subject}
                                onChange={(val) => setData('subject', val)}
                                errors={errors}
                            />

                            {/* Category */}
                            <FormSelect
                                name="category"
                                label="Category"
                                required
                                value={data.category}
                                onChange={(val) => setData('category', val)}
                                options={categoryOptions}
                                errors={errors}
                            />
                        </div>

                        {/* Message */}
                        <FormTextarea
                            name="message"
                            label="Message"
                            placeholder="Describe your experience or feature request in detail..."
                            required
                            value={data.message}
                            onChange={(val) => setData('message', val)}
                            errors={errors}
                            rows={6}
                        />

                        {/* Footer */}
                        <div className="flex flex-col-reverse gap-6 border-t border-gray-100 pt-6 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-800">
                            <div className="text-xs text-gray-500 dark:text-neutral-400">
                                Submitting feedback means agreeing to our
                                community standards.
                            </div>

                            <div className="flex w-full flex-col-reverse gap-3 sm:w-auto sm:flex-row">
                                <button
                                    type="button"
                                    onClick={() => reset()}
                                    className="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm transition-all hover:bg-gray-50 sm:w-auto dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300"
                                >
                                    Start Over
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md transition-all hover:bg-blue-700 disabled:opacity-50 sm:w-auto"
                                >
                                    {processing ? (
                                        <>
                                            <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                            Sending...
                                        </>
                                    ) : (
                                        <>
                                            <PaperAirplaneIcon className="h-4 w-4 -rotate-45" />
                                            Send Feedback
                                        </>
                                    )}
                                </button>
                            </div>
                        </div>
                    </form>
                </PageSection>
            </div>
        </AppLayout>
    );
}
