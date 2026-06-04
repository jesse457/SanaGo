import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { PlusIcon, TicketIcon, HomeIcon, ChevronRightIcon } from '@heroicons/react/24/outline';

interface Ticket { id: number; subject: string; status: string; priority: string; created_at: string; updated_at: string; }

const STATUS_COLORS: Record<string, string> = { open: 'bg-blue-100 text-blue-700', in_progress: 'bg-amber-100 text-amber-700', resolved: 'bg-emerald-100 text-emerald-700', closed: 'bg-gray-100 text-gray-600' };

export default function SupportIndex({ tickets }: { tickets: Ticket[] }) {
  const { t } = useTranslation();

  return (
    <AppLayout headerTitle="Support" headerSubtitle="View your support tickets">
      <Head title="Support" />
      <div className="mx-auto max-w-5xl space-y-6 px-6">
        <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="text-blue-600 font-medium">Support</li></ol></nav>

        <div className="flex justify-end"><Link href="/support/create" className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"><PlusIcon className="h-4 w-4" /> New Ticket</Link></div>

        {tickets.length === 0 ? (
          <div className="text-center py-16 bg-white dark:bg-neutral-900 rounded-2xl border"><TicketIcon className="mx-auto h-12 w-12 text-gray-300" /><p className="mt-3 text-gray-500 font-medium">No support tickets yet</p><Link href="/support/create" className="inline-block mt-4 text-sm font-bold text-blue-600">Create your first ticket</Link></div>
        ) : (
          <div className="space-y-4">
            {tickets.map(ticket => (
              <Link key={ticket.id} href={`/support/${ticket.id}`} className="block bg-white dark:bg-neutral-900 rounded-xl border border-gray-100 dark:border-neutral-800 p-6 hover:border-blue-400 hover:shadow-md transition-all">
                <div className="flex items-start justify-between"><div className="flex-1"><h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">{ticket.subject}</h3><div className="flex items-center gap-3 mt-2"><span className={`rounded-full px-2.5 py-0.5 text-xs font-bold ${STATUS_COLORS[ticket.status] || 'bg-gray-100'}`}>{ticket.status.replace('_', ' ')}</span><span className="text-xs font-bold text-gray-400 uppercase">{ticket.priority}</span><span className="text-xs text-gray-400">{new Date(ticket.created_at).toLocaleDateString()}</span></div></div></div>
              </Link>
            ))}
          </div>
        )}
      </div>
    </AppLayout>
  );
}
