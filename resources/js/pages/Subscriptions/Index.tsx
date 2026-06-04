import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { PlusIcon, PencilSquareIcon, TrashIcon, CreditCardIcon, MagnifyingGlassIcon, ExclamationTriangleIcon, XMarkIcon, HomeIcon, ChevronRightIcon, CalendarDaysIcon } from '@heroicons/react/24/outline';

interface SubscriptionItem { id: number; plan_name: string; tenant_name: string; billing_cycle: string; price: string; start_date: string; end_date: string; status: string; features_json: { feature_label: string }[]; }

export default function SubscriptionsIndex({ subscriptions }: { subscriptions: SubscriptionItem[] }) {
  const { t } = useTranslation();
  const [search, setSearch] = useState('');
  const [delModal, setDelModal] = useState<SubscriptionItem | null>(null);
  const { delete: destroy, processing } = useForm();
  const filtered = useMemo(() => subscriptions.filter(s => s.tenant_name?.toLowerCase().includes(search.toLowerCase()) || s.plan_name.toLowerCase().includes(search.toLowerCase())), [subscriptions, search]);
  const handleDelete = (e: FormEvent) => { e.preventDefault(); if (delModal) destroy(`/subscriptions/${delModal.id}`, { onSuccess: () => setDelModal(null) }); };

  const statusColors: Record<string, string> = { active: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400', inactive: 'bg-gray-100 text-gray-600', cancelled: 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400' };

  return (
    <SuperAdminLayout headerTitle="Subscriptions" headerSubtitle="Manage tenant subscriptions">
      <Head title="Subscriptions" />
      <div className="mx-auto max-w-7xl space-y-6 px-6">
        <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="mr-1 h-4 w-4" /> {t('common.home')}</li><li><ChevronRightIcon className="h-4 w-4" /></li><li className="text-blue-600 font-medium">Subscriptions</li></ol></nav>

        <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
          <div className="relative w-full sm:w-96"><MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" /><input type="text" placeholder="Search subscriptions..." value={search} onChange={e => setSearch(e.target.value)} className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800" /></div>
          <Link href="/subscriptions/create" className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"><PlusIcon className="h-4 w-4" /> New Subscription</Link>
        </div>

        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
          {filtered.map(sub => (
            <div key={sub.id} className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
              <div className="mb-4 flex items-start justify-between">
                <div className="rounded-lg bg-indigo-50 p-3 text-indigo-600"><CreditCardIcon className="h-6 w-6" /></div>
                <span className={`rounded-full px-2.5 py-1 text-xs font-bold ${statusColors[sub.status] || 'bg-gray-100'}`}>{sub.status}</span>
              </div>
              <div className="mb-4 flex-grow">
                <Link href={`/subscriptions/${sub.id}`}><h3 className="truncate text-lg font-bold text-gray-900 hover:text-blue-600 dark:text-neutral-100">{sub.plan_name}</h3></Link>
                <p className="mt-1 text-sm font-medium text-gray-600 dark:text-neutral-400">{sub.tenant_name}</p>
                <p className="mt-2 text-lg font-bold text-gray-900 dark:text-neutral-100">${sub.price}<span className="text-sm font-normal text-gray-500">/{sub.billing_cycle}</span></p>
                <div className="mt-3 flex items-center gap-1.5 text-xs text-gray-500"><CalendarDaysIcon className="h-3.5 w-3.5" />{sub.start_date} — {sub.end_date}</div>
              </div>
              <div className="flex gap-2 border-t border-gray-100 pt-4 opacity-0 transition-opacity group-hover:opacity-100 dark:border-neutral-700">
                <Link href={`/subscriptions/${sub.id}/edit`} className="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gray-50 py-2 text-xs font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-600 dark:bg-neutral-700 dark:text-neutral-400"><PencilSquareIcon className="h-4 w-4" /> Edit</Link>
                <button onClick={() => setDelModal(sub)} className="rounded-lg bg-gray-50 px-3 py-2 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:bg-neutral-700"><TrashIcon className="h-4 w-4" /></button>
              </div>
            </div>
          ))}
        </div>
        {filtered.length === 0 && <div className="text-center py-16"><CreditCardIcon className="mx-auto h-12 w-12 text-gray-300" /><p className="mt-3 text-gray-500 font-medium">No subscriptions found</p></div>}
      </div>

      {delModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4"><div className="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onClick={() => setDelModal(null)} /><div className="relative w-full max-w-md rounded-xl border border-gray-200 bg-white shadow-xl dark:border-neutral-700 dark:bg-neutral-800"><div className="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-neutral-700"><h3 className="text-lg font-semibold">Delete Subscription</h3><button onClick={() => setDelModal(null)} className="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100"><XMarkIcon className="h-5 w-5" /></button></div><div className="p-6 text-center"><ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-red-600" /><p className="text-gray-700 dark:text-neutral-300">Delete <strong>{delModal.plan_name}</strong> subscription?</p></div><div className="flex justify-end gap-3 border-t border-gray-100 px-6 py-4 dark:border-neutral-700"><button onClick={() => setDelModal(null)} className="rounded-lg border px-4 py-2 text-sm">{t('common.cancel')}</button><button onClick={handleDelete} disabled={processing} className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white disabled:opacity-50">{processing ? 'Deleting...' : 'Delete'}</button></div></div></div>
      )}
    </SuperAdminLayout>
  );
}
