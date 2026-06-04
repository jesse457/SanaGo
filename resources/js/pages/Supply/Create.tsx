import {
    ArrowLeftIcon,
    CubeIcon,
    TagIcon,
    ScaleIcon,
    CurrencyDollarIcon,
    InboxStackIcon,
    BellAlertIcon,
    CheckIcon,
    ArrowPathIcon,
    HomeIcon,
    ChevronRightIcon,
    ArchiveBoxIcon,
    Squares2X2Icon,
    PhotoIcon,
    XMarkIcon
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import React, { FormEvent, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Supply {
    id: number;
    name: string;
    unit_of_measure: string;
    current_stock: number;
    min_stock_level: number;
    cost_per_unit: number;
    category: string;
}

export default function SupplyForm({ supply }: { supply?: Supply }) {
    const { t } = useTranslation();
    const isEdit = !!supply;

    const { data, setData, post, put, processing, errors } = useForm({
        name: supply?.name || '',
        category: supply?.category || 'General',
        unit_of_measure: supply?.unit_of_measure || '',
        cost_per_unit: supply?.cost_per_unit || '',
        current_stock: supply?.current_stock ?? '',
        min_stock_level: supply?.min_stock_level ?? 10,
        image: null as File | null,
    });

    const [imagePreview, setImagePreview] = useState<string | null>(null);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        if (isEdit) {
            put(route('supplies.update', supply.id), { forceFormData: true });
        } else {
            post(route('supplies.store'), { forceFormData: true });
        }
    };

    const inputStyle = (error: string | undefined) => `
        w-full pl-12 pr-4 py-3 bg-white dark:bg-neutral-800 border rounded-lg
        font-bold text-sm transition-all outline-none
        ${error ? 'border-red-500 focus:ring-2 focus:ring-red-500' : 'border-gray-200 dark:border-neutral-700 focus:ring-2 focus:ring-blue-500'}
        dark:text-neutral-100 dark:placeholder:text-neutral-500
    `;

    const selectStyle = (error: string | undefined) => `
        w-full px-4 py-3 bg-white dark:bg-neutral-800 border rounded-lg
        font-bold text-sm transition-all outline-none appearance-none
        ${error ? 'border-red-500 focus:ring-2 focus:ring-red-500' : 'border-gray-200 dark:border-neutral-700 focus:ring-2 focus:ring-blue-500'}
        dark:text-neutral-100
    `;

    return (
        <AppLayout
            headerTitle={isEdit ? t('page.supply_create.update_inventory') : t('page.supply_create.stock_registration')}
            headerSubtitle={t('page.supply_create.subtitle')}
        >
            <Head title={isEdit ? t('page.supply_create.edit_supply') : t('page.supply_create.add_supply')} />

            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-20">

                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer transition-colors">
                            <HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}
                        </li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="hover:text-blue-600">{t('page.supply_create.inventory')}</li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="text-blue-600 font-medium">{isEdit ? t('page.supply_create.modify_item') : t('page.supply_create.new_supply')}</li>
                    </ol>
                </nav>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">

                        {/* Section 1: Item Identification */}
                        <div className="p-8 border-b border-gray-100 dark:border-neutral-700 bg-gray-50/50 dark:bg-neutral-800/50 flex items-center gap-5">
                            <div className="h-14 w-14 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-sm shrink-0">
                                <ArchiveBoxIcon className="w-7 h-7" />
                            </div>
                            <div>
                                <h3 className="text-2xl font-bold text-gray-900 dark:text-neutral-100 tracking-tight">{t('page.supply_create.supply_details')}</h3>
                                <p className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('page.supply_create.identification')}</p>
                            </div>
                        </div>

                        <div className="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {/* Supply Name */}
                            <div className="lg:col-span-2 space-y-2">
                                <label className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide ml-1">{t('page.supply_create.supply_name')} <span className="text-red-500">*</span></label>
                                <div className="relative">
                                    <TagIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <input
                                        type="text"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        className={inputStyle(errors.name)}
                                        placeholder="e.g. Sterile Surgical Gloves (Size 7.5)"
                                    />
                                </div>
                                {errors.name && <p className="text-xs font-bold text-red-500 mt-1 ml-1">{errors.name}</p>}
                            </div>

                            {/* Category */}
                            <div className="space-y-2">
                                <label className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide ml-1">{t('page.supply_create.stock_category')}</label>
                                <div className="relative">
                                    <Squares2X2Icon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                    <select
                                        value={data.category}
                                        onChange={(e) => setData('category', e.target.value)}
                                        className={`${selectStyle(errors.category)} pl-12`}
                                    >
                                        <option value="General">{t('page.supply_create.category_general')}</option>
                                        <option value="PPE">{t('page.supply_create.category_ppe')}</option>
                                        <option value="Pharmacy">{t('page.supply_create.category_pharmacy')}</option>
                                        <option value="IV Fluids">{t('page.supply_create.category_iv_fluids')}</option>
                                        <option value="Wound Care">{t('page.supply_create.category_wound_care')}</option>
                                        <option value="Consumables">{t('page.supply_create.category_consumables')}</option>
                                    </select>
                                </div>
                            </div>

                            {/* Unit of Measure */}
                            <div className="space-y-2">
                                <label className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide ml-1">{t('page.supply_create.unit_of_measure')}</label>
                                <div className="relative">
                                    <ScaleIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <input
                                        type="text"
                                        value={data.unit_of_measure}
                                        onChange={(e) => setData('unit_of_measure', e.target.value)}
                                        className={inputStyle(errors.unit_of_measure)}
                                        placeholder="e.g. Box of 50, Unit, Liter"
                                    />
                                </div>
                            </div>

                            {/* Cost per Unit */}
                            <div className="space-y-2">
                                <label className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide ml-1">{t('page.supply_create.cost_per_unit')}</label>
                                <div className="relative">
                                    <CurrencyDollarIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <input
                                        type="number"
                                        step="0.01"
                                        value={data.cost_per_unit}
                                        onChange={(e) => setData('cost_per_unit', e.target.value)}
                                        className={inputStyle(errors.cost_per_unit)}
                                        placeholder="0.00"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Section 2: Inventory Management */}
                        <div className="p-8 border-t border-gray-100 dark:border-neutral-700 bg-gray-50/30 dark:bg-neutral-800/30 flex items-center gap-5">
                            <div className="h-10 w-10 rounded-lg bg-blue-500 flex items-center justify-center text-white shadow-sm shrink-0">
                                <InboxStackIcon className="w-5 h-5" />
                            </div>
                            <div>
                                <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100 tracking-tight">{t('page.supply_create.stock_thresholds')}</h3>
                                <p className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('page.supply_create.inventory_levels')}</p>
                            </div>
                        </div>

                        <div className="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <label className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide ml-1">
                                    {isEdit ? t('page.supply_create.current_stock_level') : t('page.supply_create.initial_stock_quantity')}
                                </label>
                                <div className="relative">
                                    <InboxStackIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <input
                                        type="number"
                                        value={data.current_stock}
                                        onChange={(e) => setData('current_stock', e.target.value)}
                                        className={inputStyle(errors.current_stock)}
                                        placeholder="0"
                                    />
                                </div>
                            </div>

                            <div className="space-y-2">
                                <label className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide ml-1">{t('page.supply_create.low_stock_threshold')}</label>
                                <div className="relative">
                                    <BellAlertIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <input
                                        type="number"
                                        value={data.min_stock_level}
                                        onChange={(e) => setData('min_stock_level', e.target.value)}
                                        className={inputStyle(errors.min_stock_level)}
                                        placeholder="10"
                                    />
                                </div>
                                <p className="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-tight ml-1">
                                    {t('page.supply_create.alert_trigger_note')}
                                </p>
                            </div>
                        </div>

                        {/* Section 3: Supply Image */}
                        <div className="p-8 border-t border-gray-100 dark:border-neutral-700 bg-gray-50/30 dark:bg-neutral-800/30">
                            <div className="flex items-center gap-5 mb-6">
                                <div className="h-10 w-10 rounded-lg bg-blue-500 flex items-center justify-center text-white shadow-sm shrink-0">
                                    <PhotoIcon className="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100 tracking-tight">{t('page.supply_create.item_image')}</h3>
                                    <p className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('page.supply_create.optional_photo')}</p>
                                </div>
                            </div>
                            <div className="flex items-center gap-6">
                                {imagePreview ? (
                                    <div className="relative">
                                        <img src={imagePreview} alt="Preview" className="h-24 w-24 rounded-xl object-cover shadow-lg" />
                                        <button
                                            type="button"
                                            onClick={() => { setImagePreview(null); setData('image', null); }}
                                            className="absolute -top-2 -right-2 rounded-full bg-white dark:bg-neutral-700 p-1 text-red-500 shadow-md border border-gray-100 dark:border-neutral-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                                        >
                                            <XMarkIcon className="h-4 w-4" />
                                        </button>
                                    </div>
                                ) : (
                                    <div className="h-24 w-24 rounded-xl bg-gray-100 dark:bg-neutral-700 border-2 border-dashed border-gray-300 dark:border-neutral-600 flex items-center justify-center">
                                        <CubeIcon className="h-8 w-8 text-gray-400" />
                                    </div>
                                )}
                                <label className="cursor-pointer inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                    <span>{t('page.supply_create.upload_image')}</span>
                                    <input
                                        type="file"
                                        className="sr-only"
                                        accept="image/*"
                                        onChange={(e) => {
                                            const file = e.target.files?.[0];
                                            if (file) {
                                                setData('image', file);
                                                setImagePreview(URL.createObjectURL(file));
                                            }
                                        }}
                                    />
                                </label>
                                <p className="text-xs text-gray-400 dark:text-neutral-500">{t('page.supply_create.max_upload_size')}</p>
                            </div>
                        </div>
                    </div>

                    {/* Footer Actions */}
                    <div className="flex flex-col sm:flex-row items-center justify-end gap-4">
                        <Link
                            href={route('supplies.index')}
                            className="w-full sm:w-auto px-8 py-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl font-bold text-gray-500 dark:text-neutral-300 hover:bg-gray-50 dark:hover:bg-neutral-700 transition-all flex items-center justify-center gap-2"
                        >
                            <ArrowLeftIcon className="w-5 h-5" /> {t('common.cancel')}
                        </Link>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full sm:w-auto px-10 py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-sm flex items-center justify-center gap-2 disabled:opacity-50"
                        >
                            {processing ? (
                                <ArrowPathIcon className="animate-spin h-5 w-5" />
                            ) : (
                                <CheckIcon className="w-5 h-5" />
                            )}
                            {isEdit ? t('page.supply_create.update_inventory') : t('page.supply_create.register_supply')}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
