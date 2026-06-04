import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { HomeIcon, ChevronRightIcon, BuildingOfficeIcon, GlobeAltIcon, DocumentTextIcon, ArrowPathIcon, CheckIcon } from '@heroicons/react/24/outline';

interface TenantEditProps { tenant: { id: string; slug: string; name: string; domains: string[] }; }

export default function TenantsEdit({ tenant }: TenantEditProps) {
  const { t } = useTranslation();
  const { data, setData, put, processing, errors } = useForm({ name: tenant.name });

  const submit = (e: FormEvent) => { e.preventDefault(); put(`/tenants/${tenant.id}`); };

  return (
    <SuperAdminLayout headerTitle={`Edit ${tenant.name}`} headerSubtitle="Update tenant information">
      <Head title={`Edit ${tenant.name}`} />
      <div className="max-w-3xl mx-auto space-y-6 px-6">
        <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="hover:text-blue-600 cursor-pointer"><Link href="/tenants">Tenants</Link></li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="text-blue-600 font-medium">{tenant.name}</li></ol></nav>

        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
          <form onSubmit={submit} className="p-6 space-y-8">
            <div className="space-y-4"><h3 className="text-sm font-bold text-blue-600 uppercase tracking-wide border-b border-gray-100 dark:border-neutral-700 pb-2 flex items-center gap-2"><BuildingOfficeIcon className="w-5 h-5" /> Hospital Information</h3>
              <div className="grid grid-cols-2 gap-4">
                <div className="col-span-2 space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Hospital Name <span className="text-red-500">*</span></label><div className="relative"><div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><DocumentTextIcon className="h-5 w-5 text-gray-400" /></div><input type="text" value={data.name} onChange={e => setData('name', e.target.value)} className={`w-full pl-10 pr-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-neutral-100 ${errors.name ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required /></div>{errors.name && <p className="text-xs text-red-500 mt-1">{errors.name}</p>}</div>
              </div>
              <div className="grid grid-cols-2 gap-4 pt-2">
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-400">Slug</label><input type="text" value={tenant.slug} disabled className="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm bg-gray-50 dark:bg-neutral-700 text-gray-400 cursor-not-allowed" /></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-400">Domain</label><input type="text" value={tenant.domains[0] || ''} disabled className="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm bg-gray-50 dark:bg-neutral-700 text-gray-400 cursor-not-allowed" /></div>
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
              <Link href="/tenants" className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700">{t('common.cancel')}</Link>
              <button type="submit" disabled={processing} className="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">{processing ? <span className="flex items-center gap-2"><ArrowPathIcon className="animate-spin h-4 w-4" /> Saving...</span> : <span className="flex items-center gap-2"><CheckIcon className="w-4 h-4" /> Update Tenant</span>}</button>
            </div>
          </form>
        </div>
      </div>
    </SuperAdminLayout>
  );
}
