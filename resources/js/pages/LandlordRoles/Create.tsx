import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { HomeIcon, ChevronRightIcon, ShieldCheckIcon, ArrowPathIcon, CheckIcon, KeyIcon } from '@heroicons/react/24/outline';

export default function LandlordRolesCreate({ permissions }: { permissions: string[] }) {
  const { t } = useTranslation();
  const { data, setData, post, processing, errors } = useForm({ name: '', permissions: [] as string[] });

  const togglePerm = (p: string) => setData('permissions', data.permissions.includes(p) ? data.permissions.filter(x => x !== p) : [...data.permissions, p]);
  const submit = (e: FormEvent) => { e.preventDefault(); post('/landlord-roles'); };

  return (
    <SuperAdminLayout headerTitle="Create Role" headerSubtitle="Define a new landlord role">
      <Head title="Create Role" />
      <div className="max-w-3xl mx-auto space-y-6 px-6">
        <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="hover:text-blue-600 cursor-pointer">Roles</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="text-blue-600 font-medium">New</li></ol></nav>

        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
          <form onSubmit={submit} className="p-6 space-y-8">
            <div className="space-y-4"><h3 className="text-sm font-bold text-blue-600 uppercase tracking-wide border-b border-gray-100 dark:border-neutral-700 pb-2 flex items-center gap-2"><ShieldCheckIcon className="w-5 h-5" /> Role Details</h3>
              <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Role Name <span className="text-red-500">*</span></label><input type="text" value={data.name} onChange={e => setData('name', e.target.value)} className={`w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-neutral-100 ${errors.name ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required />{errors.name && <p className="text-xs text-red-500 mt-1">{errors.name}</p>}</div>
            </div>

            <div className="space-y-4"><h4 className="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-neutral-100"><KeyIcon className="w-4 h-4 text-blue-600" /> Permissions</h4>
              <div className="grid grid-cols-2 gap-3">
                {permissions.map(p => (
                  <label key={p} className={`flex cursor-pointer items-start rounded-lg border p-3 transition-all ${data.permissions.includes(p) ? 'border-blue-200 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800' : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-neutral-700 dark:hover:bg-neutral-700'}`}>
                    <input type="checkbox" checked={data.permissions.includes(p)} onChange={() => togglePerm(p)} className="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600" />
                    <span className="ml-3 text-sm font-medium text-gray-900 dark:text-neutral-100">{p}</span>
                  </label>
                ))}
              </div>
            </div>

            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
              <Link href="/landlord-roles" className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700">{t('common.cancel')}</Link>
              <button type="submit" disabled={processing} className="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">{processing ? <span className="flex items-center gap-2"><ArrowPathIcon className="animate-spin h-4 w-4" /> Creating...</span> : <span className="flex items-center gap-2"><CheckIcon className="w-4 h-4" /> Create Role</span>}</button>
            </div>
          </form>
        </div>
      </div>
    </SuperAdminLayout>
  );
}
