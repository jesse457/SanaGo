import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { ArrowLeftIcon } from '@heroicons/react/24/outline';

export default function AnnouncementsCreate() {
  const [form, setForm] = useState({ title: '', body: '', type: 'info', is_published: false, expires_at: '' });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = (e: React.FormEvent) => { e.preventDefault(); setSubmitting(true); router.post('/announcements', form, { onError: (err) => { setErrors(err as any); setSubmitting(false); } }); };

  return (
    <SuperAdminLayout headerTitle="New Announcement" headerSubtitle="Create a system-wide announcement">
      <Head title="New Announcement" />
      <Link href="/announcements" className="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 mb-6"><ArrowLeftIcon className="w-4 h-4" /> Back</Link>

      <form onSubmit={handleSubmit} className="max-w-2xl bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6 space-y-5">
        <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Title</label><input type="text" value={form.title} onChange={e => setForm({...form, title: e.target.value})} className="w-full rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" required />{errors.title && <p className="text-xs text-red-500 mt-1">{errors.title}</p>}</div>
        <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Body</label><textarea value={form.body} onChange={e => setForm({...form, body: e.target.value})} rows={5} className="w-full rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" required />{errors.body && <p className="text-xs text-red-500 mt-1">{errors.body}</p>}</div>
        <div className="grid sm:grid-cols-2 gap-4">
          <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label><select value={form.type} onChange={e => setForm({...form, type: e.target.value})} className="w-full rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm"><option value="info">Info</option><option value="warning">Warning</option><option value="success">Success</option></select></div>
          <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Expires At</label><input type="date" value={form.expires_at} onChange={e => setForm({...form, expires_at: e.target.value})} className="w-full rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" /></div>
        </div>
        <div className="flex items-center gap-3"><label className="text-xs font-bold text-gray-500 uppercase">Publish Immediately</label><input type="checkbox" checked={form.is_published} onChange={e => setForm({...form, is_published: e.target.checked})} /></div>
        <div className="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
          <Link href="/announcements" className="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-300">Cancel</Link>
          <button type="submit" disabled={submitting} className="px-6 py-2 text-sm font-bold rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">{submitting ? 'Creating...' : 'Create Announcement'}</button>
        </div>
      </form>
    </SuperAdminLayout>
  );
}
