import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    InboxStackIcon,
    AdjustmentsHorizontalIcon,
    MagnifyingGlassIcon,
    ArrowsRightLeftIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Props {
    beds: any[];
    wards: any[];
    bedTypes: any[];
}

export default function BedIndex({ beds, wards, bedTypes }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [transferModalOpen, setTransferModalOpen] = useState(false);
    const [selectedBed, setSelectedBed] = useState<any>(null);
    const [editingBed, setEditingBed] = useState<any>(null);

    // Form for create/edit
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
        code: '',
        ward_id: '',
        bed_type_id: '',
        status: 'Available',
    });

    // Form for transfer
    const {
        data: transferData,
        setData: setTransferData,
        post: transferPost,
        processing: transferProcessing,
        reset: resetTransfer,
    } = useForm({
        ward_id: '',
    });

    const openCreateModal = () => {
        setEditingBed(null);
        reset();
        clearErrors();
        setModalOpen(true);
    };

    const openEditModal = (bed: any) => {
        setEditingBed(bed);
        setData({
            code: bed.code || '',
            ward_id: bed.ward_id?.toString() || '',
            bed_type_id: bed.bed_type_id?.toString() || '',
            status: bed.status || 'Available',
        });
        clearErrors();
        setModalOpen(true);
    };

    const openTransferModal = (bed: any) => {
        setSelectedBed(bed);
        resetTransfer();
        setTransferData('ward_id', bed.ward_id?.toString() || '');
        setTransferModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setEditingBed(null);
        reset();
        clearErrors();
    };

    const closeTransferModal = () => {
        setTransferModalOpen(false);
        setSelectedBed(null);
        resetTransfer();
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (editingBed) {
            put(route('beds.update', editingBed.id), { onSuccess: closeModal });
        } else {
            post(route('beds.store'), { onSuccess: closeModal });
        }
    };

    const handleTransfer = (e: React.FormEvent) => {
        e.preventDefault();

        if (selectedBed) {
            transferPost(route('beds.transfer', selectedBed.id), {
                onSuccess: closeTransferModal,
            });
        }
    };

    const handleDelete = (bed: any) => {
        if (confirm(t('page.beds.delete_confirm', { code: bed.code }))) {
            destroy(route('beds.destroy', bed.id));
        }
    };

    const filteredBeds = beds.filter(
        (b) =>
            b.code?.toLowerCase().includes(searchQuery.toLowerCase()) ||
            b.ward?.name?.toLowerCase().includes(searchQuery.toLowerCase()),
    );

    const getIconColors = (status: string): string => {
        switch (status) {
            case 'Available':
                return 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400';
            case 'Occupied':
                return 'bg-red-100 text-red-600 dark:bg-red-900/20 dark:text-red-400';
            case 'Maintenance':
                return 'bg-amber-100 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400';
            default:
                return 'bg-gray-100 text-gray-600 dark:bg-neutral-700 dark:text-neutral-400';
        }
    };

    const getBadgeColor = (
        status: string,
    ): 'emerald' | 'red' | 'amber' | 'gray' => {
        switch (status) {
            case 'Available':
                return 'emerald';
            case 'Occupied':
                return 'red';
            case 'Maintenance':
                return 'amber';
            default:
                return 'gray';
        }
    };

    return (
        <AppLayout
            headerTitle={t('page.beds.title')}
            headerSubtitle={t('page.beds.subtitle')}
        >
            <Head title={t('page.beds.head_title')} />
            <div className="mx-auto max-w-7xl space-y-6">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: t('page.beds.head_title') },
                    ]}
                />

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <h2 className="text-2xl font-bold text-gray-900 dark:text-neutral-100">
                        {t('page.beds.beds_heading')}
                    </h2>
                    <div className="flex items-center gap-3">
                        <FormInput
                            name="search"
                            icon={MagnifyingGlassIcon}
                            placeholder={t('page.beds.search_placeholder')}
                            value={searchQuery}
                            onChange={setSearchQuery}
                            className="w-48 sm:w-64"
                        />
                        <button className="rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:text-gray-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:text-neutral-200">
                            <AdjustmentsHorizontalIcon className="h-5 w-5" />
                        </button>
                        <button
                            onClick={openCreateModal}
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('page.beds.add_bed')}
                        </button>
                    </div>
                </div>

                {filteredBeds.length === 0 ? (
                    <>
                        <EmptyState
                            icon={InboxStackIcon}
                            title={t('page.beds.no_beds')}
                        />
                        <div className="flex justify-center">
                            <button
                                onClick={openCreateModal}
                                className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700"
                            >
                                <PlusIcon className="h-4 w-4" />{' '}
                                {t('page.beds.add_first_bed')}
                            </button>
                        </div>
                    </>
                ) : (
                    <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                        {filteredBeds.map((bed) => (
                            <div
                                key={bed.id}
                                className="group relative rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:shadow-neutral-900/50"
                            >
                                <div
                                    className={`mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full ${getIconColors(bed.status)}`}
                                >
                                    <InboxStackIcon className="h-6 w-6" />
                                </div>
                                <p className="text-center text-sm font-bold text-gray-900 dark:text-neutral-100">
                                    {bed.code}
                                </p>
                                <p
                                    className="mt-0.5 truncate text-center text-[10px] font-bold tracking-wide text-gray-500 uppercase dark:text-neutral-400"
                                    title={bed.ward?.name}
                                >
                                    {bed.ward?.name || t('page.beds.no_ward')}
                                </p>
                                <div className="mt-2 flex justify-center">
                                    <StatusBadge
                                        label={
                                            bed.status || t('page.beds.unknown')
                                        }
                                        color={getBadgeColor(bed.status)}
                                    />
                                </div>
                                <div className="absolute top-2 right-2 flex flex-col gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    <button
                                        onClick={() => openEditModal(bed)}
                                        className="rounded-md border border-gray-200 bg-white p-1.5 shadow-sm hover:text-blue-600 dark:border-neutral-600 dark:bg-neutral-700 dark:hover:text-blue-400"
                                    >
                                        <PencilSquareIcon className="h-3.5 w-3.5" />
                                    </button>
                                    <button
                                        onClick={() => openTransferModal(bed)}
                                        className="rounded-md border border-gray-200 bg-white p-1.5 shadow-sm hover:text-green-600 dark:border-neutral-600 dark:bg-neutral-700 dark:hover:text-green-400"
                                    >
                                        <ArrowsRightLeftIcon className="h-3.5 w-3.5" />
                                    </button>
                                    <button
                                        onClick={() => handleDelete(bed)}
                                        className="rounded-md border border-gray-200 bg-white p-1.5 shadow-sm hover:text-red-600 dark:border-neutral-600 dark:bg-neutral-700 dark:hover:text-red-400"
                                    >
                                        <TrashIcon className="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Create/Edit Modal */}
            <Modal
                isOpen={modalOpen}
                onClose={closeModal}
                title={
                    editingBed
                        ? t('page.beds.edit_bed')
                        : t('page.beds.add_bed')
                }
            >
                <form onSubmit={handleSubmit} className="space-y-5">
                    <FormInput
                        name="code"
                        label={t('page.beds.bed_code')}
                        value={data.code}
                        onChange={(v) => setData('code', v)}
                        placeholder="e.g. BED-101"
                        errors={errors}
                    />
                    <div className="grid grid-cols-2 gap-4">
                        <FormSelect
                            name="ward_id"
                            label={t('page.beds.assign_ward')}
                            value={data.ward_id}
                            onChange={(v) => setData('ward_id', v)}
                            options={wards.map((w) => ({
                                value: w.id,
                                label: w.name,
                            }))}
                            placeholder={t('page.beds.select_ward')}
                            errors={errors}
                        />
                        <FormSelect
                            name="bed_type_id"
                            label={t('page.beds.bed_type')}
                            value={data.bed_type_id}
                            onChange={(v) => setData('bed_type_id', v)}
                            options={bedTypes.map((bt) => ({
                                value: bt.id,
                                label: bt.name,
                            }))}
                            placeholder={t('page.beds.select_type')}
                            errors={errors}
                        />
                    </div>
                    <FormSelect
                        name="status"
                        label={t('page.beds.status')}
                        value={data.status}
                        onChange={(v) => setData('status', v)}
                        options={[
                            {
                                value: 'Available',
                                label: t('page.beds.available'),
                            },
                            {
                                value: 'Occupied',
                                label: t('page.beds.occupied'),
                            },
                            {
                                value: 'Maintenance',
                                label: t('page.beds.maintenance'),
                            },
                        ]}
                        errors={errors}
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
                                ? t('page.beds.saving')
                                : t('common.save_changes')}
                        </button>
                    </div>
                </form>
            </Modal>

            {/* Transfer Modal */}
            <Modal
                isOpen={transferModalOpen}
                onClose={closeTransferModal}
                title={t('page.beds.transfer_bed')}
                maxWidth="max-w-md"
            >
                <form onSubmit={handleTransfer} className="space-y-5">
                    <div>
                        <label className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                            {t('page.beds.bed_label')}:{' '}
                            <span className="font-bold text-gray-900 dark:text-neutral-100">
                                {selectedBed?.code}
                            </span>
                        </label>
                        <p className="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                            {t('page.beds.current_ward')}:{' '}
                            {selectedBed?.ward?.name || t('page.beds.none')}
                        </p>
                    </div>
                    <div>
                        <label
                            htmlFor="transfer-ward"
                            className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400"
                        >
                            {t('page.beds.transfer_to_ward')}
                        </label>
                        <div className="relative mt-1.5">
                            <select
                                id="transfer-ward"
                                value={transferData.ward_id}
                                onChange={(e) =>
                                    setTransferData('ward_id', e.target.value)
                                }
                                className="w-full appearance-none rounded-lg border border-gray-200 bg-white py-3 pr-10 pl-4 text-sm font-semibold transition-all outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                                required
                            >
                                <option value="">
                                    {t('page.beds.select_new_ward')}
                                </option>
                                {wards.map((w) => (
                                    <option
                                        key={w.id}
                                        value={w.id}
                                        disabled={w.id === selectedBed?.ward_id}
                                    >
                                        {w.name}{' '}
                                        {w.id === selectedBed?.ward_id
                                            ? t('page.beds.current')
                                            : ''}
                                    </option>
                                ))}
                            </select>
                            <div className="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2">
                                <svg
                                    className="h-4 w-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div className="flex gap-4 pt-2">
                        <button
                            type="button"
                            onClick={closeTransferModal}
                            className="flex-1 rounded-xl border border-gray-200 bg-white py-3 font-bold text-gray-500 dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            {t('common.cancel')}
                        </button>
                        <button
                            type="submit"
                            disabled={transferProcessing}
                            className="flex-[2] rounded-xl bg-blue-600 py-3 font-bold text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            {transferProcessing
                                ? t('page.beds.transferring')
                                : t('page.beds.confirm_transfer')}
                        </button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
