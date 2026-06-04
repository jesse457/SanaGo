import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { PlusIcon, TrashIcon, TicketIcon, MagnifyingGlassIcon, ExclamationTriangleIcon, XMarkIcon, HomeIcon, ChevronRightIcon, BuildingOfficeIcon } from '@heroicons/react/24/outline';

interface Ticket { id: number; tenant_name: string | null; subject: string; status: string; priority: string; user_name: string | null; created_at: string; }

const STATUS_COLORS: Record<string, string> = { open: 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400', in_progress: 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400', resolved: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400', closed: 'bg-gray-100 text-gray-600' };

export default function SupportTicketsIndex({ tickets }: { tickets: Ticket[] }) {
  const { t } = useTranslation();
  const [search, setSearch] = useState('');
  const filtered = useMemo(() => tickets.filter(t => t.subject.toLowerCase().includes(search.toLowerCase()) || (t.tenant_name || '').toLowerCase().includes(search.toLowerCase())), [tickets, search]);

  return (
    <SuperAdminLayout headerTitle="Support Tickets" headerSubtitle="Manage tenant support requests">
      <Head title="Support Tickets" />
      <div className="mx-auto max-w-7xl space-y-6 px-6">
        <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="mr-1 h-4 w-4" /> {t('common.home')}</li><li><ChevronRightIcon className="h-4 w-4" /></li><li className="text-blue-600 font-medium">Support Tickets</li></ol></nav>

        <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
          <div className="relative w-full sm:w-96"><MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" /><input type="text" placeholder="Search tickets..." value={search} onChange={e => setSearch(e.target.value)} className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800" /></div>
          <Link href="/support-tickets/create" className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"><PlusIcon className="h-4 w-4" /> New Ticket</Link>
        </div>

        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
          {filtered.map(ticket => (
            <Link key={ticket.id} href={`/support-tickets/${ticket.id}`} className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
              <div className="mb-4 flex items-start justify-between">
                <div className="rounded-lg bg-amber-50 p-3 text-amber-600"><TicketIcon className="h-6 w-6" /></div>
                <span className={`rounded-full px-2.5 py-1 text-xs font-bold ${STATUS_COLORS[ticket.status] || 'bg-gray-100'}`}>{ticket.status.replace('_', ' ')}</span>
              </div>
              <h3 className="mb-2 text-lg font-bold text-gray-900 group-hover:text-blue-600 dark:text-neutral-100">{ticket.subject}</h3>
              <div className="flex items-center gap-1.5 text-xs text-gray-500"><BuildingOfficeIcon className="h-3.5 w-3.5" />{ticket.tenant_name || 'No tenant'}</div>
              <div className="mt-3 flex items-center gap-3 text-xs text-gray-400"><span className="font-bold uppercase">{ticket.priority}</span><span>{new Date(ticket.created_at).toLocaleDateString()}</span></div>
            </Link>
          ))}
        </div>
        {filtered.length === 0 && <div className="text-center py-16"><TicketIcon className="mx-auto h-12 w-12 text-gray-300" /><p className="mt-3 text-gray-500 font-medium">No tickets found</p></div>}
      </div>
    </SuperAdminLayout>
  );
}
