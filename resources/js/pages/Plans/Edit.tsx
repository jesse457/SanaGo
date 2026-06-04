import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { HomeIcon, ChevronRightIcon, DocumentTextIcon, ArrowPathIcon, CheckIcon, TagIcon, PlusIcon, TrashIcon } from '@heroicons/react/24/outline';

interface FeatureInput { feature_label: string; is_highlighted: boolean; }
interface Plan { id: number; name: string; slug: string; description: string; monthly_price: string | null; yearly_price: string | null; is_active: boolean; sort_order: number; max_users_per_tenant: number | null; storage_gb: number | null; features_json: FeatureInput[]; }

export default function PlansEdit({ plan }: { plan: Plan }) {
  const { t } = useTranslation();
  const { data, setData, put, processing, errors } = useForm({
    name: plan.name, slug: plan.slug, description: plan.description || '',
    monthly_price: plan.monthly_price ?? '', yearly_price: plan.yearly_price ?? '',
    is_active: plan.is_active, sort_order: plan.sort_order,
    max_users_per_tenant: plan.max_users_per_tenant ?? '', storage_gb: plan.storage_gb ?? '',
    features_json: plan.features_json || [],
  });

  const addFeature = () => setData('features_json', [...data.features_json, { feature_label: '', is_highlighted: false }]);
  const removeFeature = (i: number) => setData('features_json', data.features_json.filter((_, j) => j !== i));
  const submit = (e: FormEvent) => { e.preventDefault(); put(`/plans/${plan.id}`); };

  return (
    <SuperAdminLayout headerTitle={`Edit ${plan.name}`} headerSubtitle="Update subscription plan">
      <Head title={`Edit ${plan.name}`} />
      <div className="max-w-3xl mx-auto space-y-6 px-6">
        <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="hover:text-blue-600 cursor-pointer"><Link href="/plans">Plans</Link></li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="text-blue-600 font-medium">{plan.name}</li></ol></nav>

        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
          <form onSubmit={submit} className="p-6 space-y-8">
            <div className="space-y-4"><h3 className="text-sm font-bold text-blue-600 uppercase tracking-wide border-b border-gray-100 dark:border-neutral-700 pb-2 flex items-center gap-2"><DocumentTextIcon className="w-5 h-5" /> Plan Details</h3>
              <div className="grid grid-cols-2 gap-4">
                <div className="col-span-2 space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Name <span className="text-red-500">*</span></label><input type="text" value={data.name} onChange={e => setData('name', e.target.value)} className={`w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-neutral-100 ${errors.name ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required />{errors.name && <p className="text-xs text-red-500 mt-1">{errors.name}</p>}</div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Slug <span className="text-red-500">*</span></label><input type="text" value={data.slug} onChange={e => setData('slug', e.target.value)} className={`w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-neutral-100 ${errors.slug ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required />{errors.slug && <p className="text-xs text-red-500 mt-1">{errors.slug}</p>}</div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Monthly Price ($)</label><input type="number" step="0.01" value={data.monthly_price} onChange={e => setData('monthly_price', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100" /></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Yearly Price ($)</label><input type="number" step="0.01" value={data.yearly_price} onChange={e => setData('yearly_price', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100" /></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Max Users/Tenant</label><input type="number" value={data.max_users_per_tenant} onChange={e => setData('max_users_per_tenant', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100" /></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Storage (GB)</label><input type="number" value={data.storage_gb} onChange={e => setData('storage_gb', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100" /></div>
              </div>
              <div className="flex items-center gap-3"><label className="text-sm font-medium text-gray-700 dark:text-neutral-300">Active</label><input type="checkbox" checked={data.is_active} onChange={e => setData('is_active', e.target.checked)} className="rounded" /></div>
            </div>

            <div className="space-y-4"><div className="flex items-center justify-between border-b border-gray-100 dark:border-neutral-700 pb-2"><h3 className="text-sm font-bold text-blue-600 uppercase tracking-wide flex items-center gap-2"><TagIcon className="w-5 h-5" /> Features</h3><button type="button" onClick={addFeature} className="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 rounded-lg"><PlusIcon className="w-4 h-4" /> Add</button></div>
              {data.features_json.map((f, i) => (<div key={i} className="flex items-center gap-3"><input type="text" value={f.feature_label} onChange={e => { const n = [...data.features_json]; n[i].feature_label = e.target.value; setData('features_json', n); }} placeholder="Feature label" className="flex-1 px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700" /><label className="flex items-center gap-1 text-xs text-gray-500"><input type="checkbox" checked={f.is_highlighted} onChange={e => { const n = [...data.features_json]; n[i].is_highlighted = e.target.checked; setData('features_json', n); }} className="rounded" /> Highlight</label><button type="button" onClick={() => removeFeature(i)} className="text-red-400 hover:text-red-600"><TrashIcon className="w-4 h-4" /></button></div>))}
            </div>

            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
              <Link href="/plans" className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700">{t('common.cancel')}</Link>
              <button type="submit" disabled={processing} className="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">{processing ? <span className="flex items-center gap-2"><ArrowPathIcon className="animate-spin h-4 w-4" /> Saving...</span> : <span className="flex items-center gap-2"><CheckIcon className="w-4 h-4" /> Update Plan</span>}</button>
            </div>
          </form>
        </div>
      </div>
    </SuperAdminLayout>
  );
}
