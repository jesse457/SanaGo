import { Head, Link } from '@inertiajs/react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { HomeIcon, ChevronRightIcon, UserGroupIcon, EnvelopeIcon, BuildingOfficeIcon } from '@heroicons/react/24/outline';

interface DemoRequest { id: number; name: string; email: string; company: string | null; pipeline_status: string; assigned_to: string | null; follow_up_date: string | null; created_at: string; }

const STAGES = ['new', 'contacted', 'demo_scheduled', 'negotiating', 'converted', 'rejected'] as const;
const STAGE_LABELS: Record<string, string> = { new: 'New', contacted: 'Contacted', demo_scheduled: 'Demo', negotiating: 'Negotiating', converted: 'Won', rejected: 'Lost' };
const STAGE_COLORS: Record<string, string> = { new: 'bg-blue-50 text-blue-700', contacted: 'bg-purple-50 text-purple-700', demo_scheduled: 'bg-amber-50 text-amber-700', negotiating: 'bg-orange-50 text-orange-700', converted: 'bg-emerald-50 text-emerald-700', rejected: 'bg-red-50 text-red-700' };

export default function DemoRequestsIndex({ demoRequests }: { demoRequests: DemoRequest[] }) {
  const { t } = useTranslation();

  return (
    <SuperAdminLayout headerTitle="Demo Requests" headerSubtitle="Sales pipeline">
      <Head title="Demo Requests" />
      <div className="mx-auto max-w-full space-y-6 px-6">
        <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase"><ol className="inline-flex items-center space-x-2"><li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="mr-1 h-4 w-4" /> {t('common.home')}</li><li><ChevronRightIcon className="h-4 w-4" /></li><li className="text-blue-600 font-medium">Demo Requests</li></ol></nav>

        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
          {STAGES.map(stage => {
            const items = demoRequests.filter(d => d.pipeline_status === stage);
            return (
              <div key={stage} className="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-3">
                <div className="flex items-center justify-between mb-3">
                  <span className={`rounded-full px-2.5 py-1 text-xs font-bold ${STAGE_COLORS[stage]}`}>{STAGE_LABELS[stage]}</span>
                  <span className="text-xs font-bold text-gray-400 bg-white dark:bg-neutral-800 px-2 py-0.5 rounded-full">{items.length}</span>
                </div>
                <div className="space-y-2">
                  {items.map(req => (
                    <Link key={req.id} href={`/demo-requests/${req.id}`} className="block bg-white dark:bg-neutral-900 p-3 rounded-lg border border-gray-100 dark:border-neutral-700 hover:shadow-md hover:border-blue-300 transition-all">
                      <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100 truncate">{req.name}</p>
                      <p className="text-xs text-gray-500 truncate mt-0.5 flex items-center gap-1"><EnvelopeIcon className="w-3 h-3" />{req.email}</p>
                      {req.company && <p className="text-xs text-gray-400 mt-0.5 flex items-center gap-1"><BuildingOfficeIcon className="w-3 h-3" />{req.company}</p>}
                      {req.assigned_to && <p className="text-xs text-gray-400 mt-1 font-medium">{req.assigned_to}</p>}
                    </Link>
                  ))}
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </SuperAdminLayout>
  );
}
