import { Head, Link, router, useForm } from '@inertiajs/react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { ArrowLeftIcon, EnvelopeIcon, BuildingOfficeIcon, PhoneIcon } from '@heroicons/react/24/outline';

interface Note { id: number; note: string; user_name: string; created_at: string; }
interface DemoRequest { id: number; name: string; email: string; company: string | null; message: string; pipeline_status: string; assigned_to: { id: number; name: string } | null; follow_up_date: string | null; created_at: string; notes: Note[]; }

const STAGES = ['new', 'contacted', 'demo_scheduled', 'negotiating', 'converted', 'rejected'];
const STAGE_LABELS: Record<string, string> = { new: 'New', contacted: 'Contacted', demo_scheduled: 'Demo Scheduled', negotiating: 'Negotiating', converted: 'Converted', rejected: 'Rejected' };

export default function DemoRequestsShow({ demoRequest: req }: { demoRequest: DemoRequest }) {
  const { data, setData, post, processing, errors } = useForm({ note: '' });
  const { data: statusForm, setData: setStatus, patch: patchStatus } = useForm({ pipeline_status: req.pipeline_status, assigned_to: req.assigned_to?.id ?? '', follow_up_date: req.follow_up_date ?? '' });

  return (
    <SuperAdminLayout headerTitle={req.name} headerSubtitle="Demo Request Detail">
      <Head title={`${req.name} — Demo Request`} />
      <Link href="/demo-requests" className="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 mb-6"><ArrowLeftIcon className="w-4 h-4" /> Back to Pipeline</Link>

      <div className="grid lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 space-y-6">
          <div className="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6">
            <h2 className="text-lg font-bold mb-4">Contact Information</h2>
            <dl className="grid sm:grid-cols-2 gap-4">
              <div><dt className="text-xs font-bold text-gray-400 uppercase">Name</dt><dd className="text-sm font-semibold mt-1">{req.name}</dd></div>
              <div><dt className="text-xs font-bold text-gray-400 uppercase">Email</dt><dd className="text-sm text-blue-600 mt-1">{req.email}</dd></div>
              <div><dt className="text-xs font-bold text-gray-400 uppercase">Company</dt><dd className="text-sm mt-1">{req.company || '—'}</dd></div>
              <div><dt className="text-xs font-bold text-gray-400 uppercase">Received</dt><dd className="text-sm text-gray-500 mt-1">{new Date(req.created_at).toLocaleDateString()}</dd></div>
            </dl>
            {req.message && <div className="mt-4 p-4 bg-gray-50 dark:bg-neutral-800 rounded-lg"><p className="text-sm text-gray-700 dark:text-neutral-300">{req.message}</p></div>}
          </div>

          <div className="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6">
            <h3 className="text-lg font-bold mb-4">Notes</h3>
            <div className="space-y-3 mb-4">
              {req.notes.map(n => (<div key={n.id} className="p-3 bg-gray-50 dark:bg-neutral-800 rounded-lg"><p className="text-sm">{n.note}</p><p className="text-xs text-gray-400 mt-1">{n.user_name} — {new Date(n.created_at).toLocaleString()}</p></div>))}
              {req.notes.length === 0 && <p className="text-sm text-gray-400">No notes yet.</p>}
            </div>
            <form onSubmit={(e) => { e.preventDefault(); post(`/demo-requests/${req.id}/notes`, { preserveScroll: true }); }} className="flex gap-2">
              <input type="text" value={data.note} onChange={e => setData('note', e.target.value)} placeholder="Add a note..." className="flex-1 rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" required />
              <button type="submit" disabled={processing} className="px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">Add</button>
            </form>
          </div>
        </div>

        <div className="space-y-6">
          <div className="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6">
            <h3 className="text-lg font-bold mb-4">Pipeline</h3>
            <div className="space-y-3">
              <div><label className="text-xs font-bold text-gray-400 uppercase">Stage</label>
                <select value={statusForm.pipeline_status} onChange={e => setStatus('pipeline_status', e.target.value)} className="w-full mt-1 rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm">
                  {STAGES.map(s => <option key={s} value={s}>{STAGE_LABELS[s]}</option>)}
                </select>
              </div>
              <div><label className="text-xs font-bold text-gray-400 uppercase">Follow-up Date</label><input type="date" value={statusForm.follow_up_date} onChange={e => setStatus('follow_up_date', e.target.value)} className="w-full mt-1 rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" /></div>
              <button onClick={() => patchStatus(`/demo-requests/${req.id}/status`, { preserveScroll: true })} className="w-full py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Status</button>
            </div>
          </div>
        </div>
      </div>
    </SuperAdminLayout>
  );
}
