import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { HomeIcon, ChevronRightIcon, UserGroupIcon, EnvelopeIcon, LockClosedIcon, ShieldCheckIcon, ArrowPathIcon, CheckIcon } from '@heroicons/react/24/outline';

export default function LandlordUsersCreate({ roles }: { roles: string[] }) {
  const { t } = useTranslation();
  const { data, setData, post, processing, errors } = useForm({ name: '', email: '', password: '', role: roles[0] || '' });
  const submit = (e: FormEvent) => { e.preventDefault(); post('/landlord-users'); };

  return (
    <SuperAdminLayout headerTitle="Add Staff User" headerSubtitle="Create a new landlord organization member">
      <Head title="Add Staff User" />
      <div className="max-w-3xl mx-auto space-y-6 px-6">
        <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="hover:text-blue-600 cursor-pointer"><Link href="/landlord-users">Staff Users</Link></li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="text-blue-600 font-medium">New</li></ol></nav>

        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
          <form onSubmit={submit} className="p-6 space-y-8">
            <div className="space-y-4"><h3 className="text-sm font-bold text-blue-600 uppercase tracking-wide border-b border-gray-100 dark:border-neutral-700 pb-2 flex items-center gap-2"><UserGroupIcon className="w-5 h-5" /> User Details</h3>
              <div className="grid grid-cols-2 gap-4">
                <div className="col-span-2 space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Full Name <span className="text-red-500">*</span></label><div className="relative"><div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><UserGroupIcon className="h-5 w-5 text-gray-400" /></div><input type="text" value={data.name} onChange={e => setData('name', e.target.value)} className={`w-full pl-10 pr-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-neutral-100 ${errors.name ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required /></div>{errors.name && <p className="text-xs text-red-500 mt-1">{errors.name}</p>}</div>
                <div className="col-span-2 space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Email <span className="text-red-500">*</span></label><div className="relative"><div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><EnvelopeIcon className="h-5 w-5 text-gray-400" /></div><input type="email" value={data.email} onChange={e => setData('email', e.target.value)} className={`w-full pl-10 pr-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-neutral-100 ${errors.email ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required /></div>{errors.email && <p className="text-xs text-red-500 mt-1">{errors.email}</p>}</div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Password <span className="text-red-500">*</span></label><div className="relative"><div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><LockClosedIcon className="h-5 w-5 text-gray-400" /></div><input type="password" value={data.password} onChange={e => setData('password', e.target.value)} className={`w-full pl-10 pr-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-neutral-100 ${errors.password ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required /></div>{errors.password && <p className="text-xs text-red-500 mt-1">{errors.password}</p>}</div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Role <span className="text-red-500">*</span></label><div className="relative"><div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><ShieldCheckIcon className="h-5 w-5 text-gray-400" /></div><select value={data.role} onChange={e => setData('role', e.target.value)} className="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100">{roles.map(r => <option key={r} value={r}>{r}</option>)}</select></div></div>
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
              <Link href="/landlord-users" className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700">{t('common.cancel')}</Link>
              <button type="submit" disabled={processing} className="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">{processing ? <span className="flex items-center gap-2"><ArrowPathIcon className="animate-spin h-4 w-4" /> Creating...</span> : <span className="flex items-center gap-2"><CheckIcon className="w-4 h-4" /> Create User</span>}</button>
            </div>
          </form>
        </div>
      </div>
    </SuperAdminLayout>
  );
}
