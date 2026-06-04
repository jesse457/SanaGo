import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    CreditCardIcon,
    MagnifyingGlassIcon,
    ExclamationTriangleIcon,
    CurrencyDollarIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface PlanFeature {
    feature_label: string;
    is_highlighted: boolean;
}
interface Plan {
    id: number;
    name: string;
    slug: string;
    description: string;
    monthly_price: string | null;
    yearly_price: string | null;
    is_active: boolean;
    features_json: PlanFeature[];
    subscriptions_count: number;
}

export default function PlansIndex({ plans }: { plans: Plan[] }) {
    const [search, setSearch] = useState('');
    const [delModal, setDelModal] = useState<Plan | null>(null);
    const { delete: destroy, processing } = useForm();
    const filtered = useMemo(
        () =>
            plans.filter(
                (p) =>
                    p.name.toLowerCase().includes(search.toLowerCase()) ||
                    p.slug.toLowerCase().includes(search.toLowerCase()),
            ),
        [plans, search],
    );
    const handleDelete = (e: FormEvent) => {
        e.preventDefault();

        if (delModal) {
            destroy(`/plans/${delModal.id}`, {
                onSuccess: () => setDelModal(null),
            });
        }
    };

    return (
        <SuperAdminLayout
            headerTitle="Plans"
            headerSubtitle="Manage subscription plans and pricing"
        >
            <Head title="Plans" />
            <div className="mx-auto max-w-7xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[{ label: 'Home' }, { label: 'Plans' }]}
                />

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="relative w-full sm:w-96">
                        <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Search plans..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800"
                        />
                    </div>
                    <Link
                        href="/plans/create"
                        className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                    >
                        <PlusIcon className="h-4 w-4" /> New Plan
                    </Link>
                </div>

                {filtered.length > 0 ? (
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {filtered.map((plan) => (
                            <div
                                key={plan.id}
                                className={`group relative flex h-full flex-col rounded-xl border bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 ${!plan.is_active && 'opacity-60'}`}
                            >
                                <div className="mb-4 flex items-start justify-between">
                                    <div className="rounded-lg bg-blue-50 p-3 text-blue-600">
                                        <CreditCardIcon className="h-6 w-6" />
                                    </div>
                                    {!plan.is_active && (
                                        <StatusBadge
                                            label="Inactive"
                                            color="gray"
                                        />
                                    )}
                                </div>
                                <div className="mb-4 flex-grow">
                                    <h3 className="truncate text-lg font-bold text-gray-900 dark:text-neutral-100">
                                        {plan.name}
                                    </h3>
                                    <span className="mt-1 inline-block rounded bg-gray-50 px-1.5 py-0.5 font-mono text-xs text-gray-400 dark:bg-neutral-700">
                                        {plan.slug}
                                    </span>
                                    {plan.description && (
                                        <p className="mt-3 line-clamp-2 text-sm text-gray-500 dark:text-neutral-400">
                                            {plan.description}
                                        </p>
                                    )}
                                    <div className="mt-4 space-y-1">
                                        {plan.monthly_price && (
                                            <p className="flex items-center gap-1 text-2xl font-bold text-gray-900 dark:text-neutral-100">
                                                <CurrencyDollarIcon className="h-5 w-5 text-gray-400" />
                                                {plan.monthly_price}
                                                <span className="text-sm font-normal text-gray-500">
                                                    /mo
                                                </span>
                                            </p>
                                        )}
                                        {plan.yearly_price && (
                                            <p className="text-sm text-gray-500">
                                                ${plan.yearly_price}/yr
                                            </p>
                                        )}
                                    </div>
                                    <div className="mt-4 mb-4">
                                        <p className="mb-2 text-xs font-semibold tracking-wider text-gray-400 uppercase">
                                            Features
                                        </p>
                                        <div className="flex flex-wrap gap-1.5">
                                            {plan.features_json
                                                .filter((f) => f.is_highlighted)
                                                .map((f, i) => (
                                                    <span
                                                        key={i}
                                                        className="rounded border border-blue-100 bg-blue-50 px-2 py-0.5 text-[10px] font-medium text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-400"
                                                    >
                                                        {f.feature_label}
                                                    </span>
                                                ))}
                                        </div>
                                    </div>
                                    <div className="text-xs text-gray-400">
                                        {plan.subscriptions_count} subscriptions
                                    </div>
                                </div>
                                <div className="flex gap-2 border-t border-gray-100 pt-4 opacity-0 transition-opacity group-hover:opacity-100 dark:border-neutral-700">
                                    <Link
                                        href={`/plans/${plan.id}/edit`}
                                        className="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gray-50 py-2 text-xs font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-600 dark:bg-neutral-700 dark:text-neutral-400"
                                    >
                                        <PencilSquareIcon className="h-4 w-4" />{' '}
                                        Edit
                                    </Link>
                                    <button
                                        onClick={() => setDelModal(plan)}
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
                        title="No plans found"
                        action={{
                            label: 'Create First Plan',
                            href: '/plans/create',
                        }}
                    />
                )}
            </div>

            <Modal
                isOpen={!!delModal}
                onClose={() => setDelModal(null)}
                title="Delete Plan"
            >
                <div className="text-center">
                    <ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-red-600" />
                    <p className="text-gray-700 dark:text-neutral-300">
                        Delete plan <strong>{delModal?.name}</strong>?
                    </p>
                    <div className="mt-6 flex justify-center gap-3">
                        <button
                            onClick={() => setDelModal(null)}
                            className="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            Cancel
                        </button>
                        <button
                            onClick={handleDelete}
                            disabled={processing}
                            className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                        >
                            {processing ? 'Deleting...' : 'Delete'}
                        </button>
                    </div>
                </div>
            </Modal>
        </SuperAdminLayout>
    );
}
