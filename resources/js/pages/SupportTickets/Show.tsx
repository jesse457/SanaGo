import { Head, Link, router, useForm } from '@inertiajs/react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { ArrowLeftIcon, PaperAirplaneIcon } from '@heroicons/react/24/outline';

interface Message { id: number; message: string; is_internal: boolean; user_name: string; created_at: string; }
interface Ticket { id: number; tenant_name: string | null; tenant_id: string; subject: string; status: string; priority: string; user_name: string | null; created_at: string; messages: Message[]; }

const STATUSES = ['open', 'in_progress', 'resolved', 'closed'];

export default function SupportTicketsShow({ ticket }: { ticket: Ticket }) {
  const { data, setData, post, processing, reset } = useForm({ message: '', is_internal: false });
  const { data: sData, setData: setS, patch } = useForm({ status: ticket.status });

  const sendMessage = (e: React.FormEvent) => { e.preventDefault(); post(`/support-tickets/${ticket.id}/messages`, { preserveScroll: true, onSuccess: () => reset('message') }); };

  return (
    <SuperAdminLayout headerTitle={ticket.subject} headerSubtitle={`Ticket #${ticket.id}`}>
      <Head title={`${ticket.subject} — Ticket`} />
      <Link href="/support-tickets" className="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 mb-6"><ArrowLeftIcon className="w-4 h-4" /> Back to Tickets</Link>

      <div className="grid lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 space-y-6">
          <div className="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6">
            <h2 className="text-lg font-bold mb-4">Conversation</h2>
            <div className="space-y-4 mb-6 max-h-96 overflow-y-auto">
              {ticket.messages.map(m => (
                <div key={m.id} className={`p-4 rounded-xl ${m.is_internal ? 'bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800' : 'bg-gray-50 dark:bg-neutral-800'}`}>
                  <div className="flex items-center justify-between mb-1"><span className="text-sm font-bold">{m.user_name}</span><span className="text-xs text-gray-400">{new Date(m.created_at).toLocaleString()}</span></div>
                  <p className="text-sm">{m.message}</p>
                  {m.is_internal && <span className="text-xs font-bold text-amber-600 mt-1">Internal Note</span>}
                </div>
              ))}
            </div>
            <form onSubmit={sendMessage} className="space-y-3">
              <textarea value={data.message} onChange={e => setData('message', e.target.value)} rows={3} placeholder="Type your reply..." className="w-full rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" required />
              <div className="flex items-center justify-between">
                <label className="flex items-center gap-2 text-xs text-gray-500"><input type="checkbox" checked={data.is_internal} onChange={e => setData('is_internal', e.target.checked)} /> Internal note</label>
                <button type="submit" disabled={processing} className="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"><PaperAirplaneIcon className="w-4 h-4" /> Send</button>
              </div>
            </form>
          </div>
        </div>

        <div className="space-y-6">
          <div className="bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6">
            <h3 className="text-lg font-bold mb-4">Details</h3>
            <dl className="space-y-3">
              <div className="flex justify-between"><dt className="text-xs text-gray-400">Tenant</dt><dd className="text-sm font-semibold">{ticket.tenant_name || '—'}</dd></div>
              <div className="flex justify-between"><dt className="text-xs text-gray-400">Priority</dt><dd className="text-sm font-bold uppercase">{ticket.priority}</dd></div>
              <div className="flex justify-between"><dt className="text-xs text-gray-400">Created</dt><dd className="text-sm">{new Date(ticket.created_at).toLocaleDateString()}</dd></div>
              <div className="flex justify-between items-center"><dt className="text-xs text-gray-400">Status</dt><dd>
                <select value={sData.status} onChange={e => setS('status', e.target.value)} className="text-sm rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-2 py-1">
                  {STATUSES.map(s => <option key={s} value={s}>{s.replace('_', ' ')}</option>)}
                </select>
              </dd></div>
            </dl>
            <button onClick={() => patch(`/support-tickets/${ticket.id}/status`, { preserveScroll: true })} className="w-full mt-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Status</button>
          </div>
        </div>
      </div>
    </SuperAdminLayout>
  );
}
