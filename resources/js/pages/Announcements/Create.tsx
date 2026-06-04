import { MegaphoneIcon } from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

export default function AnnouncementsCreate() {
    const [form, setForm] = useState({
        title: '',
        body: '',
        type: 'info',
        is_published: false,
        expires_at: '',
    });
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        router.post('/announcements', form, {
            onError: (err) => {
                setErrors(err as any);
                setSubmitting(false);
            },
        });
    };

    return (
        <SuperAdminLayout
            headerTitle="New Announcement"
            headerSubtitle="Create a system-wide announcement"
        >
            <Head title="New Announcement" />
            <div className="mx-auto max-w-6xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: 'Home', href: '/' },
                        { label: 'Announcements', href: '/announcements' },
                        { label: 'New Announcement' },
                    ]}
                />

                <form onSubmit={handleSubmit}>
                    <PageSection
                        title="Announcement Details"
                        description="Fill in the details for your new announcement"
                        icon={MegaphoneIcon}
                    >
                        <div className="space-y-6 p-6">
                            <FormInput
                                name="title"
                                label="Title"
                                required
                                value={form.title}
                                onChange={(v) => setForm({ ...form, title: v })}
                                errors={errors}
                            />

                            <FormTextarea
                                name="body"
                                label="Body"
                                required
                                rows={5}
                                value={form.body}
                                onChange={(v) => setForm({ ...form, body: v })}
                                errors={errors}
                            />

                            <div className="grid gap-6 sm:grid-cols-2">
                                <FormSelect
                                    name="type"
                                    label="Type"
                                    value={form.type}
                                    onChange={(v) =>
                                        setForm({ ...form, type: v })
                                    }
                                    options={[
                                        { value: 'info', label: 'Info' },
                                        { value: 'warning', label: 'Warning' },
                                        { value: 'success', label: 'Success' },
                                    ]}
                                />

                                <FormInput
                                    name="expires_at"
                                    label="Expires At"
                                    type="date"
                                    value={form.expires_at}
                                    onChange={(v) =>
                                        setForm({ ...form, expires_at: v })
                                    }
                                />
                            </div>

                            <div className="flex items-center gap-3">
                                <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    Publish Immediately
                                </label>
                                <input
                                    type="checkbox"
                                    checked={form.is_published}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            is_published: e.target.checked,
                                        })
                                    }
                                    className="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800"
                                />
                            </div>
                        </div>

                        <div className="flex justify-end gap-3 border-t border-gray-100 px-6 py-4 dark:border-neutral-700">
                            <Link
                                href="/announcements"
                                className="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={submitting}
                                className="rounded-lg bg-blue-600 px-6 py-2 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-50"
                            >
                                {submitting
                                    ? 'Creating...'
                                    : 'Create Announcement'}
                            </button>
                        </div>
                    </PageSection>
                </form>
            </div>
        </SuperAdminLayout>
    );
}
