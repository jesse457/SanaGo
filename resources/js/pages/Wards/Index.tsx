import { PlusIcon, PencilSquareIcon, TrashIcon, HomeIcon, ChevronRightIcon, XMarkIcon, MagnifyingGlassIcon, BuildingOfficeIcon, ExclamationTriangleIcon, AdjustmentsHorizontalIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Props {
    wards: any[];
}

export default function WardIndex({ wards }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [editingWard, setEditingWard] = useState<any>(null);

    const { data, setData, post, put, delete: destroy, processing, errors, reset, clearErrors } = useForm({
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

    const filteredWards = wards.filter(w =>
        w.name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
        w.code?.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const inputClasses = "mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm";
    const labelClasses = "block text-xs font-bold text-gray-500 uppercase tracking-wide";

    return (
        <AppLayout headerTitle={t('page.wards.title')} headerSubtitle={t('page.wards.subtitle')}>
            <Head title={t('page.wards.head_title')} />
            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 py-6">
                {/* Breadcrumb */}
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-medium">{t('page.wards.head_title')}</li>
                    </ol>
                </nav>

                {/* Header with search & add button */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h2 className="text-2xl font-bold text-gray-900">{t('page.wards.heading')}</h2>
                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                type="text"
                                placeholder={t('page.wards.search_placeholder')}
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="pl-9 pr-4 py-2 w-48 sm:w-64 bg-white border border-gray-200 shadow-sm rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                        </div>
                        <button className="p-2 border border-gray-200 bg-white rounded-lg text-gray-500 hover:text-gray-900 shadow-sm">
                            <AdjustmentsHorizontalIcon className="w-5 h-5" />
                        </button>
                        <button
                            onClick={openCreateModal}
                            className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-sm"
                        >
                            <PlusIcon className="w-4 h-4" /> {t('page.wards.add_ward')}
                        </button>
                    </div>
                </div>

                {/* Ward list (reuse your WardList component) */}
                {filteredWards.length === 0 ? (
                    <div className="text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-200">
                        <BuildingOfficeIcon className="w-12 h-12 text-gray-300 mx-auto mb-4" />
                        <h3 className="text-lg font-semibold text-gray-900">{t('page.wards.no_wards')}</h3>
                        <p className="text-gray-500 text-sm mt-1 mb-4">{t('page.wards.get_started')}</p>
                        <button onClick={openCreateModal} className="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">
                            <PlusIcon className="w-4 h-4" /> {t('page.wards.add_first_ward')}
                        </button>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {filteredWards.map((ward) => (
                            <div key={ward.id} className="bg-white p-5 rounded-xl border border-gray-200 shadow-sm group hover:shadow-md transition-shadow">
                                <div className="flex justify-between items-start">
                                    <div>
                                        <span className="text-[10px] font-bold tracking-wider text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md uppercase">{ward.code}</span>
                                        <h4 className="text-base font-bold text-gray-900 mt-3">{ward.name}</h4>
                                        {ward.floor && <p className="text-xs text-gray-500 mt-1">{ward.floor}</p>}
                                    </div>
                                    <div className="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onClick={() => openEditModal(ward)} className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md">
                                            <PencilSquareIcon className="w-4 h-4" />
                                        </button>
                                        <button onClick={() => handleDelete(ward)} className="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md">
                                            <TrashIcon className="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                                <div className="mt-5 flex justify-between text-xs font-bold text-gray-500 uppercase bg-gray-50 p-3 rounded-lg">
                                    <span>{t('page.wards.current_capacity')}</span>
                                    <span className="text-blue-600">{ward.beds_count || 0} / {ward.capacity || 0} {t('page.wards.beds')}</span>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Modal (same style as original) */}
            {modalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onClick={closeModal}></div>
                    <div className="relative bg-white rounded-lg w-full max-w-lg shadow-xl border border-gray-200 overflow-hidden">
                        <div className="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 className="text-lg font-semibold text-gray-900 capitalize">{editingWard ? t('page.wards.edit_ward') : t('page.wards.add_ward')}</h3>
                            <button onClick={closeModal} className="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg">
                                <XMarkIcon className="w-5 h-5" />
                            </button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-5">
                            <div className="grid grid-cols-2 gap-4">
                                <div className="col-span-2">
                                    <label className={labelClasses}>{t('page.wards.ward_name')}</label>
                                    <input type="text" value={data.name} onChange={e => setData('name', e.target.value)} className={inputClasses} placeholder="e.g. Maternity Ward" />
                                    {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                                </div>
                                <div>
                                    <label className={labelClasses}>{t('page.wards.ward_code')}</label>
                                    <input type="text" value={data.code} onChange={e => setData('code', e.target.value)} className={inputClasses} placeholder="e.g. W-MAT-01" />
                                    {errors.code && <p className="text-red-500 text-xs mt-1">{errors.code}</p>}
                                </div>
                                <div>
                                    <label className={labelClasses}>{t('page.wards.capacity_beds')}</label>
                                    <input type="number" value={data.capacity} onChange={e => setData('capacity', e.target.value)} className={inputClasses} min="1" />
                                    {errors.capacity && <p className="text-red-500 text-xs mt-1">{errors.capacity}</p>}
                                </div>
                                <div className="col-span-2">
                                    <label className={labelClasses}>{t('page.wards.floor_location')}</label>
                                    <input type="text" value={data.floor} onChange={e => setData('floor', e.target.value)} className={inputClasses} placeholder="e.g. 2nd Floor, West Wing" />
                                    {errors.floor && <p className="text-red-500 text-xs mt-1">{errors.floor}</p>}
                                </div>
                                <div className="col-span-2">
                                    <label className={labelClasses}>{t('common.description')}</label>
                                    <textarea value={data.description} onChange={e => setData('description', e.target.value)} rows={3} className={inputClasses} placeholder="Brief description..." />
                                    {errors.description && <p className="text-red-500 text-xs mt-1">{errors.description}</p>}
                                </div>
                            </div>
                            <div className="flex justify-end gap-3 pt-4 border-t">
                                <button type="button" onClick={closeModal} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    {t('common.cancel')}
                                </button>
                                <button type="submit" disabled={processing} className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-75">
                                    {processing ? t('page.wards.saving') : t('common.save_changes')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}