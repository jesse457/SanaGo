import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { HomeIcon, ChevronRightIcon, CreditCardIcon, CurrencyDollarIcon, CalendarDaysIcon, ArrowPathIcon, CheckIcon } from '@heroicons/react/24/outline';

interface TenantOption { id: string; name: string; }
interface SubscriptionData { id: number; tenant_id: string; plan_name: string; billing_cycle: string; price: number; start_date: string; end_date: string | null; status: string; features_json: any[]; }

export default function SubscriptionsEdit({ subscription, tenants }: { subscription: SubscriptionData; tenants: TenantOption[] }) {
  const { t } = useTranslation();
  const { data, setData, put, processing, errors } = useForm({
    tenant_id: subscription.tenant_id, plan_name: subscription.plan_name,
    billing_cycle: subscription.billing_cycle, price: String(subscription.price),
    start_date: subscription.start_date, end_date: subscription.end_date || '',
    status: subscription.status, features_json: subscription.features_json || [],
  });

  const submit = (e: FormEvent) => { e.preventDefault(); put(`/subscriptions/${subscription.id}`); };

  return (
    <SuperAdminLayout headerTitle={`Edit ${subscription.plan_name}`} headerSubtitle="Update subscription">
      <Head title={`Edit ${subscription.plan_name}`} />
      <div className="max-w-3xl mx-auto space-y-6 px-6">
        <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="hover:text-blue-600 cursor-pointer"><Link href="/subscriptions">Subscriptions</Link></li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="text-blue-600 font-medium">{subscription.plan_name}</li></ol></nav>

        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
          <form onSubmit={submit} className="p-6 space-y-8">
            <div className="space-y-4"><h3 className="text-sm font-bold text-blue-600 uppercase tracking-wide border-b border-gray-100 dark:border-neutral-700 pb-2 flex items-center gap-2"><CreditCardIcon className="w-5 h-5" /> Subscription Details</h3>
              <div className="grid grid-cols-2 gap-4">
                <div className="col-span-2 space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Tenant</label><select value={data.tenant_id} onChange={e => setData('tenant_id', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100"><option value="">Select tenant...</option>{tenants.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}</select></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Plan Name</label><input type="text" value={data.plan_name} onChange={e => setData('plan_name', e.target.value)} className={`w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-neutral-100 ${errors.plan_name ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required />{errors.plan_name && <p className="text-xs text-red-500 mt-1">{errors.plan_name}</p>}</div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Billing Cycle</label><select value={data.billing_cycle} onChange={e => setData('billing_cycle', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100"><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Price ($)</label><div className="relative"><div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><CurrencyDollarIcon className="h-5 w-5 text-gray-400" /></div><input type="number" step="0.01" value={data.price} onChange={e => setData('price', e.target.value)} className="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100" /></div></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Status</label><select value={data.status} onChange={e => setData('status', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100"><option value="active">Active</option><option value="inactive">Inactive</option><option value="cancelled">Cancelled</option></select></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Start Date</label><input type="date" value={data.start_date} onChange={e => setData('start_date', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100" /></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">End Date</label><input type="date" value={data.end_date} onChange={e => setData('end_date', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100" /></div>
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
              <Link href="/subscriptions" className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700">{t('common.cancel')}</Link>
              <button type="submit" disabled={processing} className="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">{processing ? <span className="flex items-center gap-2"><ArrowPathIcon className="animate-spin h-4 w-4" /> Saving...</span> : <span className="flex items-center gap-2"><CheckIcon className="w-4 h-4" /> Update</span>}</button>
            </div>
          </form>
        </div>
      </div>
    </SuperAdminLayout>
  );
}
