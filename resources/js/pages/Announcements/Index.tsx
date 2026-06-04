import {
    PlusIcon,
    TrashIcon,
    MegaphoneIcon,
    ExclamationTriangleIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import EmptyState from '@/Components/EmptyState';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface Announcement {
    id: number;
    title: string;
    body: string;
    type: string;
    is_published: boolean;
    published_at: string | null;
    expires_at: string | null;
    created_at: string;
}

const TYPE_COLORS: Record<string, 'blue' | 'amber' | 'emerald'> = {
    info: 'blue',
    warning: 'amber',
    success: 'emerald',
};

const TYPE_ICON_CLASSES: Record<string, string> = {
    info: 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
    warning:
        'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
    success:
        'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
};

export default function AnnouncementsIndex({
    announcements,
}: {
    announcements: Announcement[];
}) {
    const { t } = useTranslation();
    const [delModal, setDelModal] = useState<Announcement | null>(null);
    const { delete: destroy, processing } = useForm();
    const handleDelete = (e: FormEvent) => {
        e.preventDefault();

        if (delModal) {
            destroy(`/announcements/${delModal.id}`, {
                onSuccess: () => setDelModal(null),
            });
        }
    };

    return (
        <SuperAdminLayout
            headerTitle="Announcements"
            headerSubtitle="System-wide announcements for all tenants"
        >
            <Head title="Announcements" />
            <div className="mx-auto max-w-7xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: '/' },
                        { label: 'Announcements' },
                    ]}
                />

                <div className="flex justify-end">
                    <Link
                        href="/announcements/create"
                        className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                    >
                        <PlusIcon className="h-4 w-4" /> New Announcement
                    </Link>
                </div>

                {announcements.length === 0 ? (
                    <EmptyState
                        icon={MegaphoneIcon}
                        title="No announcements"
                        description="Get started by creating your first system-wide announcement."
                        action={{
                            label: 'New Announcement',
                            href: '/announcements/create',
                        }}
                    />
                ) : (
                    <div className="space-y-4">
                        {announcements.map((a) => (
                            <div
                                key={a.id}
                                className="group rounded-xl border border-gray-200 bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                            >
                                <div className="flex items-start justify-between">
                                    <div className="min-w-0 flex-1">
                                        <div className="mb-2 flex items-center gap-3">
                                            <div
                                                className={`rounded-lg p-2 ${TYPE_ICON_CLASSES[a.type] || 'bg-gray-50 text-gray-600 dark:bg-neutral-700 dark:text-neutral-300'}`}
                                            >
                                                <MegaphoneIcon className="h-5 w-5" />
                                            </div>
                                            <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                                                {a.title}
                                            </h3>
                                            <StatusBadge
                                                label={a.type}
                                                color={
                                                    TYPE_COLORS[a.type] ||
                                                    'gray'
                                                }
                                            />
                                            {a.is_published ? (
                                                <StatusBadge
                                                    label="Published"
                                                    color="emerald"
                                                />
                                            ) : (
                                                <StatusBadge
                                                    label="Draft"
                                                    color="gray"
                                                />
                                            )}
                                        </div>
                                        <p className="mb-3 line-clamp-2 text-sm text-gray-600 dark:text-neutral-400">
                                            {a.body}
                                        </p>
                                        <div className="flex gap-4 text-xs text-gray-400 dark:text-neutral-500">
                                            {a.published_at && (
                                                <span>
                                                    Published:{' '}
                                                    {new Date(
                                                        a.published_at,
                                                    ).toLocaleString()}
                                                </span>
                                            )}
                                            {a.expires_at && (
                                                <span>
                                                    Expires:{' '}
                                                    {new Date(
                                                        a.expires_at,
                                                    ).toLocaleString()}
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                    <button
                                        onClick={() => setDelModal(a)}
                                        className="shrink-0 rounded-lg p-2 text-gray-400 opacity-0 transition-all group-hover:opacity-100 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                                    >
                                        <TrashIcon className="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <Modal
                isOpen={delModal !== null}
                onClose={() => setDelModal(null)}
                title="Delete Announcement"
            >
                <ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-red-600" />
                <p className="text-center text-gray-700 dark:text-neutral-300">
                    Delete <strong>{delModal?.title}</strong>?
                </p>
                <div className="mt-6 flex justify-end gap-3">
                    <button
                        onClick={() => setDelModal(null)}
                        className="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    >
                        {t('common.cancel')}
                    </button>
                    <button
                        onClick={handleDelete}
                        disabled={processing}
                        className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                    >
                        {processing ? 'Deleting...' : 'Delete'}
                    </button>
                </div>
            </Modal>
        </SuperAdminLayout>
    );
}
