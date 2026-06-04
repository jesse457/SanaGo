import { Head, Link } from '@inertiajs/react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { ArrowLeftIcon, CheckIcon, CreditCardIcon } from '@heroicons/react/24/outline';

interface Subscription { id: number; plan_name: string; tenant_name: string; billing_cycle: string; price: string; start_date: string; end_date: string; status: string; auto_renew: boolean; features_json: { feature_label: string; is_highlighted: boolean }[]; }

export default function SubscriptionsShow({ subscription: sub }: { subscription: Subscription }) {
  return (
    <SuperAdminLayout headerTitle={sub.plan_name} headerSubtitle="Subscription Details">
      <Head title={`${sub.plan_name} — Subscription`} />
      <Link href="/subscriptions" className="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 mb-6"><ArrowLeftIcon className="w-4 h-4" /> Back to Subscriptions</Link>

      <div className="grid lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6">
          <h2 className="text-lg font-bold mb-4">{sub.plan_name} Plan</h2>
          <dl className="grid sm:grid-cols-2 gap-4 mb-6">
            <div><dt className="text-xs font-bold text-gray-400 uppercase">Tenant</dt><dd className="text-sm font-semibold mt-1">{sub.tenant_name}</dd></div>
            <div><dt className="text-xs font-bold text-gray-400 uppercase">Price</dt><dd className="text-sm font-bold mt-1">${sub.price}/{sub.billing_cycle}</dd></div>
            <div><dt className="text-xs font-bold text-gray-400 uppercase">Status</dt><dd className="mt-1"><span className={`px-2 py-0.5 rounded-full text-xs font-bold ${sub.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'}`}>{sub.status}</span></dd></div>
            <div><dt className="text-xs font-bold text-gray-400 uppercase">Auto Renew</dt><dd className={`text-sm font-medium mt-1 ${sub.auto_renew ? 'text-emerald-600' : 'text-red-500'}`}>{sub.auto_renew ? 'Yes' : 'No'}</dd></div>
            <div><dt className="text-xs font-bold text-gray-400 uppercase">Start</dt><dd className="text-sm mt-1">{sub.start_date}</dd></div>
            <div><dt className="text-xs font-bold text-gray-400 uppercase">End</dt><dd className="text-sm mt-1">{sub.end_date}</dd></div>
          </dl>
          {sub.features_json && sub.features_json.length > 0 && (
            <div className="border-t border-gray-100 dark:border-neutral-800 pt-4">
              <h3 className="text-sm font-bold mb-3">Features</h3>
              <ul className="space-y-2">
                {sub.features_json.map((f, i) => (
                  <li key={i} className={`flex items-center gap-2 text-sm ${f.is_highlighted ? 'font-semibold' : 'text-gray-600'}`}>
                    <CheckIcon className={`w-4 h-4 ${f.is_highlighted ? 'text-blue-600' : 'text-gray-300 dark:text-neutral-600'}`} />{f.feature_label}
                  </li>
                ))}
              </ul>
            </div>
          )}
        </div>

        <div className="space-y-3">
          <Link href={`/subscriptions/${sub.id}/edit`} className="block w-full text-center py-3 text-sm font-bold bg-blue-600 text-white rounded-xl hover:bg-blue-700">Edit Subscription</Link>
          <Link href="/subscriptions" className="block w-full text-center py-3 text-sm font-semibold bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-300 rounded-xl">Back to List</Link>
        </div>
      </div>
    </SuperAdminLayout>
  );
}
