import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { HomeIcon, ChevronRightIcon, TicketIcon, PaperAirplaneIcon, ArrowPathIcon } from '@heroicons/react/24/outline';

interface Message { id: number; message: string; is_internal: boolean; user_name: string; created_at: string; }
interface Ticket { id: number; subject: string; status: string; priority: string; created_at: string; messages: Message[]; }

const STATUS_COLORS: Record<string, string> = { open: 'bg-blue-100 text-blue-700', in_progress: 'bg-amber-100 text-amber-700', resolved: 'bg-emerald-100 text-emerald-700', closed: 'bg-gray-100 text-gray-600' };

export default function SupportShow({ ticket }: { ticket: Ticket }) {
  const { t } = useTranslation();
  const { data, setData, post, processing, reset } = useForm({ message: '' });
  const sendReply = (e: FormEvent) => { e.preventDefault(); post(`/support/${ticket.id}/reply`, { preserveScroll: true, onSuccess: () => reset('message') }); };

  return (
    <AppLayout headerTitle={ticket.subject} headerSubtitle={`Ticket #${ticket.id}`}>
      <Head title={ticket.subject} />
      <div className="max-w-3xl mx-auto space-y-6 px-6">
        <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="hover:text-blue-600 cursor-pointer"><Link href="/support">Support</Link></li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="text-blue-600 font-medium truncate max-w-[200px]">{ticket.subject}</li></ol></nav>

        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm p-6">
          <div className="flex items-center gap-3 mb-6">
            <span className={`rounded-full px-3 py-1 text-xs font-bold ${STATUS_COLORS[ticket.status] || 'bg-gray-100'}`}>{ticket.status.replace('_', ' ')}</span>
            <span className="text-xs font-bold text-gray-400 uppercase">{ticket.priority} priority</span>
            <span className="text-xs text-gray-400 ml-auto">{new Date(ticket.created_at).toLocaleDateString()}</span>
          </div>

          <div className="space-y-4 mb-6 max-h-96 overflow-y-auto">
            {ticket.messages.map(m => (
              <div key={m.id} className={`p-4 rounded-xl ${m.is_internal ? 'hidden' : 'bg-gray-50 dark:bg-neutral-700'}`}>
                <div className="flex items-center justify-between mb-1"><span className="text-sm font-bold">{m.user_name}</span><span className="text-xs text-gray-400">{new Date(m.created_at).toLocaleString()}</span></div>
                <p className="text-sm">{m.message}</p>
              </div>
            ))}
          </div>

          {ticket.status !== 'closed' && (
            <form onSubmit={sendReply} className="space-y-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
              <textarea value={data.message} onChange={e => setData('message', e.target.value)} rows={3} placeholder="Type your reply..." className="w-full rounded-lg border border-gray-300 dark:border-neutral-600 px-3 py-2 text-sm dark:bg-neutral-700 dark:text-neutral-100 resize-none" required />
              <div className="flex justify-end"><button type="submit" disabled={processing} className="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">{processing ? <ArrowPathIcon className="animate-spin h-4 w-4" /> : <PaperAirplaneIcon className="w-4 h-4" />} Send</button></div>
            </form>
          )}
        </div>
      </div>
    </AppLayout>
  );
}
