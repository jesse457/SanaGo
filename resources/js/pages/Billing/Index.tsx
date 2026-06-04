import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { HomeIcon, ChevronRightIcon, CreditCardIcon, CurrencyDollarIcon, CalendarDaysIcon, CheckCircleIcon, ArchiveBoxIcon, UserGroupIcon, ClockIcon } from '@heroicons/react/24/outline';

interface Subscription {
  plan_name: string; billing_cycle: string; price: string; start_date: string; end_date: string;
  status: string; auto_renew: boolean; features_json: { feature_label: string; is_highlighted: boolean }[];
}
interface BillingProps { subscription: Subscription | null; tenant_name: string; }

export default function BillingIndex({ subscription: sub, tenant_name }: BillingProps) {
  const { t } = useTranslation();
  const daysRemaining = sub ? Math.max(0, Math.ceil((new Date(sub.end_date).getTime() - Date.now()) / 86400000)) : 0;
  const usagePercent = daysRemaining ? Math.round(((365 - daysRemaining) / 365) * 100) : 0;

  return (
    <AppLayout headerTitle="Billing & Subscription" headerSubtitle={tenant_name}>
      <Head title="Billing" />
      <div className="mx-auto max-w-5xl space-y-6 px-6">
        <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="text-blue-600 font-medium">Billing</li></ol></nav>

        {!sub ? (
          <div className="text-center py-16 bg-white dark:bg-neutral-900 rounded-2xl border"><CreditCardIcon className="mx-auto h-12 w-12 text-gray-300" /><p className="mt-3 text-gray-500 font-medium">No active subscription</p><p className="text-sm text-gray-400 mt-1">Contact your administrator for assistance.</p></div>
        ) : (
          <>
            {/* Stats Row */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div className="bg-white dark:bg-neutral-900 rounded-xl border border-gray-100 dark:border-neutral-800 p-4"><p className="text-xs text-gray-400 uppercase font-bold">Plan</p><p className="mt-1 text-lg font-bold text-indigo-600">{sub.plan_name}</p></div>
              <div className="bg-white dark:bg-neutral-900 rounded-xl border border-gray-100 dark:border-neutral-800 p-4"><p className="text-xs text-gray-400 uppercase font-bold">Price</p><p className="mt-1 text-lg font-bold flex items-center gap-1"><CurrencyDollarIcon className="w-5 h-5 text-gray-500" />{sub.price}<span className="text-sm font-normal text-gray-400">/{sub.billing_cycle}</span></p></div>
              <div className="bg-white dark:bg-neutral-900 rounded-xl border border-gray-100 dark:border-neutral-800 p-4"><p className="text-xs text-gray-400 uppercase font-bold">Status</p><p className={`mt-1 text-lg font-bold ${sub.status === 'active' ? 'text-emerald-600' : 'text-gray-500'}`}><CheckCircleIcon className="w-5 h-5 inline mr-1" />{sub.status}</p></div>
              <div className="bg-white dark:bg-neutral-900 rounded-xl border border-gray-100 dark:border-neutral-800 p-4"><p className="text-xs text-gray-400 uppercase font-bold">Auto Renew</p><p className={`mt-1 text-lg font-bold ${sub.auto_renew ? 'text-emerald-600' : 'text-red-500'}`}>{sub.auto_renew ? 'Yes' : 'No'}</p></div>
            </div>

            <div className="grid lg:grid-cols-3 gap-6">
              {/* Plan Details */}
              <div className="lg:col-span-2 bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6">
                <h2 className="text-lg font-bold mb-4 flex items-center gap-2"><CreditCardIcon className="w-5 h-5 text-indigo-600" /> Plan Details</h2>
                <dl className="grid sm:grid-cols-2 gap-4">
                  <div><dt className="text-xs font-bold text-gray-400 uppercase">Start Date</dt><dd className="text-sm font-semibold mt-1 flex items-center gap-1.5"><CalendarDaysIcon className="w-4 h-4 text-gray-400" />{sub.start_date}</dd></div>
                  <div><dt className="text-xs font-bold text-gray-400 uppercase">End Date</dt><dd className="text-sm font-semibold mt-1 flex items-center gap-1.5"><CalendarDaysIcon className="w-4 h-4 text-gray-400" />{sub.end_date}</dd></div>
                  <div><dt className="text-xs font-bold text-gray-400 uppercase">Days Remaining</dt><dd className={`text-sm font-bold mt-1 flex items-center gap-1.5 ${daysRemaining < 30 ? 'text-red-500' : 'text-emerald-600'}`}><ClockIcon className="w-4 h-4" />{daysRemaining} days</dd></div>
                  <div><dt className="text-xs font-bold text-gray-400 uppercase">Billing Cycle</dt><dd className="text-sm font-semibold mt-1 capitalize">{sub.billing_cycle}</dd></div>
                </dl>
                <div className="mt-4"><div className="flex justify-between text-xs text-gray-400 mb-1"><span>Period Usage ({usagePercent}%)</span><span>{365 - daysRemaining} of 365 days</span></div><div className="w-full bg-gray-100 dark:bg-neutral-700 rounded-full h-2.5"><div className={`h-2.5 rounded-full transition-all ${usagePercent > 90 ? 'bg-red-500' : 'bg-blue-600'}`} style={{ width: `${Math.min(usagePercent, 100)}%` }} /></div></div>
              </div>

              {/* Features */}
              <div className="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6">
                <h3 className="text-lg font-bold mb-4">Your Plan Features</h3>
                {sub.features_json && sub.features_json.length > 0 ? (
                  <ul className="space-y-2.5">{sub.features_json.map((f, i) => (<li key={i} className={`flex items-center gap-2 text-sm ${f.is_highlighted ? 'font-semibold text-gray-900 dark:text-neutral-100' : 'text-gray-500 dark:text-neutral-400'}`}><CheckCircleIcon className={`w-4 h-4 ${f.is_highlighted ? 'text-blue-600' : 'text-gray-300'}`} />{f.feature_label}</li>))}</ul>
                ) : <p className="text-sm text-gray-400">No feature details available.</p>}
              </div>
            </div>
          </>
        )}
      </div>
    </AppLayout>
  );
}
