import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    TagIcon,
    AdjustmentsHorizontalIcon,
    MagnifyingGlassIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Props {
    bedTypes: any[];
}

export default function BedTypeIndex({ bedTypes }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [editingType, setEditingType] = useState<any>(null);

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
        capacity: '',
    });

    const openCreateModal = () => {
        setEditingType(null);
        reset();
        clearErrors();
        setModalOpen(true);
    };

    const openEditModal = (type: any) => {
        setEditingType(type);
        setData({
            name: type.name || '',
            capacity: type.capacity || '',
        });
        clearErrors();
        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setEditingType(null);
        reset();
        clearErrors();
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (editingType) {
            put(route('bed-types.update', editingType.id), {
                onSuccess: closeModal,
            });
        } else {
            post(route('bed-types.store'), { onSuccess: closeModal });
        }
    };

    const handleDelete = (type: any) => {
        if (confirm(t('page.bed_type.delete_confirm', { name: type.name }))) {
            destroy(route('bed-types.destroy', type.id));
        }
    };

    const filteredTypes = bedTypes.filter((t) =>
        t.name?.toLowerCase().includes(searchQuery.toLowerCase()),
    );

    return (
        <AppLayout
            headerTitle={t('page.bed_type.title')}
            headerSubtitle={t('page.bed_type.subtitle')}
        >
            <Head title={t('page.bed_type.head_title')} />
            <div className="mx-auto max-w-7xl space-y-6">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: t('page.bed_type.head_title') },
                    ]}
                />

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <h2 className="text-2xl font-bold text-gray-900 dark:text-neutral-100">
                        {t('page.bed_type.heading')}
                    </h2>
                    <div className="flex items-center gap-3">
                        <FormInput
                            name="search"
                            icon={MagnifyingGlassIcon}
                            placeholder={t('page.bed_type.search_placeholder')}
                            value={searchQuery}
                            onChange={setSearchQuery}
                            className="w-48 sm:w-64"
                        />
                        <button className="rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:text-gray-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:text-neutral-200">
                            <AdjustmentsHorizontalIcon className="h-5 w-5" />
                        </button>
                        <button
                            onClick={openCreateModal}
                            className="flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />
                            {t('page.bed_type.add_type')}
                        </button>
                    </div>
                </div>

                {filteredTypes.length === 0 ? (
                    <div className="space-y-6">
                        <EmptyState
                            icon={TagIcon}
                            title={t('page.bed_type.no_bed_types')}
                        />
                        <div className="text-center">
                            <button
                                onClick={openCreateModal}
                                className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700"
                            >
                                <PlusIcon className="h-4 w-4" />
                                {t('page.bed_type.add_first_type')}
                            </button>
                        </div>
                    </div>
                ) : (
                    <PageSection
                        title={t('page.bed_type.head_title')}
                        icon={TagIcon}
                    >
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead className="border-b border-gray-100 bg-gray-50/50 text-xs font-bold tracking-wider text-gray-500 uppercase dark:border-neutral-700 dark:bg-neutral-700/30 dark:text-neutral-400">
                                    <tr>
                                        <th className="px-6 py-4">
                                            {t('page.bed_type.col_name')}
                                        </th>
                                        <th className="px-6 py-4">
                                            {t('page.bed_type.col_capacity')}
                                        </th>
                                        <th className="px-6 py-4 text-right">
                                            {t('common.actions')}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 text-sm dark:divide-neutral-700">
                                    {filteredTypes.map((type) => (
                                        <tr
                                            key={type.id}
                                            className="group transition-colors hover:bg-gray-50 dark:hover:bg-neutral-700/30"
                                        >
                                            <td className="px-6 py-4 font-semibold text-gray-900 dark:text-neutral-100">
                                                {type.name}
                                            </td>
                                            <td className="px-6 py-4">
                                                <StatusBadge
                                                    label={`${type.capacity} ${t('page.bed_type.patients')}`}
                                                    color="blue"
                                                />
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <button
                                                    onClick={() =>
                                                        openEditModal(type)
                                                    }
                                                    className="rounded-md border border-gray-200 bg-white p-1.5 shadow-sm hover:text-blue-600 dark:border-neutral-600 dark:bg-neutral-700 dark:hover:text-blue-400"
                                                >
                                                    <PencilSquareIcon className="h-3.5 w-3.5" />
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        handleDelete(type)
                                                    }
                                                    className="ml-1 rounded-md border border-gray-200 bg-white p-1.5 shadow-sm hover:text-red-600 dark:border-neutral-600 dark:bg-neutral-700 dark:hover:text-red-400"
                                                >
                                                    <TrashIcon className="h-3.5 w-3.5" />
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </PageSection>
                )}
            </div>

            <Modal
                isOpen={modalOpen}
                onClose={closeModal}
                title={
                    editingType
                        ? t('page.bed_type.edit_type')
                        : t('page.bed_type.add_type')
                }
            >
                <form onSubmit={handleSubmit} className="space-y-5">
                    <FormInput
                        name="name"
                        label={t('page.bed_type.bed_type_name')}
                        value={data.name}
                        onChange={(val) => setData('name', val)}
                        errors={errors}
                        placeholder="e.g. ICU, General, Private"
                        required
                    />
                    <FormInput
                        name="capacity"
                        label={t('page.bed_type.patient_capacity')}
                        type="number"
                        value={data.capacity}
                        onChange={(val) => setData('capacity', val)}
                        errors={errors}
                        min="1"
                        required
                    />
                    <div className="flex gap-4 pt-2">
                        <button
                            type="button"
                            onClick={closeModal}
                            className="flex-1 rounded-xl border border-gray-200 bg-white py-3 font-bold text-gray-500 dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            {t('common.cancel')}
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="flex-[2] rounded-xl bg-blue-600 py-3 font-bold text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            {processing
                                ? t('page.bed_type.saving')
                                : t('common.save_changes')}
                        </button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
