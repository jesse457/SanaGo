import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { PlusIcon, PencilSquareIcon, TrashIcon, BuildingOfficeIcon, GlobeAltIcon, MagnifyingGlassIcon, ExclamationTriangleIcon, XMarkIcon, HomeIcon, ChevronRightIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/react/24/outline';

interface TenantItem { id: string; slug: string; name: string; domains: string[]; is_active: boolean; suspended_at: string | null; created_at: string; subscription: { plan_name: string; status: string } | null; }

export default function TenantsIndex({ tenants }: { tenants: TenantItem[] }) {
  const { t } = useTranslation();
  const [search, setSearch] = useState('');
  const [delModal, setDelModal] = useState<TenantItem | null>(null);
  const { delete: destroy, processing } = useForm();
  const filtered = useMemo(() => tenants.filter(t => t.name.toLowerCase().includes(search.toLowerCase()) || t.slug.toLowerCase().includes(search.toLowerCase())), [tenants, search]);

  const handleDelete = (e: FormEvent) => { e.preventDefault(); if (delModal) destroy(`/tenants/${delModal.id}`, { onSuccess: () => setDelModal(null) }); };

  return (
    <SuperAdminLayout headerTitle="Tenants" headerSubtitle="Manage hospital tenants">
      <Head title="Tenants" />
      <div className="mx-auto max-w-7xl space-y-6 px-6">
        <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="mr-1 h-4 w-4" /> {t('common.home')}</li><li><ChevronRightIcon className="h-4 w-4" /></li><li className="text-blue-600 font-medium">Tenants</li></ol></nav>

        <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
          <div className="relative w-full sm:w-96"><MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" /><input type="text" placeholder="Search tenants..." value={search} onChange={e => setSearch(e.target.value)} className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800" /></div>
          <Link href="/tenants/create" className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"><PlusIcon className="h-4 w-4" /> New Tenant</Link>
        </div>

        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
          {filtered.map(tenant => (
            <div key={tenant.id} className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
              <div className="mb-4 flex items-start justify-between">
                <div className={`rounded-lg p-3 ${tenant.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500'}`}><BuildingOfficeIcon className="h-6 w-6" /></div>
                <span className={`inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold ${tenant.is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400'}`}>{tenant.is_active ? <CheckCircleIcon className="w-3.5 h-3.5" /> : <XCircleIcon className="w-3.5 h-3.5" />}{tenant.is_active ? 'Active' : 'Suspended'}</span>
              </div>
              <div className="mb-4 flex-grow">
                <Link href={`/tenants/${tenant.id}`}><h3 className="truncate text-lg font-bold text-gray-900 hover:text-blue-600 dark:text-neutral-100">{tenant.name}</h3></Link>
                <span className="mt-1 inline-block rounded bg-gray-50 px-1.5 py-0.5 font-mono text-xs text-gray-400 dark:bg-neutral-700">{tenant.slug}</span>
                <div className="mt-3 flex items-center gap-1.5 text-xs text-gray-500"><GlobeAltIcon className="h-3.5 w-3.5" />{tenant.domains[0] || 'No domain'}</div>
                {tenant.subscription && <span className={`mt-2 inline-block rounded-full px-2 py-0.5 text-xs font-bold ${tenant.subscription.status === 'active' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-gray-100 text-gray-600'}`}>{tenant.subscription.plan_name}</span>}
              </div>
              <div className="flex gap-2 border-t border-gray-100 pt-4 opacity-0 transition-opacity group-hover:opacity-100 dark:border-neutral-700">
                <Link href={`/tenants/${tenant.id}/edit`} className="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gray-50 py-2 text-xs font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-600 dark:bg-neutral-700 dark:text-neutral-400"><PencilSquareIcon className="h-4 w-4" /> Edit</Link>
                <button onClick={() => setDelModal(tenant)} className="rounded-lg bg-gray-50 px-3 py-2 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:bg-neutral-700"><TrashIcon className="h-4 w-4" /></button>
              </div>
            </div>
          ))}
        </div>
        {filtered.length === 0 && <div className="text-center py-16"><BuildingOfficeIcon className="mx-auto h-12 w-12 text-gray-300" /><p className="mt-3 text-gray-500 font-medium">No tenants found</p></div>}
      </div>

      {delModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4"><div className="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onClick={() => setDelModal(null)} /><div className="relative w-full max-w-md rounded-xl border border-gray-200 bg-white shadow-xl dark:border-neutral-700 dark:bg-neutral-800"><div className="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-neutral-700"><h3 className="text-lg font-semibold">Delete Tenant</h3><button onClick={() => setDelModal(null)} className="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100"><XMarkIcon className="h-5 w-5" /></button></div><div className="p-6 text-center"><ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-red-600" /><p className="text-gray-700 dark:text-neutral-300">Delete <strong>{delModal.name}</strong> permanently?</p></div><div className="flex justify-end gap-3 border-t border-gray-100 px-6 py-4 dark:border-neutral-700"><button onClick={() => setDelModal(null)} className="rounded-lg border px-4 py-2 text-sm">{t('common.cancel')}</button><button onClick={handleDelete} disabled={processing} className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white disabled:opacity-50">{processing ? 'Deleting...' : 'Delete'}</button></div></div></div>
      )}
    </SuperAdminLayout>
  );
}
