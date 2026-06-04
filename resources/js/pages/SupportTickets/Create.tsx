import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { ArrowLeftIcon } from '@heroicons/react/24/outline';

interface Tenant { id: string; name: string; }

export default function SupportTicketsCreate({ tenants }: { tenants: Tenant[] }) {
  const [form, setForm] = useState({ tenant_id: '', subject: '', priority: 'medium', message: '' });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = (e: React.FormEvent) => { e.preventDefault(); setSubmitting(true); router.post('/support-tickets', form, { onError: (err) => { setErrors(err as any); setSubmitting(false); } }); };

  return (
    <SuperAdminLayout headerTitle="New Ticket" headerSubtitle="Create a support ticket">
      <Head title="New Ticket" />
      <Link href="/support-tickets" className="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 mb-6"><ArrowLeftIcon className="w-4 h-4" /> Back to Tickets</Link>

      <form onSubmit={handleSubmit} className="max-w-2xl bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6 space-y-5">
        <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Tenant</label><select value={form.tenant_id} onChange={e => setForm({...form, tenant_id: e.target.value})} className="w-full rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" required><option value="">Select tenant...</option>{tenants.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}</select>{errors.tenant_id && <p className="text-xs text-red-500 mt-1">{errors.tenant_id}</p>}</div>
        <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Subject</label><input type="text" value={form.subject} onChange={e => setForm({...form, subject: e.target.value})} className="w-full rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" required />{errors.subject && <p className="text-xs text-red-500 mt-1">{errors.subject}</p>}</div>
        <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Priority</label><select value={form.priority} onChange={e => setForm({...form, priority: e.target.value})} className="w-full rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option><option value="urgent">Urgent</option></select></div>
        <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Message</label><textarea value={form.message} onChange={e => setForm({...form, message: e.target.value})} rows={5} className="w-full rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" required />{errors.message && <p className="text-xs text-red-500 mt-1">{errors.message}</p>}</div>
        <div className="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
          <Link href="/support-tickets" className="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-300">Cancel</Link>
          <button type="submit" disabled={submitting} className="px-6 py-2 text-sm font-bold rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">{submitting ? 'Creating...' : 'Create Ticket'}</button>
        </div>
      </form>
    </SuperAdminLayout>
  );
}
