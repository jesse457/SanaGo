import {
    CalendarDaysIcon,
    CreditCardIcon,
    GlobeAltIcon,
    CheckCircleIcon,
    XCircleIcon,
    ExclamationTriangleIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import DetailItem from '@/Components/DetailItem';
import EmptyState from '@/Components/EmptyState';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface Subscription {
    id: number;
    plan_name: string;
    plan: { id: number; name: string } | null;
    billing_cycle: string;
    price: string;
    start_date: string;
    end_date: string;
    status: string;
    auto_renew: boolean;
    features_json: { feature_label: string; is_highlighted: boolean }[];
}
interface TenantNote {
    id: number;
    note: string;
    user_name: string;
    created_at: string;
}
interface Tenant {
    id: string;
    slug: string;
    name: string;
    domains: string[];
    is_active: boolean;
    suspended_at: string | null;
    trial_ends_at: string | null;
    created_at: string;
    updated_at: string;
    subscription: Subscription | null;
    notes: TenantNote[];
}

export default function TenantShow({ tenant }: { tenant: Tenant }) {
    const sub = tenant.subscription;
    const [showDelModal, setShowDelModal] = useState(false);
    const { delete: destroy, processing: delProcessing } = useForm();

    const handleToggleActive = () => {
        if (
            confirm(
                tenant.is_active
                    ? 'Suspend this tenant?'
                    : 'Reactivate this tenant?',
            )
        ) {
            router.patch(`/tenants/${tenant.id}/toggle-active`);
        }
    };

    const handleDelete = (e: FormEvent) => {
        e.preventDefault();
        destroy(`/tenants/${tenant.id}`, {
            onSuccess: () => setShowDelModal(false),
        });
    };

    const daysRemaining = sub
        ? Math.max(
              0,
              Math.ceil(
                  (new Date(sub.end_date).getTime() - Date.now()) / 86400000,
              ),
          )
        : 0;
    const usagePercent =
        daysRemaining && sub
            ? Math.round(((365 - daysRemaining) / 365) * 100)
            : 0;

    return (
        <SuperAdminLayout
            headerTitle={tenant.name}
            headerSubtitle="Tenant Details"
        >
            <Head title={`${tenant.name} — Tenant`} />
            <div className="mx-auto max-w-7xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: 'Home', href: '/' },
                        { label: 'Tenants', href: '/tenants' },
                        { label: tenant.name },
                    ]}
                />

                {/* Stats Row */}
                <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <StatCard
                        icon={tenant.is_active ? CheckCircleIcon : XCircleIcon}
                        label="Status"
                        value={tenant.is_active ? 'Active' : 'Suspended'}
                        color={tenant.is_active ? 'blue' : 'red'}
                    />
                    <StatCard
                        icon={GlobeAltIcon}
                        label="Domain"
                        value={tenant.domains[0] || '—'}
                        color="blue"
                    />
                    <StatCard
                        icon={CalendarDaysIcon}
                        label="Created"
                        value={new Date(tenant.created_at).toLocaleDateString()}
                        color="blue"
                    />
                    <StatCard
                        icon={CreditCardIcon}
                        label="Subscription"
                        value={sub ? sub.plan_name : 'None'}
                        color={sub ? 'blue' : 'amber'}
                    />
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Main Info */}
                    <div className="space-y-6 lg:col-span-2">
                        <PageSection title="Hospital Information">
                            <div className="p-6">
                                <dl className="grid gap-4 sm:grid-cols-2">
                                    <DetailItem
                                        label="Name"
                                        value={tenant.name}
                                    />
                                    <DetailItem
                                        label="Slug"
                                        value={tenant.slug}
                                    />
                                    <div className="sm:col-span-2">
                                        <DetailItem
                                            label="Domain"
                                            value={
                                                tenant.domains[0] || 'No domain'
                                            }
                                        />
                                    </div>
                                    <DetailItem
                                        label="Created"
                                        value={new Date(
                                            tenant.created_at,
                                        ).toLocaleDateString()}
                                    />
                                    <DetailItem
                                        label="Trial Ends"
                                        value={tenant.trial_ends_at}
                                    />
                                    {tenant.suspended_at && (
                                        <DetailItem
                                            label="Suspended At"
                                            value={new Date(
                                                tenant.suspended_at,
                                            ).toLocaleString()}
                                        />
                                    )}
                                </dl>
                                <div className="mt-6 flex gap-3 border-t border-gray-100 pt-6 dark:border-neutral-800">
                                    {tenant.is_active ? (
                                        <button
                                            onClick={handleToggleActive}
                                            className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                                        >
                                            Suspend Tenant
                                        </button>
                                    ) : (
                                        <button
                                            onClick={handleToggleActive}
                                            className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700"
                                        >
                                            Reactivate Tenant
                                        </button>
                                    )}
                                    <button
                                        onClick={() => setShowDelModal(true)}
                                        className="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700"
                                    >
                                        Delete Tenant
                                    </button>
                                </div>
                            </div>
                        </PageSection>

                        {tenant.notes.length > 0 && (
                            <PageSection title="Internal Notes">
                                <div className="space-y-3 p-6">
                                    {tenant.notes.map((n) => (
                                        <div
                                            key={n.id}
                                            className="rounded-lg bg-gray-50 p-3 dark:bg-neutral-800"
                                        >
                                            <p className="text-sm text-gray-700 dark:text-neutral-300">
                                                {n.note}
                                            </p>
                                            <p className="mt-1.5 text-xs text-gray-400 dark:text-neutral-500">
                                                {n.user_name} —{' '}
                                                {new Date(
                                                    n.created_at,
                                                ).toLocaleString()}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            </PageSection>
                        )}
                    </div>

                    {/* Subscription Sidebar */}
                    <div className="space-y-6">
                        {sub ? (
                            <PageSection
                                title="Subscription"
                                icon={CreditCardIcon}
                            >
                                <div className="space-y-3 p-6">
                                    <div className="flex justify-between">
                                        <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            Plan
                                        </span>
                                        <span className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                            {sub.plan_name}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            Price
                                        </span>
                                        <span className="text-sm font-bold text-blue-600 dark:text-blue-400">
                                            ${sub.price}
                                            <span className="text-xs font-normal text-gray-400 dark:text-neutral-500">
                                                /{sub.billing_cycle}
                                            </span>
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            Status
                                        </span>
                                        <StatusBadge
                                            label={sub.status}
                                            color={
                                                sub.status === 'active'
                                                    ? 'emerald'
                                                    : 'gray'
                                            }
                                        />
                                    </div>
                                    <hr className="border-gray-100 dark:border-neutral-700" />
                                    <div className="flex justify-between">
                                        <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            Start Date
                                        </span>
                                        <span className="text-sm font-medium text-gray-900 dark:text-neutral-100">
                                            {sub.start_date}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            End Date
                                        </span>
                                        <span className="text-sm font-medium text-gray-900 dark:text-neutral-100">
                                            {sub.end_date}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            Days Remaining
                                        </span>
                                        <span
                                            className={`text-sm font-bold ${daysRemaining < 30 ? 'text-red-500' : 'text-emerald-600 dark:text-emerald-400'}`}
                                        >
                                            {daysRemaining} days
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            Auto Renew
                                        </span>
                                        <span
                                            className={`text-sm font-medium ${sub.auto_renew ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500'}`}
                                        >
                                            {sub.auto_renew ? 'Yes' : 'No'}
                                        </span>
                                    </div>
                                    <div className="mt-2">
                                        <div className="mb-1 flex justify-between text-xs text-gray-400 dark:text-neutral-500">
                                            <span className="font-semibold tracking-wide uppercase">
                                                Usage ({usagePercent}%)
                                            </span>
                                            <span>
                                                {365 - daysRemaining} / 365 days
                                            </span>
                                        </div>
                                        <div className="h-2 w-full rounded-full bg-gray-100 dark:bg-neutral-700">
                                            <div
                                                className={`h-2 rounded-full transition-all ${usagePercent > 90 ? 'bg-red-500' : 'bg-blue-600'}`}
                                                style={{
                                                    width: `${Math.min(usagePercent, 100)}%`,
                                                }}
                                            />
                                        </div>
                                    </div>

                                    {sub.features_json &&
                                        sub.features_json.length > 0 && (
                                            <>
                                                <hr className="border-gray-100 dark:border-neutral-700" />
                                                <p className="text-xs font-bold text-gray-400 uppercase dark:text-neutral-500">
                                                    Features
                                                </p>
                                                <ul className="space-y-1.5">
                                                    {sub.features_json.map(
                                                        (f, i) => (
                                                            <li
                                                                key={i}
                                                                className={`flex items-center gap-1.5 text-xs ${f.is_highlighted ? 'font-semibold text-gray-900 dark:text-neutral-100' : 'text-gray-500 dark:text-neutral-400'}`}
                                                            >
                                                                <CheckCircleIcon
                                                                    className={`h-3.5 w-3.5 shrink-0 ${f.is_highlighted ? 'text-blue-600 dark:text-blue-400' : 'text-gray-300 dark:text-neutral-600'}`}
                                                                />
                                                                {
                                                                    f.feature_label
                                                                }
                                                            </li>
                                                        ),
                                                    )}
                                                </ul>
                                            </>
                                        )}

                                    <hr className="border-gray-100 dark:border-neutral-700" />
                                    <Link
                                        href={`/subscriptions/${sub.id}/edit`}
                                        className="block rounded-lg bg-blue-50 py-2 text-center text-sm font-semibold text-blue-600 transition-colors hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30"
                                    >
                                        Manage Subscription
                                    </Link>
                                </div>
                            </PageSection>
                        ) : (
                            <EmptyState
                                icon={ExclamationTriangleIcon}
                                title="No active subscription"
                                action={{
                                    label: 'Create Subscription',
                                    href: '/subscriptions/create',
                                }}
                            />
                        )}
                    </div>
                </div>
            </div>

            <Modal
                isOpen={showDelModal}
                onClose={() => setShowDelModal(false)}
                title="Delete Tenant"
            >
                <div className="text-center">
                    <ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-red-600" />
                    <p className="text-gray-700 dark:text-neutral-300">
                        Delete <strong>{tenant.name}</strong> permanently?
                    </p>
                </div>
                <div className="mt-6 flex justify-end gap-3">
                    <button
                        onClick={() => setShowDelModal(false)}
                        className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    >
                        Cancel
                    </button>
                    <button
                        onClick={handleDelete}
                        disabled={delProcessing}
                        className="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {delProcessing ? 'Deleting...' : 'Delete'}
                    </button>
                </div>
            </Modal>
        </SuperAdminLayout>
    );
}
