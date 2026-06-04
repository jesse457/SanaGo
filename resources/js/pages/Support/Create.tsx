import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { HomeIcon, ChevronRightIcon, TicketIcon, ArrowPathIcon, CheckIcon } from '@heroicons/react/24/outline';

export default function SupportCreate() {
  const { t } = useTranslation();
  const { data, setData, post, processing, errors } = useForm({ subject: '', priority: 'medium', message: '' });
  const submit = (e: FormEvent) => { e.preventDefault(); post('/support'); };

  return (
    <AppLayout headerTitle="New Support Ticket" headerSubtitle="Report an issue or ask for help">
      <Head title="New Ticket" />
      <div className="max-w-3xl mx-auto space-y-6 px-6">
        <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="hover:text-blue-600 cursor-pointer"><Link href="/support">Support</Link></li><li><ChevronRightIcon className="w-4 h-4" /></li><li className="text-blue-600 font-medium">New</li></ol></nav>

        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
          <form onSubmit={submit} className="p-6 space-y-8">
            <div className="space-y-4"><h3 className="text-sm font-bold text-blue-600 uppercase tracking-wide border-b border-gray-100 dark:border-neutral-700 pb-2 flex items-center gap-2"><TicketIcon className="w-5 h-5" /> Ticket Details</h3>
              <div className="space-y-4">
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Subject <span className="text-red-500">*</span></label><input type="text" value={data.subject} onChange={e => setData('subject', e.target.value)} className={`w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-neutral-100 ${errors.subject ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required />{errors.subject && <p className="text-xs text-red-500 mt-1">{errors.subject}</p>}</div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Priority</label><select value={data.priority} onChange={e => setData('priority', e.target.value)} className="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm dark:bg-neutral-700 dark:text-neutral-100"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option><option value="urgent">Urgent</option></select></div>
                <div className="space-y-1"><label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">Message <span className="text-red-500">*</span></label><textarea value={data.message} onChange={e => setData('message', e.target.value)} rows={5} className={`w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.message ? 'border-red-300' : 'border-gray-300 dark:border-neutral-600'}`} required />{errors.message && <p className="text-xs text-red-500 mt-1">{errors.message}</p>}</div>
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
              <Link href="/support" className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700">{t('common.cancel')}</Link>
              <button type="submit" disabled={processing} className="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">{processing ? <span className="flex items-center gap-2"><ArrowPathIcon className="animate-spin h-4 w-4" /> Submitting...</span> : <span className="flex items-center gap-2"><CheckIcon className="w-4 h-4" /> Submit Ticket</span>}</button>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}
