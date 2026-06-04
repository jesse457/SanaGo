import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    BuildingOfficeIcon,
    MagnifyingGlassIcon,
    AdjustmentsHorizontalIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import FormInput from '@/Components/FormInput';
import FormTextarea from '@/Components/FormTextarea';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Props {
    wards: any[];
}

export default function WardIndex({ wards }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [editingWard, setEditingWard] = useState<any>(null);

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
        floor: '',
        capacity: '',
        description: '',
    });

    const openCreateModal = () => {
        setEditingWard(null);
        reset();
        clearErrors();
        setModalOpen(true);
    };

    const openEditModal = (ward: any) => {
        setEditingWard(ward);
        setData({
            name: ward.name || '',
            code: ward.code || '',
            floor: ward.floor || '',
            capacity: ward.capacity || '',
            description: ward.description || '',
        });
        clearErrors();
        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setEditingWard(null);
        reset();
        clearErrors();
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (editingWard) {
            put(route('wards.update', editingWard.id), {
                onSuccess: closeModal,
            });
        } else {
            post(route('wards.store'), {
                onSuccess: closeModal,
            });
        }
    };

    const handleDelete = (ward: any) => {
        if (confirm(t('page.wards.delete_confirm', { name: ward.name }))) {
            destroy(route('wards.destroy', ward.id));
        }
    };

    const filteredWards = wards.filter(
        (w) =>
            w.name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
            w.code?.toLowerCase().includes(searchQuery.toLowerCase()),
    );

    return (
        <AppLayout
            headerTitle={t('page.wards.title')}
            headerSubtitle={t('page.wards.subtitle')}
        >
            <Head title={t('page.wards.head_title')} />
            <div className="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: '/' },
                        { label: t('page.wards.head_title') },
                    ]}
                />

                {/* Header with search & add button */}
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <h2 className="text-2xl font-bold text-gray-900 dark:text-neutral-100">
                        {t('page.wards.heading')}
                    </h2>
                    <div className="flex items-center gap-3">
                        <FormInput
                            name="search"
                            icon={MagnifyingGlassIcon}
                            placeholder={t('page.wards.search_placeholder')}
                            value={searchQuery}
                            onChange={setSearchQuery}
                            className="w-48 sm:w-64"
                        />
                        <button className="rounded-lg border border-gray-200 bg-white p-2 text-gray-500 shadow-sm hover:text-gray-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:text-neutral-200">
                            <AdjustmentsHorizontalIcon className="h-5 w-5" />
                        </button>
                        <button
                            onClick={openCreateModal}
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('page.wards.add_ward')}
                        </button>
                    </div>
                </div>

                {/* Ward list */}
                {filteredWards.length === 0 ? (
                    <div className="rounded-2xl border-2 border-dashed border-gray-200 bg-white p-16 text-center dark:border-neutral-700 dark:bg-neutral-800">
                        <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-neutral-700">
                            <BuildingOfficeIcon className="h-8 w-8 text-gray-400 dark:text-neutral-500" />
                        </div>
                        <h3 className="mt-4 text-lg font-bold text-gray-900 dark:text-neutral-100">
                            {t('page.wards.no_wards')}
                        </h3>
                        <p className="mt-2 text-sm text-gray-500 dark:text-neutral-400">
                            {t('page.wards.get_started')}
                        </p>
                        <button
                            onClick={openCreateModal}
                            className="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition-colors hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('page.wards.add_first_ward')}
                        </button>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {filteredWards.map((ward) => (
                            <div
                                key={ward.id}
                                className="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                            >
                                <div className="flex items-start justify-between">
                                    <div>
                                        <StatusBadge
                                            label={ward.code}
                                            color="blue"
                                        />
                                        <h4 className="mt-3 text-base font-bold text-gray-900 dark:text-neutral-100">
                                            {ward.name}
                                        </h4>
                                        {ward.floor && (
                                            <p className="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                                {ward.floor}
                                            </p>
                                        )}
                                    </div>
                                    <div className="flex gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                        <button
                                            onClick={() => openEditModal(ward)}
                                            className="rounded-md p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600 dark:text-neutral-400 dark:hover:bg-blue-900/20 dark:hover:text-blue-400"
                                        >
                                            <PencilSquareIcon className="h-4 w-4" />
                                        </button>
                                        <button
                                            onClick={() => handleDelete(ward)}
                                            className="rounded-md p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:text-neutral-400 dark:hover:bg-red-900/20 dark:hover:text-red-400"
                                        >
                                            <TrashIcon className="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                                <div className="mt-5 flex justify-between rounded-lg bg-gray-50 p-3 text-xs font-bold text-gray-500 uppercase dark:bg-neutral-700/50 dark:text-neutral-400">
                                    <span>
                                        {t('page.wards.current_capacity')}
                                    </span>
                                    <span className="text-blue-600 dark:text-blue-400">
                                        {ward.beds_count || 0} /{' '}
                                        {ward.capacity || 0}{' '}
                                        {t('page.wards.beds')}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <Modal
                isOpen={modalOpen}
                onClose={closeModal}
                title={
                    editingWard
                        ? t('page.wards.edit_ward')
                        : t('page.wards.add_ward')
                }
            >
                <form onSubmit={handleSubmit} className="space-y-5">
                    <div className="grid grid-cols-2 gap-4">
                        <FormInput
                            name="name"
                            label={t('page.wards.ward_name')}
                            value={data.name}
                            onChange={(v) => setData('name', v)}
                            errors={errors}
                            placeholder="e.g. Maternity Ward"
                            className="col-span-2"
                        />
                        <FormInput
                            name="code"
                            label={t('page.wards.ward_code')}
                            value={data.code}
                            onChange={(v) => setData('code', v)}
                            errors={errors}
                            placeholder="e.g. W-MAT-01"
                        />
                        <FormInput
                            name="capacity"
                            type="number"
                            label={t('page.wards.capacity_beds')}
                            value={data.capacity}
                            onChange={(v) => setData('capacity', v)}
                            errors={errors}
                            min="1"
                        />
                        <FormInput
                            name="floor"
                            label={t('page.wards.floor_location')}
                            value={data.floor}
                            onChange={(v) => setData('floor', v)}
                            errors={errors}
                            placeholder="e.g. 2nd Floor, West Wing"
                            className="col-span-2"
                        />
                        <FormTextarea
                            name="description"
                            label={t('common.description')}
                            value={data.description}
                            onChange={(v) => setData('description', v)}
                            errors={errors}
                            rows={3}
                            placeholder="Brief description..."
                            className="col-span-2"
                        />
                    </div>

                    <div className="flex justify-end gap-3 border-t border-gray-100 pt-6 dark:border-neutral-700">
                        <button
                            type="button"
                            onClick={closeModal}
                            disabled={processing}
                            className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:opacity-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            {t('common.cancel')}
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700 disabled:opacity-50"
                        >
                            {processing && (
                                <span className="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />
                            )}
                            {processing
                                ? t('page.wards.saving')
                                : t('common.save_changes')}
                        </button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
