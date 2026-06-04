import {
    PlusIcon, PencilSquareIcon, TrashIcon,
    HomeIcon, ChevronRightIcon, XMarkIcon,
    MagnifyingGlassIcon, InboxStackIcon,
    TagIcon, BuildingOfficeIcon, ExclamationTriangleIcon,
    AdjustmentsHorizontalIcon
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Props {
    wards: any[];
    beds: any[];
    bedTypes: any[];
}

type ModalMode = 'add' | 'edit' | 'delete' | null;
type EntityType = 'ward' | 'bed' | 'type';

export default function WardManagement({ wards, beds, bedTypes }: Props) {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState<'wards' | 'beds' | 'types'>('wards');
    const [searchQuery, setSearchQuery] = useState('');

    // Modal state
    const [modalOpen, setModalOpen] = useState(false);
    const [modalEntity, setModalEntity] = useState<EntityType>('ward');
    const [modalMode, setModalMode] = useState<ModalMode>(null);
    const [selectedItem, setSelectedItem] = useState<any>(null);

    // Inertia Form
    const { data, setData, post, put, delete: destroy, processing, errors, reset, clearErrors } = useForm({
        entity_type: '' as EntityType,
        name: '', code: '', floor: '', capacity: '' as number | string, description: '', // Shared/Ward/Type
        ward_id: '', bed_type_id: '', status: 'Available', // Bed specific
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
            destroy(route('infrastructure.destroy', selectedItem.id), {
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
    const filteredWards = wards.filter(w => w.name?.toLowerCase().includes(searchQuery.toLowerCase()) || w.code?.toLowerCase().includes(searchQuery.toLowerCase()));
    const filteredBeds = beds.filter(b => b.code?.toLowerCase().includes(searchQuery.toLowerCase()));
    const filteredTypes = bedTypes.filter(t => t.name?.toLowerCase().includes(searchQuery.toLowerCase()));

    // Shared CSS Classes
    const inputClasses = "mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm";
    const labelClasses = "block text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase tracking-wide";

    return (
        <AppLayout
            headerTitle={t('page.utilities.title')}
            headerSubtitle={t('page.utilities.subtitle')}
        >
            <Head title={t('page.utilities.head_title')} />

            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 py-6">
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-medium">{t('page.utilities.head_title')}</li>
                    </ol>
                </nav>

                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="flex p-1 bg-gray-100 rounded-lg w-fit">
                        <TabButton active={activeTab === 'wards'} onClick={() => setActiveTab('wards')} label={t('page.utilities.wards_tab')} icon={<BuildingOfficeIcon className="w-4 h-4"/>} />
                        <TabButton active={activeTab === 'beds'} onClick={() => setActiveTab('beds')} label={t('page.utilities.beds_tab')} icon={<InboxStackIcon className="w-4 h-4"/>} />
                        <TabButton active={activeTab === 'types'} onClick={() => setActiveTab('types')} label={t('page.utilities.bed_types_tab')} icon={<TagIcon className="w-4 h-4"/>} />
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                placeholder={t('page.utilities.search_placeholder', { tab: activeTab })}
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="pl-9 pr-4 py-2 w-48 sm:w-64 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 shadow-sm rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                        </div>
                        <button className="p-2 border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 rounded-lg text-gray-500 dark:text-neutral-500 hover:text-gray-900 dark:text-neutral-100 dark:hover:text-neutral-100 shadow-sm">
                            <AdjustmentsHorizontalIcon className="w-5 h-5" />
                        </button>
                        <button
                            onClick={() => openModal(activeTab === 'types' ? 'type' : activeTab === 'beds' ? 'bed' : 'ward', 'add')}
                            className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-sm transition-all"
                        >
                            <PlusIcon className="w-4 h-4" /> {t('page.utilities.add')} <span className="hidden sm:inline capitalize">{activeTab.slice(0, -1)}</span>
                        </button>
                    </div>
                </div>

                <div className="mt-2">
                    {activeTab === 'wards' && (
                        <WardList wards={filteredWards} onEdit={(w) => openModal('ward', 'edit', w)} onDelete={(w) => openModal('ward', 'delete', w)} />
                    )}
                    {activeTab === 'beds' && (
                        <BedGrid beds={filteredBeds} onEdit={(b) => openModal('bed', 'edit', b)} onDelete={(b) => openModal('bed', 'delete', b)} />
                    )}
                    {activeTab === 'types' && (
                        <BedTypeList types={filteredTypes} onEdit={(t) => openModal('type', 'edit', t)} onDelete={(t) => openModal('type', 'delete', t)} />
                    )}

                    {/* Empty State */}
                    {((activeTab === 'wards' && filteredWards.length === 0) ||
                      (activeTab === 'beds' && filteredBeds.length === 0) ||
                      (activeTab === 'types' && filteredTypes.length === 0)) && (
                        <div className="text-center py-16 bg-white dark:bg-neutral-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-neutral-700">
                            <InboxStackIcon className="w-12 h-12 text-gray-300 mx-auto mb-4" />
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-neutral-100 capitalize">{t('page.utilities.no_records', { tab: activeTab })}</h3>
                            <p className="text-gray-500 dark:text-neutral-500 text-sm mt-1 mb-4">{t('page.utilities.get_started')}</p>
                            <button onClick={() => openModal(activeTab === 'types' ? 'type' : activeTab === 'beds' ? 'bed' : 'ward', 'add')} className="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold shadow-sm">
                                <PlusIcon className="w-4 h-4" /> {t('page.utilities.add_first')} <span className="capitalize">{activeTab.slice(0, -1)}</span>
                            </button>
                        </div>
                    )}
                </div>
            </div>

            {/* Modal */}
            {modalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onClick={closeModal}></div>
                    <div className="relative bg-white dark:bg-neutral-800 rounded-xl w-full max-w-lg shadow-xl border border-gray-200 dark:border-neutral-700 overflow-hidden">
                        <div className="px-6 py-4 border-b border-gray-100 dark:border-neutral-700 flex justify-between items-center bg-gray-50 dark:bg-neutral-700">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-neutral-100 capitalize">{modalMode} {modalEntity}</h3>
                            <button onClick={closeModal} className="p-1.5 text-gray-400 dark:text-neutral-500 hover:text-gray-600 dark:text-neutral-400 dark:hover:text-neutral-400 hover:bg-gray-200 rounded-lg transition-colors"><XMarkIcon className="w-5 h-5"/></button>
                        </div>

                        <form onSubmit={handleSubmit} className="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
                            {modalMode === 'delete' ? (
                                <div className="text-center py-4">
                                    <div className="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                                        <ExclamationTriangleIcon className="w-6 h-6 text-red-600" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 dark:text-neutral-100 mb-2">{t('page.utilities.confirm_deletion')}</h3>
                                    <p className="text-gray-600 dark:text-neutral-400 text-sm">
                                        {t('page.utilities.confirm_delete_message', { name: selectedItem?.name || selectedItem?.code })}
                                    </p>
                                </div>
                            ) : (
                                <>
                                    {modalEntity === 'ward' && (
                                        <div className="grid grid-cols-2 gap-4">
                                            <div className="col-span-2">
                                                <label className={labelClasses}>{t('page.utilities.ward_name')}</label>
                                                <input type="text" value={data.name} onChange={e => setData('name', e.target.value)} className={inputClasses} placeholder="e.g. Maternity Ward" />
                                                {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                                            </div>
                                            <div>
                                                <label className={labelClasses}>{t('page.utilities.ward_code')}</label>
                                                <input type="text" value={data.code} onChange={e => setData('code', e.target.value)} className={inputClasses} placeholder="e.g. W-MAT-01" />
                                                {errors.code && <p className="text-red-500 text-xs mt-1">{errors.code}</p>}
                                            </div>
                                            <div>
                                                <label className={labelClasses}>{t('page.utilities.capacity_beds')}</label>
                                                <input type="number" value={data.capacity} onChange={e => setData('capacity', e.target.value ? Number(e.target.value) : '')} className={inputClasses} min="1" />
                                                {errors.capacity && <p className="text-red-500 text-xs mt-1">{errors.capacity}</p>}
                                            </div>
                                            <div className="col-span-2">
                                                <label className={labelClasses}>{t('page.utilities.floor_location')}</label>
                                                <input type="text" value={data.floor} onChange={e => setData('floor', e.target.value)} className={inputClasses} placeholder="e.g. 2nd Floor, West Wing" />
                                                {errors.floor && <p className="text-red-500 text-xs mt-1">{errors.floor}</p>}
                                            </div>
                                            <div className="col-span-2">
                                                <label className={labelClasses}>{t('common.description')}</label>
                                                <textarea value={data.description} onChange={e => setData('description', e.target.value)} rows={3} className={inputClasses} placeholder="Brief description of the ward..." />
                                                {errors.description && <p className="text-red-500 text-xs mt-1">{errors.description}</p>}
                                            </div>
                                        </div>
                                    )}

                                    {modalEntity === 'bed' && (
                                        <div className="space-y-4">
                                            <div>
                                                <label className={labelClasses}>{t('page.utilities.bed_code')}</label>
                                                <input type="text" value={data.code} onChange={e => setData('code', e.target.value)} className={inputClasses} placeholder="e.g. BED-101" />
                                                {errors.code && <p className="text-red-500 text-xs mt-1">{errors.code}</p>}
                                            </div>
                                            <div className="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label className={labelClasses}>{t('page.utilities.assign_ward')}</label>
                                                    <select value={data.ward_id} onChange={e => setData('ward_id', e.target.value)} className={inputClasses}>
                                                        <option value="">{t('page.utilities.select_ward')}</option>
                                                        {wards.map(w => <option key={w.id} value={w.id}>{w.name}</option>)}
                                                    </select>
                                                    {errors.ward_id && <p className="text-red-500 text-xs mt-1">{errors.ward_id}</p>}
                                                </div>
                                                <div>
                                                    <label className={labelClasses}>{t('page.utilities.bed_type')}</label>
                                                    <select value={data.bed_type_id} onChange={e => setData('bed_type_id', e.target.value)} className={inputClasses}>
                                                        <option value="">{t('page.utilities.select_type')}</option>
                                                        {bedTypes.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
                                                    </select>
                                                    {errors.bed_type_id && <p className="text-red-500 text-xs mt-1">{errors.bed_type_id}</p>}
                                                </div>
                                            </div>
                                            <div>
                                                <label className={labelClasses}>{t('page.utilities.current_status')}</label>
                                                <select value={data.status} onChange={e => setData('status', e.target.value)} className={inputClasses}>
                                                    <option value="Available">{t('page.utilities.available')}</option>
                                                    <option value="Occupied">{t('page.utilities.occupied')}</option>
                                                    <option value="Maintenance">{t('page.utilities.maintenance')}</option>
                                                </select>
                                                {errors.status && <p className="text-red-500 text-xs mt-1">{errors.status}</p>}
                                            </div>
                                        </div>
                                    )}

                                    {modalEntity === 'type' && (
                                        <div className="space-y-4">
                                            <div>
                                                <label className={labelClasses}>{t('page.utilities.bed_type_name')}</label>
                                                <input type="text" value={data.name} onChange={e => setData('name', e.target.value)} className={inputClasses} placeholder="e.g. ICU Bed, Standard" />
                                                {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                                            </div>
                                            <div>
                                                <label className={labelClasses}>{t('page.utilities.patient_capacity')}</label>
                                                <input type="number" value={data.capacity} onChange={e => setData('capacity', e.target.value ? Number(e.target.value) : '')} className={inputClasses} min="1" />
                                                {errors.capacity && <p className="text-red-500 text-xs mt-1">{errors.capacity}</p>}
                                            </div>
                                        </div>
                                    )}
                                </>
                            )}

                            <div className="flex justify-end gap-3 pt-4 border-t mt-6">
                                <button type="button" onClick={closeModal} className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 shadow-sm">
                                    {t('common.cancel')}
                                </button>
                                <button type="submit" disabled={processing} className={`px-4 py-2 text-sm font-medium text-white rounded-lg shadow-sm transition-all disabled:opacity-75 ${modalMode === 'delete' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'}`}>
                                    {processing ? t('common.processing') : modalMode === 'delete' ? t('page.utilities.delete_permanently') : t('common.save_changes')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}

// --- Sub-Components ---
function TabButton({ active, onClick, label, icon }: any) {
    return (
        <button onClick={onClick} className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all ${active ? 'bg-white text-blue-700 shadow-sm ring-1 ring-gray-200' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:text-neutral-100 dark:hover:text-neutral-100'}`}>
            {icon} {label}
        </button>
    );
}

function WardList({ wards, onEdit, onDelete }: any) {
    return (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {wards.map((ward: any) => (
                <div key={ward.id} className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm group hover:shadow-md transition-shadow">
                    <div className="flex justify-between items-start">
                        <div>
                            <span className="text-[10px] font-bold tracking-wider text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md uppercase">{ward.code}</span>
                            <h4 className="text-base font-bold text-gray-900 dark:text-neutral-100 mt-3">{ward.name}</h4>
                            {ward.floor && <p className="text-xs text-gray-500 dark:text-neutral-500 mt-1">{ward.floor}</p>}
                        </div>
                        <div className="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onClick={() => onEdit(ward)} className="p-1.5 text-gray-400 dark:text-neutral-500 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors"><PencilSquareIcon className="w-4 h-4"/></button>
                            <button onClick={() => onDelete(ward)} className="p-1.5 text-gray-400 dark:text-neutral-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors"><TrashIcon className="w-4 h-4"/></button>
                        </div>
                    </div>
                    <div className="mt-5 flex justify-between text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase bg-gray-50 dark:bg-neutral-700 p-3 rounded-lg border border-gray-100 dark:border-neutral-700">
                        <span>{t('page.utilities.current_capacity')}</span>
                        <span className="text-blue-600">{ward.beds_count || 0} / {ward.capacity || 0} {t('page.utilities.beds')}</span>
                    </div>
                </div>
            ))}
        </div>
    );
}

function BedGrid({ beds, onEdit, onDelete }: any) {
    const getStatusColor = (status: string) => {
        switch(status) {
            case 'Available': return 'bg-emerald-100 text-emerald-600 border-emerald-200';
            case 'Occupied': return 'bg-red-100 text-red-600 border-red-200';
            case 'Maintenance': return 'bg-amber-100 text-amber-600 border-amber-200';
            default: return 'bg-gray-100 text-gray-600 dark:text-neutral-400 border-gray-200 dark:border-neutral-700';
        }
    };

    return (
        <div className="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
            {beds.map((bed: any) => (
                <div key={bed.id} className={`relative group p-4 rounded-xl border ${getStatusColor(bed.status).replace(/text-.*|bg-.*/g, '')} bg-gray-50 dark:bg-neutral-700 text-center hover:shadow-md transition-all`}>
                    <div className={`mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-3 ${getStatusColor(bed.status).split(' ').slice(0,2).join(' ')}`}>
                        <InboxStackIcon className="w-6 h-6" />
                    </div>
                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{bed.code}</p>
                    <p className="text-[10px] font-bold text-gray-500 dark:text-neutral-500 uppercase tracking-wide mt-0.5 truncate px-1" title={bed.ward?.name}>{bed.ward?.name || t('page.utilities.no_ward')}</p>

                    <span className={`inline-block mt-2 text-[10px] font-bold px-2 py-0.5 rounded-full ${getStatusColor(bed.status).split(' ').slice(0,2).join(' ')}`}>
                        {bed.status || t('page.utilities.unknown')}
                    </span>

                    <div className="absolute top-2 right-2 flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onClick={() => onEdit(bed)} className="p-1.5 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-md shadow-sm hover:text-blue-600"><PencilSquareIcon className="w-3.5 h-3.5"/></button>
                        <button onClick={() => onDelete(bed)} className="p-1.5 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-md shadow-sm hover:text-red-600"><TrashIcon className="w-3.5 h-3.5"/></button>
                    </div>
                </div>
            ))}
        </div>
    );
}

function BedTypeList({ types, onEdit, onDelete }: any) {
    return (
        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <table className="w-full text-left">
                <thead className="bg-gray-50 dark:bg-neutral-700 border-b border-gray-200 dark:border-neutral-700 text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase tracking-wider">
                    <tr>
                        <th className="px-6 py-4">{t('page.utilities.type_name')}</th>
                        <th className="px-6 py-4">{t('page.utilities.capacity')}</th>
                        <th className="px-6 py-4 text-right">{t('common.actions')}</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-100 dark:divide-neutral-700 text-sm">
                    {types.map((type: any) => (
                        <tr key={type.id} className="hover:bg-gray-50 dark:hover:bg-neutral-700/80 transition-colors group">
                            <td className="px-6 py-4 font-semibold text-gray-900 dark:text-neutral-100">{type.name}</td>
                            <td className="px-6 py-4 text-gray-600 dark:text-neutral-400">
                                <span className="bg-gray-100 text-gray-700 dark:text-neutral-300 px-2.5 py-1 rounded-md text-xs font-semibold">
                                    {type.capacity} {t('page.utilities.patients')}
                                </span>
                            </td>
                            <td className="px-6 py-4 text-right">
                                <button onClick={() => onEdit(type)} className="p-2 text-gray-400 dark:text-neutral-500 hover:text-blue-600 transition-colors"><PencilSquareIcon className="w-4.5 h-4.5"/></button>
                                <button onClick={() => onDelete(type)} className="p-2 text-gray-400 dark:text-neutral-500 hover:text-red-600 transition-colors"><TrashIcon className="w-4.5 h-4.5"/></button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}