import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    BuildingOffice2Icon,
    PhoneIcon,
    MagnifyingGlassIcon,
    UserGroupIcon,
    CheckBadgeIcon,
    BeakerIcon,
    ExclamationTriangleIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import AppLayout from '@/Layouts/AppLayout';

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
    const [modalMode, setModalMode] = useState<
        'add' | 'edit' | 'delete' | null
    >(null);
    const [selectedItem, setSelectedItem] = useState<Department | null>(null);

    const {
        data,
        setData,
        post,
        put,
        delete: destroy,
        processing,
        errors,
        reset,
        clearErrors,
    } = useForm({
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
        if (!searchQuery) {
            return departments;
        }

        const q = searchQuery.toLowerCase();

        return departments.filter(
            (d) =>
                d.name.toLowerCase().includes(q) ||
                d.code.toLowerCase().includes(q) ||
                d.type.toLowerCase().includes(q),
        );
    }, [departments, searchQuery]);

    const stats = useMemo(
        () => ({
            total: departments.length,
            active: departments.filter((d) => d.is_active).length,
            clinical: departments.filter((d) => d.type === 'clinical').length,
            staff: departments.reduce((acc, d) => acc + d.staff_count, 0),
        }),
        [departments],
    );

    return (
        <AppLayout
            headerTitle="Hospital Departments"
            headerSubtitle="Configure hospital units and service categories"
        >
            <Head title="Departments" />

            <div className="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: 'Home', href: '/' },
                        { label: 'Infrastructure' },
                        { label: 'Departments' },
                    ]}
                />

                {/* Stats Overview */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        label="Total Units"
                        value={stats.total}
                        icon={BuildingOffice2Icon}
                        color="blue"
                    />
                    <StatCard
                        label="Operational"
                        value={stats.active}
                        icon={CheckBadgeIcon}
                        color="emerald"
                    />
                    <StatCard
                        label="Clinical Depts"
                        value={stats.clinical}
                        icon={BeakerIcon}
                        color="purple"
                    />
                    <StatCard
                        label="Total Staff"
                        value={stats.staff}
                        icon={UserGroupIcon}
                        color="amber"
                    />
                </div>

                {/* Header Controls */}
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="relative w-full sm:w-96">
                        <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                        <input
                            type="text"
                            placeholder="Search departments..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm transition-all focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500"
                        />
                    </div>

                    <button
                        onClick={() => openModal('add')}
                        className="flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold whitespace-nowrap text-white shadow-sm transition-colors hover:bg-blue-700"
                    >
                        <PlusIcon className="h-4 w-4" /> Add Department
                    </button>
                </div>

                {/* Grid */}
                {filteredDepartments.length === 0 ? (
                    <EmptyState
                        icon={BuildingOffice2Icon}
                        title="No departments found"
                        description="Try adjusting your search or add a new department."
                    />
                ) : (
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {filteredDepartments.map((dept) => (
                            <div
                                key={dept.id}
                                className="group relative rounded-xl border border-gray-200 bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                            >
                                <div className="mb-4 flex items-start justify-between">
                                    <div
                                        className={`rounded-lg p-3 ${dept.is_active ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-400 dark:text-neutral-500'}`}
                                    >
                                        <BuildingOffice2Icon className="h-6 w-6" />
                                    </div>
                                    <StatusBadge
                                        label={
                                            dept.is_active
                                                ? 'Active'
                                                : 'Inactive'
                                        }
                                        color={
                                            dept.is_active ? 'emerald' : 'gray'
                                        }
                                    />
                                </div>

                                <div className="mb-4">
                                    <h3 className="truncate text-lg font-bold text-gray-900 dark:text-neutral-100">
                                        {dept.name}
                                    </h3>
                                    <div className="mt-1 flex items-center gap-2">
                                        <span className="rounded bg-blue-50 px-1.5 py-0.5 font-mono text-xs font-semibold text-blue-600">
                                            {dept.code}
                                        </span>
                                        <span className="text-xs text-gray-400 dark:text-neutral-500">
                                            •
                                        </span>
                                        <span className="text-xs font-medium text-gray-500 dark:text-neutral-500">
                                            {dept.type}
                                        </span>
                                    </div>
                                </div>

                                <p className="mb-6 line-clamp-2 h-10 text-sm text-gray-500 dark:text-neutral-500">
                                    {dept.description}
                                </p>

                                <div className="mb-3 flex items-center justify-between border-t border-gray-100 py-4 dark:border-neutral-700">
                                    <div className="flex gap-4">
                                        <div className="text-center">
                                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                {dept.staff_count}
                                            </p>
                                            <p className="text-[10px] font-semibold text-gray-400 uppercase dark:text-neutral-500">
                                                Staff
                                            </p>
                                        </div>
                                        <div className="text-center">
                                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                {dept.ward_count}
                                            </p>
                                            <p className="text-[10px] font-semibold text-gray-400 uppercase dark:text-neutral-500">
                                                Wards
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-1.5 rounded-lg bg-gray-50 px-2.5 py-1.5 text-xs font-medium text-gray-500 dark:bg-neutral-700 dark:text-neutral-500">
                                        <PhoneIcon className="h-3.5 w-3.5" />{' '}
                                        {dept.phone_extension}
                                    </div>
                                </div>

                                <div className="mb-4 flex items-center gap-2 text-xs text-gray-500 dark:text-neutral-400">
                                    <UserIcon className="h-3.5 w-3.5 shrink-0" />
                                    <span className="truncate">
                                        {dept.head_of_department ? (
                                            <span className="font-medium text-gray-700 dark:text-neutral-300">
                                                {dept.head_of_department.name}
                                            </span>
                                        ) : (
                                            <span className="italic">
                                                No HOD assigned
                                            </span>
                                        )}
                                    </span>
                                </div>

                                <div className="flex gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                    <button
                                        onClick={() => openModal('edit', dept)}
                                        className="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gray-50 py-2 text-xs font-semibold text-gray-600 transition-colors hover:bg-blue-50 hover:text-blue-600 dark:bg-neutral-700 dark:text-neutral-400"
                                    >
                                        <PencilSquareIcon className="h-4 w-4" />{' '}
                                        Edit
                                    </button>
                                    <button
                                        onClick={() =>
                                            openModal('delete', dept)
                                        }
                                        className="rounded-lg bg-gray-50 px-3 py-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:bg-neutral-700 dark:text-neutral-500 dark:hover:bg-red-900/20"
                                    >
                                        <TrashIcon className="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* --- MODAL --- */}
            <Modal
                isOpen={modalOpen}
                onClose={closeModal}
                title={`${modalMode === 'add' ? 'Add' : modalMode === 'edit' ? 'Edit' : 'Confirm Delete'} Department`}
            >
                {modalMode === 'delete' ? (
                    <div>
                        <div className="py-4 text-center">
                            <ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-red-600" />
                            <p className="mb-2 text-gray-700 dark:text-neutral-300">
                                Are you sure you want to delete{' '}
                                <span className="font-semibold">
                                    "{selectedItem?.name}"
                                </span>
                                ?
                            </p>
                            <p className="text-sm text-gray-500 dark:text-neutral-500">
                                This action cannot be undone.
                            </p>
                            {(errors as Record<string, string>).error && (
                                <p className="mt-4 text-sm font-bold text-red-600">
                                    {(errors as Record<string, string>).error}
                                </p>
                            )}
                        </div>
                        <div className="flex justify-end gap-3 border-t border-gray-100 pt-6 dark:border-neutral-700">
                            <button
                                type="button"
                                onClick={closeModal}
                                disabled={processing}
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:opacity-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                onClick={handleSubmit}
                                disabled={processing}
                                className="flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700 disabled:opacity-50"
                            >
                                {processing && (
                                    <span className="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />
                                )}
                                Delete Department
                            </button>
                        </div>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div className="grid grid-cols-2 gap-4">
                            <FormInput
                                name="name"
                                label="Department Name"
                                required
                                value={data.name}
                                onChange={(v) => setData('name', v)}
                                errors={errors}
                                placeholder="e.g. Cardiology"
                                className="col-span-2"
                            />

                            <FormInput
                                name="code"
                                label="Code"
                                required
                                value={data.code}
                                onChange={(v) => setData('code', v)}
                                errors={errors}
                                placeholder="e.g. CARD-01"
                            />

                            <FormInput
                                name="phone_extension"
                                label="Phone Ext."
                                required
                                value={data.phone_extension}
                                onChange={(v) => setData('phone_extension', v)}
                                errors={errors}
                                placeholder="e.g. 101"
                            />

                            <FormSelect
                                name="type"
                                label="Classification"
                                value={data.type}
                                onChange={(v) => setData('type', v as any)}
                                errors={errors}
                                options={[
                                    {
                                        value: 'clinical',
                                        label: 'Clinical',
                                    },
                                    {
                                        value: 'administrative',
                                        label: 'Administrative',
                                    },
                                    {
                                        value: 'support',
                                        label: 'Support Services',
                                    },
                                ]}
                                className="col-span-2"
                            />

                            <FormSelect
                                name="head_of_department_id"
                                label="Head of Department"
                                value={data.head_of_department_id}
                                onChange={(v) =>
                                    setData('head_of_department_id', v)
                                }
                                errors={errors}
                                options={users.map((u) => ({
                                    value: u.id,
                                    label: u.name,
                                }))}
                                placeholder="None"
                                className="col-span-2"
                            />

                            <FormTextarea
                                name="description"
                                label="Description"
                                rows={3}
                                value={data.description}
                                onChange={(v) => setData('description', v)}
                                errors={errors}
                                placeholder="Brief description..."
                                className="col-span-2"
                            />

                            <div className="col-span-2 flex items-center pt-2">
                                <input
                                    type="checkbox"
                                    id="is_active"
                                    checked={data.is_active}
                                    onChange={(e) =>
                                        setData('is_active', e.target.checked)
                                    }
                                    className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <label
                                    htmlFor="is_active"
                                    className="ml-2 text-sm font-medium text-gray-700 dark:text-neutral-300"
                                >
                                    Active
                                </label>
                            </div>
                        </div>

                        <div className="flex justify-end gap-3 border-t border-gray-100 pt-6 dark:border-neutral-700">
                            <button
                                type="button"
                                onClick={closeModal}
                                disabled={processing}
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:opacity-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700 disabled:opacity-50"
                            >
                                {processing && (
                                    <span className="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />
                                )}
                                {modalMode === 'add'
                                    ? 'Create Department'
                                    : 'Save Changes'}
                            </button>
                        </div>
                    </form>
                )}
            </Modal>
        </AppLayout>
    );
}
