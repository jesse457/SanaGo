import {
    PlusIcon, PencilSquareIcon, TrashIcon,
    BuildingOffice2Icon, PhoneIcon, ChevronRightIcon,
    XMarkIcon, MagnifyingGlassIcon,
    UserGroupIcon, CheckBadgeIcon, BeakerIcon,
    ExclamationTriangleIcon, HomeIcon, UserIcon
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useState, useMemo } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import Modal from '@/Components/Modal';

interface Department {
    id: number;
    name: string;
    code: string;
    description: string;
    phone_extension: string;
    is_active: boolean;
    type: 'clinical' | 'administrative' | 'support';
    staff_count: number;
    ward_count: number;
    head_of_department?: { id: number; name: string } | null;
}

interface Props {
    departments: Department[];
    users: { id: number; name: string; department_id: number | null }[];
}

export default function Departments({ departments, users }: Props) {
    const [searchQuery, setSearchQuery] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState<'add' | 'edit' | 'delete' | null>(null);
    const [selectedItem, setSelectedItem] = useState<Department | null>(null);

    const { data, setData, post, put, delete: destroy, processing, errors, reset, clearErrors } = useForm({
        name: '',
        code: '',
        description: '',
        phone_extension: '',
        type: 'clinical' as 'clinical' | 'administrative' | 'support',
        is_active: true,
        head_of_department_id: '' as string | number,
    });

    const openModal = (mode: 'add' | 'edit' | 'delete', dept?: Department) => {
        setModalMode(mode);
        setSelectedItem(dept || null);
        clearErrors();

        if (mode === 'edit' && dept) {
            setData({
                name: dept.name,
                code: dept.code,
                description: dept.description || '',
                phone_extension: dept.phone_extension,
                type: dept.type,
                is_active: dept.is_active,
                head_of_department_id: dept.head_of_department?.id || '',
            });
        } else if (mode === 'add') {
            reset();
        }

        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setModalMode(null);
        setSelectedItem(null);
        reset();
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (modalMode === 'add') {
            post(route('departments.store'), {
                onSuccess: () => closeModal(),
            });
        } else if (modalMode === 'edit' && selectedItem) {
            put(route('departments.update', selectedItem.id), {
                onSuccess: () => closeModal(),
            });
        } else if (modalMode === 'delete' && selectedItem) {
            destroy(route('departments.destroy', selectedItem.id), {
                onSuccess: () => closeModal(),
            });
        }
    };

    const filteredDepartments = useMemo(() => {
        if (!searchQuery) return departments;
        const q = searchQuery.toLowerCase();
        return departments.filter(d =>
            d.name.toLowerCase().includes(q) ||
            d.code.toLowerCase().includes(q) ||
            d.type.toLowerCase().includes(q)
        );
    }, [departments, searchQuery]);

    const stats = useMemo(() => ({
        total: departments.length,
        active: departments.filter(d => d.is_active).length,
        clinical: departments.filter(d => d.type === 'clinical').length,
        staff: departments.reduce((acc, d) => acc + d.staff_count, 0)
    }), [departments]);

    return (
        <AppLayout
            headerTitle="Hospital Departments"
            headerSubtitle="Configure hospital units and service categories"
        >
            <Head title="Departments" />

            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer transition-colors">
                            <HomeIcon className="w-4 h-4 mr-1" /> Home
                        </li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li>Infrastructure</li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-medium">Departments</li>
                    </ol>
                </nav>

                {/* Stats Overview */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard label="Total Units" value={stats.total} icon={<BuildingOffice2Icon className="w-5 h-5" />} color="blue" />
                    <StatCard label="Operational" value={stats.active} icon={<CheckBadgeIcon className="w-5 h-5" />} color="emerald" />
                    <StatCard label="Clinical Depts" value={stats.clinical} icon={<BeakerIcon className="w-5 h-5" />} color="purple" />
                    <StatCard label="Total Staff" value={stats.staff} icon={<UserGroupIcon className="w-5 h-5" />} color="amber" />
                </div>

                {/* Header Controls */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="relative w-full sm:w-96">
                        <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                        <input
                            type="text"
                            placeholder="Search departments..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pl-9 pr-4 py-2 w-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        />
                    </div>

                    <button
                        onClick={() => openModal('add')}
                        className="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors shadow-sm whitespace-nowrap"
                    >
                        <PlusIcon className="w-4 h-4" /> Add Department
                    </button>
                </div>

                {/* Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {filteredDepartments.map((dept) => (
                        <div key={dept.id} className="group bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 hover:border-blue-400 hover:shadow-md transition-all duration-200 relative">
                            <div className="flex justify-between items-start mb-4">
                                <div className={`p-3 rounded-lg ${dept.is_active ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-400 dark:text-neutral-500'}`}>
                                    <BuildingOffice2Icon className="w-6 h-6" />
                                </div>
                                <span className={`px-2.5 py-1 rounded-full text-xs font-bold border ${
                                    dept.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-gray-50 text-gray-500 dark:text-neutral-500 border-gray-100 dark:border-neutral-700'
                                }`}>
                                    {dept.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </div>

                            <div className="mb-4">
                                <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100 truncate">{dept.name}</h3>
                                <div className="flex items-center gap-2 mt-1">
                                    <span className="text-xs font-mono font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">{dept.code}</span>
                                    <span className="text-xs text-gray-400 dark:text-neutral-500">•</span>
                                    <span className="text-xs font-medium text-gray-500 dark:text-neutral-500">{dept.type}</span>
                                </div>
                            </div>

                            <p className="text-sm text-gray-500 dark:text-neutral-500 line-clamp-2 mb-6 h-10">{dept.description}</p>

                            <div className="flex items-center justify-between py-4 border-t border-gray-100 dark:border-neutral-700 mb-3">
                                <div className="flex gap-4">
                                    <div className="text-center">
                                        <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{dept.staff_count}</p>
                                        <p className="text-[10px] uppercase text-gray-400 dark:text-neutral-500 font-semibold">Staff</p>
                                    </div>
                                    <div className="text-center">
                                        <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{dept.ward_count}</p>
                                        <p className="text-[10px] uppercase text-gray-400 dark:text-neutral-500 font-semibold">Wards</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-neutral-500 bg-gray-50 dark:bg-neutral-700 px-2.5 py-1.5 rounded-lg">
                                    <PhoneIcon className="w-3.5 h-3.5" /> {dept.phone_extension}
                                </div>
                            </div>

                            <div className="flex items-center gap-2 mb-4 text-xs text-gray-500 dark:text-neutral-400">
                                <UserIcon className="w-3.5 h-3.5 shrink-0" />
                                <span className="truncate">
                                    {dept.head_of_department ? (
                                        <span className="font-medium text-gray-700 dark:text-neutral-300">{dept.head_of_department.name}</span>
                                    ) : (
                                        <span className="italic">No HOD assigned</span>
                                    )}
                                </span>
                            </div>

                            <div className="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    onClick={() => openModal('edit', dept)}
                                    className="flex-1 flex items-center justify-center gap-2 py-2 bg-gray-50 dark:bg-neutral-700 hover:bg-blue-50 text-gray-600 dark:text-neutral-400 hover:text-blue-600 rounded-lg text-xs font-semibold transition-colors"
                                >
                                    <PencilSquareIcon className="w-4 h-4" /> Edit
                                </button>
                                <button
                                    onClick={() => openModal('delete', dept)}
                                    className="px-3 py-2 bg-gray-50 dark:bg-neutral-700 hover:bg-red-50 dark:hover:bg-red-900/20 text-gray-400 dark:text-neutral-500 hover:text-red-600 rounded-lg transition-colors"
                                >
                                    <TrashIcon className="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* --- ANIMATED MODAL --- */}
            <Modal
                isOpen={modalOpen}
                onClose={closeModal}
                title={`${modalMode === 'add' ? 'Add' : modalMode === 'edit' ? 'Edit' : 'Confirm Delete'} Department`}
            >
                <form onSubmit={handleSubmit} className="space-y-5">
                    {modalMode === 'delete' ? (
                        <div className="text-center">
                            <div className="mx-auto w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
                                <ExclamationTriangleIcon className="w-6 h-6 text-red-600" />
                            </div>
                            <p className="text-gray-700 dark:text-neutral-300 mb-2">
                                Are you sure you want to delete <span className="font-semibold">"{selectedItem?.name}"</span>?
                            </p>
                            <p className="text-sm text-gray-500 dark:text-neutral-500">This action cannot be undone.</p>
                            {errors.error && <p className="mt-4 text-sm font-bold text-red-600">{errors.error}</p>}
                        </div>
                    ) : (
                        <div className="grid grid-cols-2 gap-4">
                            <div className="col-span-2">
                                <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Department Name *</label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className={`w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 ${errors.name ? 'border-red-300' : 'border-gray-300'}`}
                                    placeholder="e.g. Cardiology"
                                />
                                {errors.name && <p className="mt-1 text-xs text-red-500">{errors.name}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Code *</label>
                                <input
                                    type="text"
                                    value={data.code}
                                    onChange={(e) => setData('code', e.target.value)}
                                    className={`w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 ${errors.code ? 'border-red-300' : 'border-gray-300'}`}
                                    placeholder="e.g. CARD-01"
                                />
                                {errors.code && <p className="mt-1 text-xs text-red-500">{errors.code}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Phone Ext. *</label>
                                <input
                                    type="text"
                                    value={data.phone_extension}
                                    onChange={(e) => setData('phone_extension', e.target.value)}
                                    className={`w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 ${errors.phone_extension ? 'border-red-300' : 'border-gray-300'}`}
                                    placeholder="e.g. 101"
                                />
                                {errors.phone_extension && <p className="mt-1 text-xs text-red-500">{errors.phone_extension}</p>}
                            </div>

                            <div className="col-span-2">
                                <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Classification</label>
                                <select
                                    value={data.type}
                                    onChange={(e) => setData('type', e.target.value as any)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="clinical">Clinical</option>
                                    <option value="administrative">Administrative</option>
                                    <option value="support">Support Services</option>
                                </select>
                            </div>

                            <div className="col-span-2">
                                <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Head of Department</label>
                                <select
                                    value={data.head_of_department_id}
                                    onChange={(e) => setData('head_of_department_id', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">None</option>
                                    {users.map(u => (
                                        <option key={u.id} value={u.id}>{u.name}</option>
                                    ))}
                                </select>
                            </div>

                            <div className="col-span-2">
                                <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Description</label>
                                <textarea
                                    rows={3}
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Brief description..."
                                />
                            </div>

                            <div className="col-span-2 flex items-center pt-2">
                                <input
                                    type="checkbox"
                                    id="is_active"
                                    checked={data.is_active}
                                    onChange={(e) => setData('is_active', e.target.checked)}
                                    className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <label htmlFor="is_active" className="ml-2 text-sm font-medium text-gray-700 dark:text-neutral-300">Active</label>
                            </div>
                        </div>
                    )}

                    <div className="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
                        <button
                            type="button"
                            onClick={closeModal}
                            disabled={processing}
                            className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 disabled:opacity-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className={`px-4 py-2 text-sm font-medium text-white rounded-lg disabled:opacity-50 flex items-center gap-2 transition-colors ${modalMode === 'delete' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'}`}
                        >
                            {processing && <span className="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full" />}
                            {modalMode === 'add' ? 'Create Department' : modalMode === 'edit' ? 'Save Changes' : 'Delete Department'}
                        </button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}

function StatCard({ label, value, icon, color }: { label: string, value: number, icon: React.ReactNode, color: string }) {
    const colors: any = {
        blue: 'bg-blue-50 text-blue-600',
        emerald: 'bg-emerald-50 text-emerald-600',
        purple: 'bg-purple-50 text-purple-600',
        amber: 'bg-amber-50 text-amber-600'
    };

    return (
        <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
            <div className={`p-3 rounded-lg ${colors[color]}`}>
                {icon}
            </div>
            <div>
                <p className="text-xl font-bold text-gray-900 dark:text-neutral-100 leading-none">{value}</p>
                <p className="text-xs font-semibold text-gray-500 dark:text-neutral-500 mt-1">{label}</p>
            </div>
        </div>
    );
}
