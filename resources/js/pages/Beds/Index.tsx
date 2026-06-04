import { PlusIcon, PencilSquareIcon, TrashIcon, HomeIcon, ChevronRightIcon, XMarkIcon, InboxStackIcon, AdjustmentsHorizontalIcon, MagnifyingGlassIcon, ArrowsRightLeftIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

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
    const { data, setData, post, put, delete: destroy, processing, errors, reset, clearErrors } = useForm({
        code: '',
        ward_id: '',
        bed_type_id: '',
        status: 'Available',
    });

    // Form for transfer
    const { data: transferData, setData: setTransferData, post: transferPost, processing: transferProcessing, reset: resetTransfer } = useForm({
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

    const filteredBeds = beds.filter(b =>
        b.code?.toLowerCase().includes(searchQuery.toLowerCase()) ||
        b.ward?.name?.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const getStatusColor = (status: string) => {
        switch(status) {
            case 'Available': return 'bg-emerald-100 text-emerald-600 border-emerald-200';
            case 'Occupied': return 'bg-red-100 text-red-600 border-red-200';
            case 'Maintenance': return 'bg-amber-100 text-amber-600 border-amber-200';
            default: return 'bg-gray-100 text-gray-600 border-gray-200';
        }
    };

    const inputClasses = "mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm";
    const labelClasses = "block text-xs font-bold text-gray-500 uppercase tracking-wide";

    return (
        <AppLayout headerTitle={t('page.beds.title')} headerSubtitle={t('page.beds.subtitle')}>
            <Head title={t('page.beds.head_title')} />
            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 py-6">
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-medium">{t('page.beds.head_title')}</li>
                    </ol>
                </nav>

                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h2 className="text-2xl font-bold text-gray-900">{t('page.beds.beds_heading')}</h2>
                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                type="text"
                                placeholder={t('page.beds.search_placeholder')}
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="pl-9 pr-4 py-2 w-48 sm:w-64 bg-white border border-gray-200 shadow-sm rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                        </div>
                        <button className="p-2 border border-gray-200 bg-white rounded-lg text-gray-500 hover:text-gray-900">
                            <AdjustmentsHorizontalIcon className="w-5 h-5" />
                        </button>
                        <button
                            onClick={openCreateModal}
                            className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700"
                        >
                            <PlusIcon className="w-4 h-4" /> {t('page.beds.add_bed')}
                        </button>
                    </div>
                </div>

                {filteredBeds.length === 0 ? (
                    <div className="text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-200">
                        <InboxStackIcon className="w-12 h-12 text-gray-300 mx-auto mb-4" />
                        <h3 className="text-lg font-semibold text-gray-900">{t('page.beds.no_beds')}</h3>
                        <button onClick={openCreateModal} className="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">
                            <PlusIcon className="w-4 h-4" /> {t('page.beds.add_first_bed')}
                        </button>
                    </div>
                ) : (
                    <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                        {filteredBeds.map((bed) => (
                            <div key={bed.id} className="relative group p-4 rounded-xl border bg-white shadow-sm hover:shadow-md transition-all">
                                <div className={`mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-3 ${getStatusColor(bed.status).split(' ').slice(0,2).join(' ')}`}>
                                    <InboxStackIcon className="w-6 h-6" />
                                </div>
                                <p className="text-sm font-bold text-gray-900 text-center">{bed.code}</p>
                                <p className="text-[10px] font-bold text-gray-500 uppercase tracking-wide text-center mt-0.5 truncate" title={bed.ward?.name}>
                                    {bed.ward?.name || t('page.beds.no_ward')}
                                </p>
                                <div className="flex justify-center mt-2">
                                    <span className={`inline-block text-[10px] font-bold px-2 py-0.5 rounded-full ${getStatusColor(bed.status)}`}>
                                        {bed.status || t('page.beds.unknown')}
                                    </span>
                                </div>
                                <div className="absolute top-2 right-2 flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onClick={() => openEditModal(bed)} className="p-1.5 bg-white border border-gray-200 rounded-md shadow-sm hover:text-blue-600">
                                        <PencilSquareIcon className="w-3.5 h-3.5" />
                                    </button>
                                    <button onClick={() => openTransferModal(bed)} className="p-1.5 bg-white border border-gray-200 rounded-md shadow-sm hover:text-green-600">
                                        <ArrowsRightLeftIcon className="w-3.5 h-3.5" />
                                    </button>
                                    <button onClick={() => handleDelete(bed)} className="p-1.5 bg-white border border-gray-200 rounded-md shadow-sm hover:text-red-600">
                                        <TrashIcon className="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Create/Edit Modal */}
            {modalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onClick={closeModal}></div>
                    <div className="relative bg-white rounded-xl w-full max-w-lg shadow-xl border border-gray-200 overflow-hidden">
                        <div className="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 className="text-lg font-semibold text-gray-900">{editingBed ? t('page.beds.edit_bed') : t('page.beds.add_bed')}</h3>
                            <button onClick={closeModal} className="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg">
                                <XMarkIcon className="w-5 h-5" />
                            </button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-5">
                            <div>
                                <label className={labelClasses}>{t('page.beds.bed_code')}</label>
                                <input type="text" value={data.code} onChange={e => setData('code', e.target.value)} className={inputClasses} placeholder="e.g. BED-101" />
                                {errors.code && <p className="text-red-500 text-xs mt-1">{errors.code}</p>}
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClasses}>{t('page.beds.assign_ward')}</label>
                                    <select value={data.ward_id} onChange={e => setData('ward_id', e.target.value)} className={inputClasses}>
                                        <option value="">{t('page.beds.select_ward')}</option>
                                        {wards.map(w => <option key={w.id} value={w.id}>{w.name}</option>)}
                                    </select>
                                    {errors.ward_id && <p className="text-red-500 text-xs mt-1">{errors.ward_id}</p>}
                                </div>
                                <div>
                                    <label className={labelClasses}>{t('page.beds.bed_type')}</label>
                                    <select value={data.bed_type_id} onChange={e => setData('bed_type_id', e.target.value)} className={inputClasses}>
                                        <option value="">{t('page.beds.select_type')}</option>
                                        {bedTypes.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
                                    </select>
                                    {errors.bed_type_id && <p className="text-red-500 text-xs mt-1">{errors.bed_type_id}</p>}
                                </div>
                            </div>
                            <div>
                                <label className={labelClasses}>{t('page.beds.status')}</label>
                                <select value={data.status} onChange={e => setData('status', e.target.value)} className={inputClasses}>
                                    <option value="Available">{t('page.beds.available')}</option>
                                    <option value="Occupied">{t('page.beds.occupied')}</option>
                                    <option value="Maintenance">{t('page.beds.maintenance')}</option>
                                </select>
                                {errors.status && <p className="text-red-500 text-xs mt-1">{errors.status}</p>}
                            </div>
                            <div className="flex justify-end gap-3 pt-4 border-t">
                                <button type="button" onClick={closeModal} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    {t('common.cancel')}
                                </button>
                                <button type="submit" disabled={processing} className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-75">
                                    {processing ? t('page.beds.saving') : t('common.save_changes')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Transfer Modal */}
            {transferModalOpen && selectedBed && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onClick={closeTransferModal}></div>
                    <div className="relative bg-white rounded-xl w-full max-w-md shadow-xl border border-gray-200 overflow-hidden">
                        <div className="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 className="text-lg font-semibold text-gray-900">{t('page.beds.transfer_bed')}</h3>
                            <button onClick={closeTransferModal} className="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg">
                                <XMarkIcon className="w-5 h-5" />
                            </button>
                        </div>
                        <form onSubmit={handleTransfer} className="p-6 space-y-5">
                            <div>
                                <label className={labelClasses}>{t('page.beds.bed_label')}: <span className="font-bold">{selectedBed.code}</span></label>
                                <p className="text-sm text-gray-500 mb-3">{t('page.beds.current_ward')}: {selectedBed.ward?.name || t('page.beds.none')}</p>
                            </div>
                            <div>
                                <label className={labelClasses}>{t('page.beds.transfer_to_ward')}</label>
                                <select
                                    value={transferData.ward_id}
                                    onChange={e => setTransferData('ward_id', e.target.value)}
                                    className={inputClasses}
                                    required
                                >
                                    <option value="">{t('page.beds.select_new_ward')}</option>
                                    {wards.map(w => (
                                        <option key={w.id} value={w.id} disabled={w.id === selectedBed.ward_id}>
                                            {w.name} {w.id === selectedBed.ward_id ? t('page.beds.current') : ''}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="flex justify-end gap-3 pt-4 border-t">
                                <button type="button" onClick={closeTransferModal} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    {t('common.cancel')}
                                </button>
                                <button type="submit" disabled={transferProcessing} className="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-75">
                                    {transferProcessing ? t('page.beds.transferring') : t('page.beds.confirm_transfer')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}