import { PlusIcon, PencilSquareIcon, TrashIcon, HomeIcon, ChevronRightIcon, XMarkIcon, TagIcon, AdjustmentsHorizontalIcon, MagnifyingGlassIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Props {
    bedTypes: any[];
}

export default function BedTypeIndex({ bedTypes }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [editingType, setEditingType] = useState<any>(null);

    const { data, setData, post, put, delete: destroy, processing, errors, reset, clearErrors } = useForm({
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
            put(route('bed-types.update', editingType.id), { onSuccess: closeModal });
        } else {
            post(route('bed-types.store'), { onSuccess: closeModal });
        }
    };

    const handleDelete = (type: any) => {
        if (confirm(t('page.bed_type.delete_confirm', { name: type.name }))) {
            destroy(route('bed-types.destroy', type.id));
        }
    };

    const filteredTypes = bedTypes.filter(t =>
        t.name?.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const inputClasses = "mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm";
    const labelClasses = "block text-xs font-bold text-gray-500 uppercase tracking-wide";

    return (
        <AppLayout headerTitle={t('page.bed_type.title')} headerSubtitle={t('page.bed_type.subtitle')}>
            <Head title={t('page.bed_type.head_title')} />
            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 py-6">
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-medium">{t('page.bed_type.head_title')}</li>
                    </ol>
                </nav>

                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h2 className="text-2xl font-bold text-gray-900">{t('page.bed_type.heading')}</h2>
                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                type="text"
                                placeholder={t('page.bed_type.search_placeholder')}
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
                            <PlusIcon className="w-4 h-4" /> {t('page.bed_type.add_type')}
                        </button>
                    </div>
                </div>

                {filteredTypes.length === 0 ? (
                    <div className="text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-200">
                        <TagIcon className="w-12 h-12 text-gray-300 mx-auto mb-4" />
                        <h3 className="text-lg font-semibold text-gray-900">{t('page.bed_type.no_bed_types')}</h3>
                        <button onClick={openCreateModal} className="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">
                            <PlusIcon className="w-4 h-4" /> {t('page.bed_type.add_first_type')}
                        </button>
                    </div>
                ) : (
                    <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <table className="w-full text-left">
                            <thead className="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th className="px-6 py-4">{t('page.bed_type.col_name')}</th>
                                    <th className="px-6 py-4">{t('page.bed_type.col_capacity')}</th>
                                    <th className="px-6 py-4 text-right">{t('common.actions')}</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 text-sm">
                                {filteredTypes.map((type) => (
                                    <tr key={type.id} className="hover:bg-gray-50 transition-colors group">
                                        <td className="px-6 py-4 font-semibold text-gray-900">{type.name}</td>
                                        <td className="px-6 py-4">
                                            <span className="bg-gray-100 text-gray-700 px-2.5 py-1 rounded-md text-xs font-semibold">
                                                {type.capacity} {t('page.bed_type.patients')}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <button onClick={() => openEditModal(type)} className="p-2 text-gray-400 hover:text-blue-600">
                                                <PencilSquareIcon className="w-4.5 h-4.5" />
                                            </button>
                                            <button onClick={() => handleDelete(type)} className="p-2 text-gray-400 hover:text-red-600">
                                                <TrashIcon className="w-4.5 h-4.5" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            {/* Modal */}
            {modalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onClick={closeModal}></div>
                    <div className="relative bg-white rounded-xl w-full max-w-lg shadow-xl border border-gray-200 overflow-hidden">
                        <div className="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 className="text-lg font-semibold text-gray-900">{editingType ? t('page.bed_type.edit_type') : t('page.bed_type.add_type')}</h3>
                            <button onClick={closeModal} className="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg">
                                <XMarkIcon className="w-5 h-5" />
                            </button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-5">
                            <div>
                                <label className={labelClasses}>{t('page.bed_type.bed_type_name')}</label>
                                <input type="text" value={data.name} onChange={e => setData('name', e.target.value)} className={inputClasses} placeholder="e.g. ICU, General, Private" />
                                {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                            </div>
                            <div>
                                <label className={labelClasses}>{t('page.bed_type.patient_capacity')}</label>
                                <input type="number" value={data.capacity} onChange={e => setData('capacity', e.target.value)} className={inputClasses} min="1" />
                                {errors.capacity && <p className="text-red-500 text-xs mt-1">{errors.capacity}</p>}
                            </div>
                            <div className="flex justify-end gap-3 pt-4 border-t">
                                <button type="button" onClick={closeModal} className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    {t('common.cancel')}
                                </button>
                                <button type="submit" disabled={processing} className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-75">
                                    {processing ? t('page.bed_type.saving') : t('common.save_changes')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}