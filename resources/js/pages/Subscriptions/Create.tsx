import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { HomeIcon, ChevronRightIcon, CreditCardIcon, CurrencyDollarIcon, CalendarDaysIcon, ArrowPathIcon, CheckIcon, BuildingOfficeIcon, CheckCircleIcon } from '@heroicons/react/24/outline';

interface TenantOption { id: string; name: string; }
interface PlanOption { id: number; name: string; monthly_price: string | null; yearly_price: string | null; features_json: { feature_label: string; is_highlighted: boolean }[]; }

export default function SubscriptionsCreate({ tenants, plans }: { tenants: TenantOption[]; plans: PlanOption[] }) {
  const { t } = useTranslation();
  const { data, setData, post, processing, errors } = useForm({
    tenant_id: '', plan_id: '', plan_name: '', billing_cycle: 'monthly', price: '',
    start_date: new Date().toISOString().split('T')[0], end_date: '', status: 'active',
  });

  const selectedPlan = plans.find(p => p.id === Number(data.plan_id));

  const selectPlan = (planId: string) => {
    const plan = plans.find(p => p.id === Number(planId));
    if (plan) {
      const price = plan.monthly_price ?? plan.yearly_price ?? '';
      const cycle = plan.monthly_price ? 'monthly' : 'yearly';
      setData({ ...data, plan_id: planId, plan_name: plan.name, price: String(price), billing_cycle: cycle });
    }
  };

  const submit = (e: FormEvent) => { e.preventDefault(); post('/subscriptions'); };

  return (
    <SuperAdminLayout headerTitle="New Subscription" headerSubtitle="Create a tenant subscription">
      <Head title="New Subscription" />
      <div className="max-w-3xl mx-auto space-y-6 px-6">
        <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="hover:text-blue-600 cursor-pointer"><Link href="/subscriptions">Subscriptions</Link></li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="text-blue-600 font-medium">New</li></ol></nav>

        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
          <form onSubmit={submit} className="p-6 space-y-8">
            {/* Tenant & Plan Selection */}
            <div className="space-y-4"><h3 className="text-sm font-bold text-blue-600 uppercase tracking-wide border-b border-gray-100 dark:border-neutral-700 pb-2 flex items-center gap-2"><BuildingOfficeIcon className="w-5 h-5" /> Tenant & Plan</h3>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Tenant <span className="text-red-500">*</span></label><select value={data.tenant_id} onChange={e => setData('tenant_id', e.target.value)} className={`w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-neutral-100 ${errors.tenant_id ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required><option value="">Select tenant...</option>{tenants.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}</select>{errors.tenant_id && <p className="text-xs text-red-500 mt-1">{errors.tenant_id}</p>}</div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Plan <span className="text-gray-400 text-xs">(auto-fills pricing)</span></label><select value={data.plan_id} onChange={e => selectPlan(e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100"><option value="">Custom / Manual</option>{plans.map(p => <option key={p.id} value={p.id}>{p.name}</option>)}</select></div>
              </div>
              {selectedPlan && (
                <div className="p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg border border-blue-100 dark:border-blue-800">
                  <p className="text-sm font-bold text-blue-700 dark:text-blue-400 mb-2">{selectedPlan.name} Features:</p>
                  <div className="flex flex-wrap gap-1.5">{selectedPlan.features_json.filter(f => f.is_highlighted).map((f, i) => <span key={i} className="rounded bg-blue-100 dark:bg-blue-900/30 px-2 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-400"><CheckCircleIcon className="w-3 h-3 inline mr-1" />{f.feature_label}</span>)}</div>
                </div>
              )}
            </div>

            {/* Billing Details */}
            <div className="space-y-4"><h3 className="text-sm font-bold text-blue-600 uppercase tracking-wide border-b border-gray-100 dark:border-neutral-700 pb-2 flex items-center gap-2"><CurrencyDollarIcon className="w-5 h-5" /> Billing Details</h3>
              <div className="grid grid-cols-3 gap-4">
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Plan Name</label><input type="text" value={data.plan_name} onChange={e => setData('plan_name', e.target.value)} className={`w-full px-3 py-2 border rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100 ${errors.plan_name ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} /></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Billing Cycle</label><select value={data.billing_cycle} onChange={e => setData('billing_cycle', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100"><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Price ($) <span className="text-red-500">*</span></label><div className="relative"><div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><CurrencyDollarIcon className="h-5 w-5 text-gray-400" /></div><input type="number" step="0.01" value={data.price} onChange={e => setData('price', e.target.value)} className={`w-full pl-10 pr-3 py-2 border rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100 ${errors.price ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required /></div>{errors.price && <p className="text-xs text-red-500 mt-1">{errors.price}</p>}</div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Start Date <span className="text-red-500">*</span></label><div className="relative"><div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><CalendarDaysIcon className="h-5 w-5 text-gray-400" /></div><input type="date" value={data.start_date} onChange={e => setData('start_date', e.target.value)} className={`w-full pl-10 pr-3 py-2 border rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100 ${errors.start_date ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required /></div>{errors.start_date && <p className="text-xs text-red-500 mt-1">{errors.start_date}</p>}</div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">End Date</label><input type="date" value={data.end_date} onChange={e => setData('end_date', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100" /></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Status</label><select value={data.status} onChange={e => setData('status', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100"><option value="active">Active</option><option value="inactive">Inactive</option><option value="cancelled">Cancelled</option></select></div>
              </div>
            </div>

            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
              <Link href="/subscriptions" className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700">{t('common.cancel')}</Link>
              <button type="submit" disabled={processing} className="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">{processing ? <span className="flex items-center gap-2"><ArrowPathIcon className="animate-spin h-4 w-4" /> Creating...</span> : <span className="flex items-center gap-2"><CheckIcon className="w-4 h-4" /> Create Subscription</span>}</button>
            </div>
          </form>
        </div>
      </div>
    </SuperAdminLayout>
  );
}
