import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    MagnifyingGlassIcon,
    InboxStackIcon,
    TagIcon,
    BuildingOfficeIcon,
    ExclamationTriangleIcon,
    AdjustmentsHorizontalIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, router } from '@inertiajs/react';
import React, { useState } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Props {
    wards: any[];
    beds: any[];
    bedTypes: any[];
}

type ModalMode = 'add' | 'edit' | 'delete' | null;
type EntityType = 'ward' | 'bed' | 'type';

export default function WardManagement({ wards, beds, bedTypes }: Props) {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState<'wards' | 'beds' | 'types'>(
        'wards',
    );
    const [searchQuery, setSearchQuery] = useState('');

    // Modal state
    const [modalOpen, setModalOpen] = useState(false);
    const [modalEntity, setModalEntity] = useState<EntityType>('ward');
    const [modalMode, setModalMode] = useState<ModalMode>(null);
    const [selectedItem, setSelectedItem] = useState<any>(null);

    // Inertia Form
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
        entity_type: '' as EntityType,
        name: '',
        code: '',
        floor: '',
        capacity: '' as number | string,
        description: '', // Shared/Ward/Type
        ward_id: '',
        bed_type_id: '',
        status: 'Available', // Bed specific
    });

    const openModal = (entity: EntityType, mode: ModalMode, item?: any) => {
        setModalEntity(entity);
        setModalMode(mode);
        setSelectedItem(item || null);
        clearErrors();

        if (mode === 'edit' && item) {
            // Provide a full object to avoid cross-contamination of previous inputs
            setData({
                entity_type: entity,
                name: item.name || '',
                code: item.code || '',
                floor: item.floor || '',
                capacity: item.capacity || '',
                description: item.description || '',
                ward_id: item.ward_id?.toString() || '',
                bed_type_id: item.bed_type_id?.toString() || '',
                status: item.status || 'Available',
            });
        } else {
            reset();
            setData('entity_type', entity);
        }

        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setModalMode(null);
        setSelectedItem(null);
        reset();
        clearErrors();
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (modalMode === 'delete') {
            router.delete(route('infrastructure.destroy', selectedItem.id), {
                data: { entity_type: modalEntity },
                onSuccess: closeModal,
            });
        } else if (modalMode === 'edit') {
            put(route('infrastructure.update', selectedItem.id), {
                onSuccess: closeModal,
            });
        } else {
            post(route('infrastructure.store'), {
                onSuccess: closeModal,
            });
        }
    };

    // --- Filters ---
    const filteredWards = wards.filter(
        (w: any) =>
            w.name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
            w.code?.toLowerCase().includes(searchQuery.toLowerCase()),
    );
    const filteredBeds = beds.filter((b: any) =>
        b.code?.toLowerCase().includes(searchQuery.toLowerCase()),
    );
    const filteredTypes = bedTypes.filter((t: any) =>
        t.name?.toLowerCase().includes(searchQuery.toLowerCase()),
    );

    return (
        <AppLayout
            headerTitle={t('page.utilities.title')}
            headerSubtitle={t('page.utilities.subtitle')}
        >
            <Head title={t('page.utilities.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: '/' },
                        { label: t('page.utilities.head_title') },
                    ]}
                />

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="flex w-fit rounded-lg bg-gray-100 p-1">
                        <TabButton
                            active={activeTab === 'wards'}
                            onClick={() => setActiveTab('wards')}
                            label={t('page.utilities.wards_tab')}
                            icon={<BuildingOfficeIcon className="h-4 w-4" />}
                        />
                        <TabButton
                            active={activeTab === 'beds'}
                            onClick={() => setActiveTab('beds')}
                            label={t('page.utilities.beds_tab')}
                            icon={<InboxStackIcon className="h-4 w-4" />}
                        />
                        <TabButton
                            active={activeTab === 'types'}
                            onClick={() => setActiveTab('types')}
                            label={t('page.utilities.bed_types_tab')}
                            icon={<TagIcon className="h-4 w-4" />}
                        />
                    </div>

                    <div className="flex items-center gap-3">
                        <FormInput
                            name="search"
                            icon={MagnifyingGlassIcon}
                            placeholder={t(
                                'page.utilities.search_placeholder',
                                { tab: activeTab },
                            )}
                            value={searchQuery}
                            onChange={setSearchQuery}
                            className="w-48 sm:w-64"
                        />
                        <button className="rounded-lg border border-gray-200 bg-white p-2 text-gray-500 shadow-sm hover:text-gray-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-500 dark:hover:text-neutral-100">
                            <AdjustmentsHorizontalIcon className="h-5 w-5" />
                        </button>
                        <button
                            onClick={() =>
                                openModal(
                                    activeTab === 'types'
                                        ? 'type'
                                        : activeTab === 'beds'
                                          ? 'bed'
                                          : 'ward',
                                    'add',
                                )
                            }
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('page.utilities.add')}{' '}
                            <span className="hidden capitalize sm:inline">
                                {activeTab.slice(0, -1)}
                            </span>
                        </button>
                    </div>
                </div>

                <div className="mt-2">
                    {activeTab === 'wards' && (
                        <WardList
                            wards={filteredWards}
                            onEdit={(w: any) => openModal('ward', 'edit', w)}
                            onDelete={(w: any) =>
                                openModal('ward', 'delete', w)
                            }
                        />
                    )}
                    {activeTab === 'beds' && (
                        <BedGrid
                            beds={filteredBeds}
                            onEdit={(b: any) => openModal('bed', 'edit', b)}
                            onDelete={(b: any) => openModal('bed', 'delete', b)}
                        />
                    )}
                    {activeTab === 'types' && (
                        <BedTypeList
                            types={filteredTypes}
                            onEdit={(t: any) => openModal('type', 'edit', t)}
                            onDelete={(t: any) =>
                                openModal('type', 'delete', t)
                            }
                        />
                    )}

                    {/* Empty State */}
                    {((activeTab === 'wards' && filteredWards.length === 0) ||
                        (activeTab === 'beds' && filteredBeds.length === 0) ||
                        (activeTab === 'types' &&
                            filteredTypes.length === 0)) && (
                        <div className="rounded-2xl border-2 border-dashed border-gray-200 bg-white p-16 text-center dark:border-neutral-700 dark:bg-neutral-800">
                            <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-neutral-700">
                                <InboxStackIcon className="h-8 w-8 text-gray-400 dark:text-neutral-500" />
                            </div>
                            <h3 className="mt-4 text-lg font-bold text-gray-900 capitalize dark:text-neutral-100">
                                {t('page.utilities.no_records', {
                                    tab: activeTab,
                                })}
                            </h3>
                            <p className="mt-2 text-sm text-gray-500 dark:text-neutral-500">
                                {t('page.utilities.get_started')}
                            </p>
                            <button
                                onClick={() =>
                                    openModal(
                                        activeTab === 'types'
                                            ? 'type'
                                            : activeTab === 'beds'
                                              ? 'bed'
                                              : 'ward',
                                        'add',
                                    )
                                }
                                className="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition-colors hover:bg-blue-700"
                            >
                                <PlusIcon className="h-4 w-4" />{' '}
                                {t('page.utilities.add_first')}{' '}
                                <span className="capitalize">
                                    {activeTab.slice(0, -1)}
                                </span>
                            </button>
                        </div>
                    )}
                </div>
            </div>

            {/* Modal */}
            <Modal
                isOpen={modalOpen}
                onClose={closeModal}
                title={
                    modalMode
                        ? `${modalMode.charAt(0).toUpperCase() + modalMode.slice(1)} ${modalEntity}`
                        : ''
                }
                maxWidth="max-w-lg"
            >
                <form
                    onSubmit={handleSubmit}
                    className="max-h-[75vh] space-y-5 overflow-y-auto"
                >
                    {modalMode === 'delete' ? (
                        <div className="py-4 text-center">
                            <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                <ExclamationTriangleIcon className="h-6 w-6 text-red-600" />
                            </div>
                            <h3 className="mb-2 text-lg font-medium text-gray-900 dark:text-neutral-100">
                                {t('page.utilities.confirm_deletion')}
                            </h3>
                            <p className="text-sm text-gray-600 dark:text-neutral-400">
                                {t('page.utilities.confirm_delete_message', {
                                    name:
                                        selectedItem?.name ||
                                        selectedItem?.code,
                                })}
                            </p>
                        </div>
                    ) : (
                        <>
                            {modalEntity === 'ward' && (
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="col-span-2">
                                        <FormInput
                                            name="name"
                                            label={t(
                                                'page.utilities.ward_name',
                                            )}
                                            value={data.name}
                                            onChange={(v) => setData('name', v)}
                                            placeholder="e.g. Maternity Ward"
                                            errors={errors}
                                        />
                                    </div>
                                    <FormInput
                                        name="code"
                                        label={t('page.utilities.ward_code')}
                                        value={data.code}
                                        onChange={(v) => setData('code', v)}
                                        placeholder="e.g. W-MAT-01"
                                        errors={errors}
                                    />
                                    <FormInput
                                        name="capacity"
                                        label={t(
                                            'page.utilities.capacity_beds',
                                        )}
                                        type="number"
                                        value={data.capacity}
                                        onChange={(v) =>
                                            setData(
                                                'capacity',
                                                v ? Number(v) : '',
                                            )
                                        }
                                        min={1}
                                        errors={errors}
                                    />
                                    <div className="col-span-2">
                                        <FormInput
                                            name="floor"
                                            label={t(
                                                'page.utilities.floor_location',
                                            )}
                                            value={data.floor}
                                            onChange={(v) =>
                                                setData('floor', v)
                                            }
                                            placeholder="e.g. 2nd Floor, West Wing"
                                            errors={errors}
                                        />
                                    </div>
                                    <div className="col-span-2">
                                        <FormTextarea
                                            name="description"
                                            label={t('common.description')}
                                            value={data.description as string}
                                            onChange={(v) =>
                                                setData('description', v)
                                            }
                                            rows={3}
                                            placeholder="Brief description of the ward..."
                                            errors={errors}
                                        />
                                    </div>
                                </div>
                            )}

                            {modalEntity === 'bed' && (
                                <div className="space-y-4">
                                    <FormInput
                                        name="code"
                                        label={t('page.utilities.bed_code')}
                                        value={data.code}
                                        onChange={(v) => setData('code', v)}
                                        placeholder="e.g. BED-101"
                                        errors={errors}
                                    />
                                    <div className="grid grid-cols-2 gap-4">
                                        <FormSelect
                                            name="ward_id"
                                            label={t(
                                                'page.utilities.assign_ward',
                                            )}
                                            value={data.ward_id}
                                            onChange={(v) =>
                                                setData('ward_id', v)
                                            }
                                            placeholder={t(
                                                'page.utilities.select_ward',
                                            )}
                                            options={wards.map((w: any) => ({
                                                value: w.id.toString(),
                                                label: w.name,
                                            }))}
                                            errors={errors}
                                        />
                                        <FormSelect
                                            name="bed_type_id"
                                            label={t('page.utilities.bed_type')}
                                            value={data.bed_type_id}
                                            onChange={(v) =>
                                                setData('bed_type_id', v)
                                            }
                                            placeholder={t(
                                                'page.utilities.select_type',
                                            )}
                                            options={bedTypes.map(
                                                (bt: any) => ({
                                                    value: bt.id.toString(),
                                                    label: bt.name,
                                                }),
                                            )}
                                            errors={errors}
                                        />
                                    </div>
                                    <FormSelect
                                        name="status"
                                        label={t(
                                            'page.utilities.current_status',
                                        )}
                                        value={data.status}
                                        onChange={(v) => setData('status', v)}
                                        options={[
                                            {
                                                value: 'Available',
                                                label: t(
                                                    'page.utilities.available',
                                                ),
                                            },
                                            {
                                                value: 'Occupied',
                                                label: t(
                                                    'page.utilities.occupied',
                                                ),
                                            },
                                            {
                                                value: 'Maintenance',
                                                label: t(
                                                    'page.utilities.maintenance',
                                                ),
                                            },
                                        ]}
                                        errors={errors}
                                    />
                                </div>
                            )}

                            {modalEntity === 'type' && (
                                <div className="space-y-4">
                                    <FormInput
                                        name="name"
                                        label={t(
                                            'page.utilities.bed_type_name',
                                        )}
                                        value={data.name}
                                        onChange={(v) => setData('name', v)}
                                        placeholder="e.g. ICU Bed, Standard"
                                        errors={errors}
                                    />
                                    <FormInput
                                        name="capacity"
                                        label={t(
                                            'page.utilities.patient_capacity',
                                        )}
                                        type="number"
                                        value={data.capacity}
                                        onChange={(v) =>
                                            setData(
                                                'capacity',
                                                v ? Number(v) : '',
                                            )
                                        }
                                        min={1}
                                        errors={errors}
                                    />
                                </div>
                            )}
                        </>
                    )}

                    <div className="flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                        <button
                            type="button"
                            onClick={closeModal}
                            className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            {t('common.cancel')}
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className={`rounded-lg px-4 py-2 text-sm font-medium text-white shadow-sm transition-all disabled:opacity-75 ${
                                modalMode === 'delete'
                                    ? 'bg-red-600 hover:bg-red-700'
                                    : 'bg-blue-600 hover:bg-blue-700'
                            }`}
                        >
                            {processing
                                ? t('common.processing')
                                : modalMode === 'delete'
                                  ? t('page.utilities.delete_permanently')
                                  : t('common.save_changes')}
                        </button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}

// --- Sub-Components ---
function TabButton({ active, onClick, label, icon }: any) {
    return (
        <button
            onClick={onClick}
            className={`flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-all ${active ? 'bg-white text-blue-700 shadow-sm ring-1 ring-gray-200' : 'text-gray-600 hover:text-gray-900 dark:text-neutral-400 dark:hover:text-neutral-100'}`}
        >
            {icon} {label}
        </button>
    );
}

function WardList({ wards, onEdit, onDelete }: any) {
    const { t } = useTranslation();

    return (
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
            {wards.map((ward: any) => (
                <div
                    key={ward.id}
                    className="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                >
                    <div className="flex items-start justify-between">
                        <div>
                            <span className="rounded-md bg-blue-50 px-2.5 py-1 text-[10px] font-bold tracking-wider text-blue-700 uppercase dark:bg-blue-900/20 dark:text-blue-400">
                                {ward.code}
                            </span>
                            <h4 className="mt-3 text-base font-bold text-gray-900 dark:text-neutral-100">
                                {ward.name}
                            </h4>
                            {ward.floor && (
                                <p className="mt-1 text-xs text-gray-500 dark:text-neutral-500">
                                    {ward.floor}
                                </p>
                            )}
                        </div>
                        <div className="flex gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                            <button
                                onClick={() => onEdit(ward)}
                                className="rounded-md p-1.5 text-gray-400 transition-colors hover:bg-blue-50 hover:text-blue-600 dark:text-neutral-500 dark:hover:bg-blue-900/20 dark:hover:text-blue-400"
                            >
                                <PencilSquareIcon className="h-4 w-4" />
                            </button>
                            <button
                                onClick={() => onDelete(ward)}
                                className="rounded-md p-1.5 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:text-neutral-500 dark:hover:bg-red-900/20"
                            >
                                <TrashIcon className="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                    <div className="mt-5 flex justify-between rounded-lg border border-gray-100 bg-gray-50 p-3 text-xs font-bold text-gray-500 uppercase dark:border-neutral-700 dark:bg-neutral-700 dark:text-neutral-500">
                        <span>{t('page.utilities.current_capacity')}</span>
                        <span className="text-blue-600">
                            {ward.beds_count || 0} / {ward.capacity || 0}{' '}
                            {t('page.utilities.beds')}
                        </span>
                    </div>
                </div>
            ))}
        </div>
    );
}

function BedGrid({ beds, onEdit, onDelete }: any) {
    const { t } = useTranslation();

    return (
        <div className="grid grid-cols-2 gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:grid-cols-4 lg:grid-cols-6 dark:border-neutral-700 dark:bg-neutral-800">
            {beds.map((bed: any) => (
                <div
                    key={bed.id}
                    className={`group relative rounded-xl border p-4 text-center transition-all hover:shadow-md ${
                        bed.status === 'Available'
                            ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20'
                            : bed.status === 'Occupied'
                              ? 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20'
                              : bed.status === 'Maintenance'
                                ? 'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20'
                                : 'border-gray-200 bg-gray-50 dark:border-neutral-700 dark:bg-neutral-700'
                    }`}
                >
                    <div
                        className={`mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full ${
                            bed.status === 'Available'
                                ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'
                                : bed.status === 'Occupied'
                                  ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'
                                  : bed.status === 'Maintenance'
                                    ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400'
                                    : 'bg-gray-100 text-gray-600 dark:bg-neutral-700 dark:text-neutral-400'
                        }`}
                    >
                        <InboxStackIcon className="h-6 w-6" />
                    </div>
                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                        {bed.code}
                    </p>
                    <p
                        className="mt-0.5 truncate px-1 text-[10px] font-bold tracking-wide text-gray-500 uppercase dark:text-neutral-500"
                        title={bed.ward?.name}
                    >
                        {bed.ward?.name || t('page.utilities.no_ward')}
                    </p>

                    <span
                        className={`mt-2 inline-block rounded-full px-2 py-0.5 text-[10px] font-bold ${
                            bed.status === 'Available'
                                ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'
                                : bed.status === 'Occupied'
                                  ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'
                                  : bed.status === 'Maintenance'
                                    ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400'
                                    : 'bg-gray-100 text-gray-600 dark:bg-neutral-700 dark:text-neutral-400'
                        }`}
                    >
                        {bed.status === 'Available'
                            ? t('page.utilities.available')
                            : bed.status === 'Occupied'
                              ? t('page.utilities.occupied')
                              : bed.status === 'Maintenance'
                                ? t('page.utilities.maintenance')
                                : t('page.utilities.unknown')}
                    </span>

                    <div className="absolute top-2 right-2 flex flex-col gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                        <button
                            onClick={() => onEdit(bed)}
                            className="rounded-md border border-gray-200 bg-white p-1.5 shadow-sm hover:text-blue-600 dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <PencilSquareIcon className="h-3.5 w-3.5" />
                        </button>
                        <button
                            onClick={() => onDelete(bed)}
                            className="rounded-md border border-gray-200 bg-white p-1.5 shadow-sm hover:text-red-600 dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <TrashIcon className="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            ))}
        </div>
    );
}

function BedTypeList({ types, onEdit, onDelete }: any) {
    const { t } = useTranslation();

    return (
        <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <table className="w-full text-left">
                <thead className="border-b border-gray-200 bg-gray-50 text-xs font-bold tracking-wider text-gray-500 uppercase dark:border-neutral-700 dark:bg-neutral-700 dark:text-neutral-500">
                    <tr>
                        <th className="px-6 py-4">
                            {t('page.utilities.type_name')}
                        </th>
                        <th className="px-6 py-4">
                            {t('page.utilities.capacity')}
                        </th>
                        <th className="px-6 py-4 text-right">
                            {t('common.actions')}
                        </th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-100 text-sm dark:divide-neutral-700">
                    {types.map((type: any) => (
                        <tr
                            key={type.id}
                            className="group transition-colors hover:bg-gray-50 dark:hover:bg-neutral-700/80"
                        >
                            <td className="px-6 py-4 font-semibold text-gray-900 dark:text-neutral-100">
                                {type.name}
                            </td>
                            <td className="px-6 py-4 text-gray-600 dark:text-neutral-400">
                                <span className="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-neutral-700 dark:text-neutral-300">
                                    {type.capacity}{' '}
                                    {t('page.utilities.patients')}
                                </span>
                            </td>
                            <td className="px-6 py-4 text-right">
                                <button
                                    onClick={() => onEdit(type)}
                                    className="p-2 text-gray-400 transition-colors hover:text-blue-600 dark:text-neutral-500 dark:hover:text-blue-400"
                                >
                                    <PencilSquareIcon className="h-4.5 w-4.5" />
                                </button>
                                <button
                                    onClick={() => onDelete(type)}
                                    className="p-2 text-gray-400 transition-colors hover:text-red-600 dark:text-neutral-500 dark:hover:text-red-400"
                                >
                                    <TrashIcon className="h-4.5 w-4.5" />
                                </button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
