import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { PlusIcon, TrashIcon, MegaphoneIcon, ExclamationTriangleIcon, XMarkIcon, HomeIcon, ChevronRightIcon, CheckCircleIcon } from '@heroicons/react/24/outline';

interface Announcement { id: number; title: string; body: string; type: string; is_published: boolean; published_at: string | null; expires_at: string | null; created_at: string; }

const TYPE_COLORS: Record<string, string> = { info: 'bg-blue-50 text-blue-700', warning: 'bg-amber-50 text-amber-700', success: 'bg-emerald-50 text-emerald-700' };

export default function AnnouncementsIndex({ announcements }: { announcements: Announcement[] }) {
  const { t } = useTranslation();
  const [delModal, setDelModal] = useState<Announcement | null>(null);
  const { delete: destroy, processing } = useForm();
  const handleDelete = (e: FormEvent) => { e.preventDefault(); if (delModal) destroy(`/announcements/${delModal.id}`, { onSuccess: () => setDelModal(null) }); };

  return (
    <SuperAdminLayout headerTitle="Announcements" headerSubtitle="System-wide announcements for all tenants">
      <Head title="Announcements" />
      <div className="mx-auto max-w-7xl space-y-6 px-6">
        <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="mr-1 h-4 w-4" /> {t('common.home')}</li><li><ChevronRightIcon className="h-4 w-4" /></li><li className="text-blue-600 font-medium">Announcements</li></ol></nav>

        <div className="flex justify-end">
          <Link href="/announcements/create" className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"><PlusIcon className="h-4 w-4" /> New Announcement</Link>
        </div>

        {announcements.length === 0 ? (
          <div className="text-center py-16"><MegaphoneIcon className="mx-auto h-12 w-12 text-gray-300" /><p className="mt-3 text-gray-500 font-medium">No announcements</p></div>
        ) : (
          <div className="space-y-4">
            {announcements.map(a => (
              <div key={a.id} className="group rounded-xl border border-gray-200 bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
                <div className="flex items-start justify-between">
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-3 mb-2">
                      <div className={`rounded-lg p-2 ${TYPE_COLORS[a.type] || 'bg-gray-50'}`}><MegaphoneIcon className="h-5 w-5" /></div>
                      <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">{a.title}</h3>
                      <span className={`rounded-full px-2 py-0.5 text-xs font-bold ${TYPE_COLORS[a.type] || 'bg-gray-100'}`}>{a.type}</span>
                      {a.is_published ? <span className="flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full"><CheckCircleIcon className="w-3 h-3" /> Published</span> : <span className="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Draft</span>}
                    </div>
                    <p className="text-sm text-gray-600 dark:text-neutral-400 line-clamp-2 mb-3">{a.body}</p>
                    <div className="flex gap-4 text-xs text-gray-400">
                      {a.published_at && <span>Published: {new Date(a.published_at).toLocaleString()}</span>}
                      {a.expires_at && <span>Expires: {new Date(a.expires_at).toLocaleString()}</span>}
                    </div>
                  </div>
                  <button onClick={() => setDelModal(a)} className="shrink-0 rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-all"><TrashIcon className="h-4 w-4" /></button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {delModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4"><div className="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onClick={() => setDelModal(null)} /><div className="relative w-full max-w-md rounded-xl border border-gray-200 bg-white shadow-xl dark:border-neutral-700 dark:bg-neutral-800"><div className="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-neutral-700"><h3 className="text-lg font-semibold">Delete Announcement</h3><button onClick={() => setDelModal(null)} className="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100"><XMarkIcon className="h-5 w-5" /></button></div><div className="p-6 text-center"><ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-red-600" /><p className="text-gray-700 dark:text-neutral-300">Delete <strong>{delModal.title}</strong>?</p></div><div className="flex justify-end gap-3 border-t border-gray-100 px-6 py-4 dark:border-neutral-700"><button onClick={() => setDelModal(null)} className="rounded-lg border px-4 py-2 text-sm">{t('common.cancel')}</button><button onClick={handleDelete} disabled={processing} className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white disabled:opacity-50">{processing ? 'Deleting...' : 'Delete'}</button></div></div></div>
      )}
    </SuperAdminLayout>
  );
}
