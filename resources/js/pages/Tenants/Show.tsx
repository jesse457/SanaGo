import { Head, Link, router } from '@inertiajs/react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { HomeIcon, ChevronRightIcon, BuildingOfficeIcon, GlobeAltIcon, CalendarDaysIcon, CreditCardIcon, PencilSquareIcon, ArrowLeftIcon, CheckCircleIcon, XCircleIcon, ExclamationTriangleIcon, ClockIcon, UserGroupIcon, ArchiveBoxIcon, CurrencyDollarIcon } from '@heroicons/react/24/outline';

interface Subscription {
  id: number; plan_name: string; plan: { id: number; name: string } | null;
  billing_cycle: string; price: string; start_date: string; end_date: string;
  status: string; auto_renew: boolean; features_json: { feature_label: string; is_highlighted: boolean }[];
}
interface TenantNote { id: number; note: string; user_name: string; created_at: string; }
interface Tenant {
  id: string; slug: string; name: string; domains: string[]; is_active: boolean;
  suspended_at: string | null; trial_ends_at: string | null;
  created_at: string; updated_at: string;
  subscription: Subscription | null; notes: TenantNote[];
}

export default function TenantShow({ tenant }: { tenant: Tenant }) {
  const { t } = useTranslation();
  const sub = tenant.subscription;

  const handleToggleActive = () => { if (confirm(tenant.is_active ? 'Suspend this tenant?' : 'Reactivate this tenant?')) router.patch(`/tenants/${tenant.id}/toggle-active`); };
  const handleDelete = () => { if (confirm('Delete this tenant permanently?')) router.delete(`/tenants/${tenant.id}`); };

  const daysRemaining = sub ? Math.max(0, Math.ceil((new Date(sub.end_date).getTime() - Date.now()) / 86400000)) : 0;
  const usagePercent = daysRemaining && sub ? Math.round(((365 - daysRemaining) / 365) * 100) : 0;

  return (
    <SuperAdminLayout headerTitle={tenant.name} headerSubtitle="Tenant Details">
      <Head title={`${tenant.name} — Tenant`} />
      <div className="mx-auto max-w-7xl space-y-6 px-6">
        <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="hover:text-blue-600 cursor-pointer"><Link href="/tenants">Tenants</Link></li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="text-blue-600 font-medium">{tenant.name}</li></ol></nav>

        {/* Stats Row */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div className="bg-white dark:bg-neutral-900 rounded-xl border border-gray-100 dark:border-neutral-800 p-4"><p className="text-xs text-gray-400 uppercase font-bold">Status</p><p className={`mt-1 text-lg font-bold flex items-center gap-1.5 ${tenant.is_active ? 'text-emerald-600' : 'text-red-500'}`}>{tenant.is_active ? <CheckCircleIcon className="w-5 h-5" /> : <XCircleIcon className="w-5 h-5" />}{tenant.is_active ? 'Active' : 'Suspended'}</p></div>
          <div className="bg-white dark:bg-neutral-900 rounded-xl border border-gray-100 dark:border-neutral-800 p-4"><p className="text-xs text-gray-400 uppercase font-bold">Domain</p><p className="mt-1 text-lg font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1.5"><GlobeAltIcon className="w-5 h-5" />{tenant.domains[0] || '—'}</p></div>
          <div className="bg-white dark:bg-neutral-900 rounded-xl border border-gray-100 dark:border-neutral-800 p-4"><p className="text-xs text-gray-400 uppercase font-bold">Created</p><p className="mt-1 text-lg font-bold flex items-center gap-1.5"><CalendarDaysIcon className="w-5 h-5 text-gray-500" />{new Date(tenant.created_at).toLocaleDateString()}</p></div>
          <div className="bg-white dark:bg-neutral-900 rounded-xl border border-gray-100 dark:border-neutral-800 p-4"><p className="text-xs text-gray-400 uppercase font-bold">Subscription</p><p className={`mt-1 text-lg font-bold ${sub ? 'text-indigo-600' : 'text-gray-400'}`}><CreditCardIcon className="w-5 h-5 inline mr-1" />{sub ? sub.plan_name : 'None'}</p></div>
        </div>

        <div className="grid lg:grid-cols-3 gap-6">
          {/* Main Info */}
          <div className="lg:col-span-2 space-y-6">
            <div className="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6">
              <div className="flex items-center justify-between mb-6"><h2 className="text-lg font-bold">Hospital Information</h2><div className="flex gap-2"><Link href={`/tenants/${tenant.id}/edit`} className="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-lg bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-300 hover:bg-gray-200 dark:hover:bg-neutral-700"><PencilSquareIcon className="w-4 h-4" /> Edit</Link></div></div>
              <dl className="grid sm:grid-cols-2 gap-4">
                <div><dt className="text-xs font-bold text-gray-400 uppercase">Name</dt><dd className="text-sm font-semibold mt-1">{tenant.name}</dd></div>
                <div><dt className="text-xs font-bold text-gray-400 uppercase">Slug</dt><dd className="text-sm font-mono mt-1">{tenant.slug}</dd></div>
                <div className="sm:col-span-2"><dt className="text-xs font-bold text-gray-400 uppercase">Domain</dt><dd className="text-sm font-mono text-blue-600 dark:text-blue-400 mt-1">{tenant.domains[0] || 'No domain'}</dd></div>
                <div><dt className="text-xs font-bold text-gray-400 uppercase">Created</dt><dd className="text-sm mt-1">{new Date(tenant.created_at).toLocaleDateString()}</dd></div>
                <div><dt className="text-xs font-bold text-gray-400 uppercase">Trial Ends</dt><dd className="text-sm mt-1">{tenant.trial_ends_at || '—'}</dd></div>
                {tenant.suspended_at && <div><dt className="text-xs font-bold text-gray-400 uppercase">Suspended At</dt><dd className="text-sm text-red-500 mt-1">{new Date(tenant.suspended_at).toLocaleString()}</dd></div>}
              </dl>
              <div className="flex gap-3 mt-6 pt-6 border-t border-gray-100 dark:border-neutral-800">
                <button onClick={handleToggleActive} className={`px-4 py-2 text-sm font-semibold rounded-lg ${tenant.is_active ? 'bg-amber-100 text-amber-700 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400'}`}>{tenant.is_active ? 'Suspend Tenant' : 'Reactivate Tenant'}</button>
                <button onClick={handleDelete} className="px-4 py-2 text-sm font-semibold rounded-lg bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400">Delete Tenant</button>
              </div>
            </div>

            {tenant.notes.length > 0 && (
              <div className="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6"><h3 className="text-lg font-bold mb-4">Internal Notes</h3><div className="space-y-3">{tenant.notes.map(n => (<div key={n.id} className="p-3 bg-gray-50 dark:bg-neutral-800 rounded-lg"><p className="text-sm">{n.note}</p><p className="text-xs text-gray-400 mt-1.5">{n.user_name} — {new Date(n.created_at).toLocaleString()}</p></div>))}</div></div>
            )}
          </div>

          {/* Subscription Sidebar */}
          <div className="space-y-6">
            {sub ? (
              <div className="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6">
                <h3 className="text-lg font-bold mb-4 flex items-center gap-2"><CreditCardIcon className="w-5 h-5 text-indigo-600" /> Subscription</h3>
                <div className="space-y-3">
                  <div className="flex justify-between"><span className="text-sm text-gray-500">Plan</span><span className="text-sm font-bold">{sub.plan_name}</span></div>
                  <div className="flex justify-between"><span className="text-sm text-gray-500">Price</span><span className="text-sm font-bold text-indigo-600">${sub.price}<span className="text-xs font-normal text-gray-400">/{sub.billing_cycle}</span></span></div>
                  <div className="flex justify-between"><span className="text-sm text-gray-500">Status</span><span className={`px-2 py-0.5 rounded-full text-xs font-bold ${sub.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'}`}>{sub.status}</span></div>
                  <hr className="border-gray-100 dark:border-neutral-700" />
                  <div className="flex justify-between"><span className="text-sm text-gray-500">Start Date</span><span className="text-sm font-medium">{sub.start_date}</span></div>
                  <div className="flex justify-between"><span className="text-sm text-gray-500">End Date</span><span className="text-sm font-medium">{sub.end_date}</span></div>
                  <div className="flex justify-between"><span className="text-sm text-gray-500">Days Remaining</span><span className={`text-sm font-bold ${daysRemaining < 30 ? 'text-red-500' : 'text-emerald-600'}`}>{daysRemaining} days</span></div>
                  <div className="flex justify-between"><span className="text-sm text-gray-500">Auto Renew</span><span className={`text-sm font-medium ${sub.auto_renew ? 'text-emerald-600' : 'text-red-500'}`}>{sub.auto_renew ? 'Yes' : 'No'}</span></div>
                  <div className="mt-2"><div className="flex justify-between text-xs text-gray-400 mb-1"><span>Usage ({usagePercent}%)</span><span>{365 - daysRemaining} / 365 days</span></div><div className="w-full bg-gray-100 dark:bg-neutral-700 rounded-full h-2"><div className={`h-2 rounded-full transition-all ${usagePercent > 90 ? 'bg-red-500' : 'bg-blue-600'}`} style={{ width: `${Math.min(usagePercent, 100)}%` }} /></div></div>
                </div>

                {sub.features_json && sub.features_json.length > 0 && (
                  <div className="mt-4 pt-4 border-t border-gray-100 dark:border-neutral-700">
                    <p className="text-xs font-bold text-gray-400 uppercase mb-2">Features</p>
                    <ul className="space-y-1.5">{sub.features_json.map((f, i) => (<li key={i} className={`text-xs flex items-center gap-1.5 ${f.is_highlighted ? 'font-semibold' : 'text-gray-500'}`}><CheckCircleIcon className={`w-3.5 h-3.5 ${f.is_highlighted ? 'text-blue-600' : 'text-gray-300'}`} />{f.feature_label}</li>))}</ul>
                  </div>
                )}

                <Link href={`/subscriptions/${sub.id}/edit`} className="block mt-4 text-center text-sm font-semibold text-blue-600 hover:text-blue-700 py-2 rounded-lg bg-blue-50 dark:bg-blue-900/20">Manage Subscription</Link>
              </div>
            ) : (
              <div className="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6 text-center"><ExclamationTriangleIcon className="w-10 h-10 text-gray-300 mx-auto mb-2" /><p className="text-sm text-gray-500">No active subscription</p><Link href="/subscriptions/create" className="inline-block mt-3 text-sm font-bold text-blue-600">Create Subscription</Link></div>
            )}
          </div>
        </div>
      </div>
    </SuperAdminLayout>
  );
}
