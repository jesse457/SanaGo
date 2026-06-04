import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    CreditCardIcon,
    MagnifyingGlassIcon,
    ExclamationTriangleIcon,
    CalendarDaysIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface SubscriptionItem {
    id: number;
    plan_name: string;
    tenant_name: string;
    billing_cycle: string;
    price: string;
    start_date: string;
    end_date: string;
    status: string;
    features_json: { feature_label: string }[];
}

export default function SubscriptionsIndex({
    subscriptions,
}: {
    subscriptions: SubscriptionItem[];
}) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [delModal, setDelModal] = useState<SubscriptionItem | null>(null);
    const { delete: destroy, processing } = useForm();

    const filtered = useMemo(
        () =>
            subscriptions.filter(
                (s) =>
                    s.tenant_name
                        ?.toLowerCase()
                        .includes(search.toLowerCase()) ||
                    s.plan_name.toLowerCase().includes(search.toLowerCase()),
            ),
        [subscriptions, search],
    );

    const handleDelete = (e: FormEvent) => {
        e.preventDefault();

        if (delModal) {
            destroy(`/subscriptions/${delModal.id}`, {
                onSuccess: () => setDelModal(null),
            });
        }
    };

    const badgeColor: Record<string, 'emerald' | 'gray' | 'red'> = {
        active: 'emerald',
        inactive: 'gray',
        cancelled: 'red',
    };

    return (
        <SuperAdminLayout
            headerTitle="Subscriptions"
            headerSubtitle="Manage tenant subscriptions"
        >
            <Head title="Subscriptions" />
            <div className="mx-auto max-w-7xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[{ label: 'Home' }, { label: 'Subscriptions' }]}
                />

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="w-full sm:w-72">
                        <FormInput
                            name="search"
                            placeholder="Search subscriptions..."
                            icon={MagnifyingGlassIcon}
                            value={search}
                            onChange={setSearch}
                        />
                    </div>
                    <Link
                        href="/subscriptions/create"
                        className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700"
                    >
                        <PlusIcon className="h-4 w-4" /> New Subscription
                    </Link>
                </div>

                {filtered.length > 0 ? (
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {filtered.map((sub) => (
                            <div
                                key={sub.id}
                                className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                            >
                                <div className="mb-4 flex items-start justify-between">
                                    <div className="rounded-lg bg-blue-50 p-3 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                                        <CreditCardIcon className="h-6 w-6" />
                                    </div>
                                    <StatusBadge
                                        label={sub.status}
                                        color={badgeColor[sub.status] || 'gray'}
                                    />
                                </div>
                                <div className="mb-4 flex-grow">
                                    <Link href={`/subscriptions/${sub.id}`}>
                                        <h3 className="truncate text-lg font-bold text-gray-900 hover:text-blue-600 dark:text-neutral-100">
                                            {sub.plan_name}
                                        </h3>
                                    </Link>
                                    <p className="mt-1 text-sm font-medium text-gray-600 dark:text-neutral-400">
                                        {sub.tenant_name}
                                    </p>
                                    <p className="mt-2 text-lg font-bold text-gray-900 dark:text-neutral-100">
                                        ${sub.price}
                                        <span className="text-sm font-normal text-gray-500">
                                            /{sub.billing_cycle}
                                        </span>
                                    </p>
                                    <div className="mt-3 flex items-center gap-1.5 text-xs text-gray-500">
                                        <CalendarDaysIcon className="h-3.5 w-3.5" />
                                        {sub.start_date} — {sub.end_date}
                                    </div>
                                </div>
                                <div className="flex gap-2 border-t border-gray-100 pt-4 opacity-0 transition-opacity group-hover:opacity-100 dark:border-neutral-700">
                                    <Link
                                        href={`/subscriptions/${sub.id}/edit`}
                                        className="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gray-50 py-2 text-xs font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-600 dark:bg-neutral-700 dark:text-neutral-400"
                                    >
                                        <PencilSquareIcon className="h-4 w-4" />{' '}
                                        Edit
                                    </Link>
                                    <button
                                        onClick={() => setDelModal(sub)}
                                        className="rounded-lg bg-gray-50 px-3 py-2 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:bg-neutral-700"
                                    >
                                        <TrashIcon className="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <EmptyState
                        icon={CreditCardIcon}
                        title="No subscriptions found"
                        description={
                            search
                                ? 'Try adjusting your search terms.'
                                : undefined
                        }
                    />
                )}
            </div>

            <Modal
                isOpen={delModal !== null}
                onClose={() => setDelModal(null)}
                title="Delete Subscription"
            >
                <div className="text-center">
                    <ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-red-600" />
                    <p className="text-gray-700 dark:text-neutral-300">
                        Delete <strong>{delModal?.plan_name}</strong>{' '}
                        subscription?
                    </p>
                </div>
                <div className="mt-6 flex justify-end gap-3">
                    <button
                        onClick={() => setDelModal(null)}
                        className="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
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
