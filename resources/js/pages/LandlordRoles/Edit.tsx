import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { ArrowLeftIcon } from '@heroicons/react/24/outline';

interface EditRole { id: number; name: string; current_permissions: string[]; }

export default function LandlordRolesEdit({ role, permissions }: { role: EditRole; permissions: string[] }) {
  const [form, setForm] = useState({ name: role.name, permissions: role.current_permissions || [] });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [submitting, setSubmitting] = useState(false);

  const togglePerm = (p: string) => setForm(f => ({ ...f, permissions: f.permissions.includes(p) ? f.permissions.filter(x => x !== p) : [...f.permissions, p] }));

  const handleSubmit = (e: React.FormEvent) => { e.preventDefault(); setSubmitting(true); router.put(`/landlord-roles/${role.id}`, form, { onError: (err) => { setErrors(err as any); setSubmitting(false); } }); };

  return (
    <SuperAdminLayout headerTitle={`Edit ${role.name}`} headerSubtitle="Update role permissions">
      <Head title={`Edit ${role.name}`} />
      <Link href="/landlord-roles" className="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 mb-6"><ArrowLeftIcon className="w-4 h-4" /> Back</Link>
      <form onSubmit={handleSubmit} className="max-w-lg bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm p-6 space-y-5">
        <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Role Name</label><input type="text" value={form.name} onChange={e => setForm({...form, name: e.target.value})} className="w-full rounded-lg border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" required />{errors.name && <p className="text-xs text-red-500 mt-1">{errors.name}</p>}</div>
        <div>
          <label className="block text-xs font-bold text-gray-500 uppercase mb-3">Permissions</label>
          <div className="grid grid-cols-2 gap-2 max-h-64 overflow-y-auto">
            {permissions.map(p => (
              <label key={p} className={`flex items-center gap-2 p-2 rounded-lg cursor-pointer border text-sm ${form.permissions.includes(p) ? 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800 text-blue-700' : 'border-gray-100 dark:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-800'}`}>
                <input type="checkbox" checked={form.permissions.includes(p)} onChange={() => togglePerm(p)} className="rounded" />{p}
              </label>
            ))}
          </div>
        </div>
        <div className="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
          <Link href="/landlord-roles" className="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-300">Cancel</Link>
          <button type="submit" disabled={submitting} className="px-6 py-2 text-sm font-bold rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">{submitting ? 'Saving...' : 'Update Role'}</button>
        </div>
      </form>
    </SuperAdminLayout>
  );
}
