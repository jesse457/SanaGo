import {
    HomeIcon, ChevronRightIcon,
    ClipboardDocumentListIcon, BeakerIcon,
    ScaleIcon, CheckIcon,
    ArrowPathIcon, XMarkIcon,
    ExclamationTriangleIcon, CheckCircleIcon,
    ExclamationCircleIcon,
    CurrencyDollarIcon,
    Bars3BottomLeftIcon,
    ArrowLeftIcon,
    FingerprintIcon,
    HashtagIcon,
    PowerIcon
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface LabTestForm {
    test_name: string;
    code: string;
    price: string;
    units: string;
    description: string;
    is_active: boolean;
}

export default function CreateLabTest() {
    const { t } = useTranslation();
    const [formData, setFormData] = useState<LabTestForm>({
        test_name: '',
        code: '',
        price: '',
        units: '',
        description: '',
        is_active: true
    });

    const [formErrors, setFormErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);

    const handleInputChange = (field: keyof LabTestForm, value: any) => {
        setFormData(prev => ({ ...prev, [field]: value }));
    };

    const handleSubmit = async (e: FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);
        // Simulation
        await new Promise(resolve => setTimeout(resolve, 1000));
        setSuccessMessage(t('lab_tests.create.success_message'));
        setIsSubmitting(false);
    };

    // Style helper for higher inputs
    const inputStyle = (error: string | undefined) => `
        w-full pl-12 pr-4 py-4 bg-white dark:bg-neutral-800 border-2 rounded-lg
        font-bold text-sm transition-all outline-none
        ${error ? 'border-red-500 focus:border-red-600' : 'border-gray-200 dark:border-neutral-700 focus:border-blue-600 focus:ring-0'}
    `;

    return (
        <AppLayout
            headerTitle={t('lab_tests.create.title')}
            headerSubtitle={t('lab_tests.create.subtitle')}
        >
            <Head title={t('lab_tests.create.head_title')} />

            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 min-h-[80vh]">
                {/* Breadcrumbs */}
                <nav className="flex text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer transition-colors">
                            <HomeIcon className="w-3.5 h-3.5 mr-1" /> {t('common.home')}
                        </li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="hover:text-blue-600">{t('lab_tests.create.lab_definitions')}</li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="text-blue-600">{t('lab_tests.create.new_configuration')}</li>
                    </ol>
                </nav>

                {/* Alerts */}
                {successMessage && (
                    <div className="p-5 rounded-lg bg-emerald-50 border-2 border-emerald-100 flex justify-between items-center shadow-sm">
                        <div className="flex items-center gap-3">
                            <CheckCircleIcon className="w-6 h-6 text-emerald-600" />
                            <p className="text-sm font-black text-emerald-900 uppercase tracking-tight">{successMessage}</p>
                        </div>
                        <button onClick={() => setSuccessMessage(null)}><XMarkIcon className="w-5 h-5 text-emerald-400" /></button>
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-8 pb-20">
                    <div className="bg-white dark:bg-neutral-800 rounded-lg border-2 border-gray-100 dark:border-neutral-700 shadow-md overflow-hidden">
                        {/* Section Header - More Height */}
                        <div className="p-8 border-b-2 border-gray-50 bg-gray-50/30 dark:bg-neutral-700/30 dark:bg-neutral-700/30 flex items-center gap-5">
                            <div className="h-14 w-14 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-xl shadow-blue-100 shrink-0">
                                <BeakerIcon className="w-7 h-7" />
                            </div>
                            <div>
                                <h3 className="text-2xl font-black text-gray-900 dark:text-neutral-100 tracking-tight">{t('lab_tests.create.test_details')}</h3>
                                <p className="text-[10px] font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-widest">{t('lab_tests.create.general_settings')}</p>
                            </div>
                        </div>

                        {/* Main Grid - More Padding (p-10) */}
                        <div className="p-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                            {/* Test Name */}
                            <div className="lg:col-span-2 space-y-2">
                                <label className="text-[10px] font-black uppercase text-gray-400 dark:text-neutral-500 ml-1">{t('lab_tests.create.official_test_name')} <span className="text-red-500">*</span></label>
                                <div className="relative">
                                    <ClipboardDocumentListIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                    <input
                                        type="text"
                                        value={formData.test_name}
                                        onChange={(e) => handleInputChange('test_name', e.target.value)}
                                        className={inputStyle(formErrors.test_name)}
                                        placeholder={t('lab_tests.create.test_name_placeholder')}
                                    />
                                </div>
                            </div>

                            {/* Test Code */}
                            <div className="space-y-2">
                                <label className="text-[10px] font-black uppercase text-gray-400 dark:text-neutral-500 ml-1">{t('lab_tests.create.internal_code')} <span className="text-red-500">*</span></label>
                                <div className="relative">
                                    <HashtagIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                    <input
                                        type="text"
                                        value={formData.code}
                                        onChange={(e) => handleInputChange('code', e.target.value)}
                                        className={inputStyle(formErrors.code)}
                                        placeholder={t('lab_tests.create.code_placeholder')}
                                    />
                                </div>
                            </div>

                            {/* Price */}
                            <div className="space-y-2">
                                <label className="text-[10px] font-black uppercase text-gray-400 dark:text-neutral-500 ml-1">{t('lab_tests.create.unit_price')} <span className="text-red-500">*</span></label>
                                <div className="relative">
                                    <CurrencyDollarIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                    <input
                                        type="number"
                                        value={formData.price}
                                        onChange={(e) => handleInputChange('price', e.target.value)}
                                        className={inputStyle(formErrors.price)}
                                        placeholder={t('lab_tests.create.price_placeholder')}
                                    />
                                </div>
                            </div>

                            {/* Units */}
                            <div className="space-y-2">
                                <label className="text-[10px] font-black uppercase text-gray-400 dark:text-neutral-500 ml-1">{t('lab_tests.create.result_units')} <span className="text-red-500">*</span></label>
                                <div className="relative">
                                    <ScaleIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                    <input
                                        type="text"
                                        value={formData.units}
                                        onChange={(e) => handleInputChange('units', e.target.value)}
                                        className={inputStyle(formErrors.units)}
                                        placeholder={t('lab_tests.create.units_placeholder')}
                                    />
                                </div>
                            </div>

                            {/* Is Active Status */}
                            <div className="space-y-2">
                                <label className="text-[10px] font-black uppercase text-gray-400 dark:text-neutral-500 ml-1">{t('lab_tests.create.availability_status')}</label>
                                <div className="relative">
                                    <PowerIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                    <select
                                        value={formData.is_active ? 'true' : 'false'}
                                        onChange={(e) => handleInputChange('is_active', e.target.value === 'true')}
                                        className={inputStyle(undefined)}
                                    >
                                        <option value="true">{t('lab_tests.create.active')}</option>
                                        <option value="false">{t('lab_tests.create.inactive')}</option>
                                    </select>
                                </div>
                            </div>

                            {/* Description - Full Width */}
                            <div className="lg:col-span-3 space-y-2">
                                <label className="text-[10px] font-black uppercase text-gray-400 dark:text-neutral-500 ml-1">{t('lab_tests.create.test_description')}</label>
                                <div className="relative">
                                    <Bars3BottomLeftIcon className="absolute left-4 top-5 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                    <textarea
                                        rows={4}
                                        value={formData.description}
                                        onChange={(e) => handleInputChange('description', e.target.value)}
                                        className="w-full pl-12 pr-4 py-4 bg-white dark:bg-neutral-800 border-2 border-gray-200 dark:border-neutral-700 rounded-lg font-bold text-sm outline-none focus:border-blue-600 resize-none transition-all"
                                        placeholder={t('lab_tests.create.description_placeholder')}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Action Bar */}
                    <div className="flex flex-col sm:flex-row items-center justify-end gap-6">
                        <Link
                            href="/lab-technician/manage-lab-definitions"
                            className="w-full sm:w-auto px-10 py-5 bg-white dark:bg-neutral-800 border-2 border-gray-200 dark:border-neutral-700 rounded-lg font-black text-gray-500 dark:text-neutral-500 hover:bg-gray-50 dark:hover:bg-neutral-700 transition-all flex items-center justify-center gap-2"
                        >
                            <ArrowLeftIcon className="w-5 h-5" /> {t('common.cancel')}
                        </Link>

                        <button
                            type="submit"
                            disabled={isSubmitting}
                            className="w-full sm:w-auto px-12 py-5 bg-gray-900 dark:bg-neutral-800 text-white rounded-lg font-black hover:bg-black transition-all shadow-2xl flex items-center justify-center gap-2 disabled:opacity-50"
                        >
                            {isSubmitting ? (
                                <ArrowPathIcon className="animate-spin h-6 w-6" />
                            ) : (
                                <CheckIcon className="w-6 h-6" />
                            )}
                            {t('lab_tests.create.publish')}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
