import {
    ClipboardDocumentListIcon,
    BeakerIcon,
    ScaleIcon,
    CheckIcon,
    ArrowPathIcon,
    XMarkIcon,
    CheckCircleIcon,
    CurrencyDollarIcon,
    Bars3BottomLeftIcon,
    ArrowLeftIcon,
    HashtagIcon,
    PowerIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useState } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

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
        is_active: true,
    });

    const [formErrors, setFormErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);

    const handleInputChange = (field: keyof LabTestForm, value: any) => {
        setFormData((prev) => ({ ...prev, [field]: value }));
    };

    const handleSubmit = async (e: FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);
        // Simulation
        await new Promise((resolve) => setTimeout(resolve, 1000));
        setSuccessMessage(t('lab_tests.create.success_message'));
        setIsSubmitting(false);
    };

    return (
        <AppLayout
            headerTitle={t('lab_tests.create.title')}
            headerSubtitle={t('lab_tests.create.subtitle')}
        >
            <Head title={t('lab_tests.create.head_title')} />

            <div className="mx-auto min-h-[80vh] max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: route('dashboard') },
                        {
                            label: t('lab_tests.create.lab_definitions'),
                            href: route('tests.index'),
                        },
                        { label: t('lab_tests.create.new_configuration') },
                    ]}
                />

                {/* Alerts */}
                {successMessage && (
                    <div className="flex items-center justify-between rounded-lg border-2 border-emerald-100 bg-emerald-50 p-5 shadow-sm dark:border-emerald-800 dark:bg-emerald-900/20">
                        <div className="flex items-center gap-3">
                            <CheckCircleIcon className="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                            <p className="text-sm font-black tracking-tight text-emerald-900 uppercase dark:text-emerald-300">
                                {successMessage}
                            </p>
                        </div>
                        <button onClick={() => setSuccessMessage(null)}>
                            <XMarkIcon className="h-5 w-5 text-emerald-400" />
                        </button>
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-8 pb-20">
                    <PageSection
                        icon={BeakerIcon}
                        title={t('lab_tests.create.test_details')}
                        description={t('lab_tests.create.general_settings')}
                    >
                        <div className="grid grid-cols-1 gap-8 p-10 md:grid-cols-2 lg:grid-cols-3">
                            {/* Test Name */}
                            <FormInput
                                name="test_name"
                                label={t('lab_tests.create.official_test_name')}
                                icon={ClipboardDocumentListIcon}
                                value={formData.test_name}
                                onChange={(v) =>
                                    handleInputChange('test_name', v)
                                }
                                required
                                placeholder={t(
                                    'lab_tests.create.test_name_placeholder',
                                )}
                                errors={formErrors}
                                className="lg:col-span-2"
                            />

                            {/* Test Code */}
                            <FormInput
                                name="code"
                                label={t('lab_tests.create.internal_code')}
                                icon={HashtagIcon}
                                value={formData.code}
                                onChange={(v) => handleInputChange('code', v)}
                                required
                                placeholder={t(
                                    'lab_tests.create.code_placeholder',
                                )}
                                errors={formErrors}
                            />

                            {/* Price */}
                            <FormInput
                                name="price"
                                label={t('lab_tests.create.unit_price')}
                                icon={CurrencyDollarIcon}
                                type="number"
                                value={formData.price}
                                onChange={(v) => handleInputChange('price', v)}
                                required
                                placeholder={t(
                                    'lab_tests.create.price_placeholder',
                                )}
                                errors={formErrors}
                            />

                            {/* Units */}
                            <FormInput
                                name="units"
                                label={t('lab_tests.create.result_units')}
                                icon={ScaleIcon}
                                value={formData.units}
                                onChange={(v) => handleInputChange('units', v)}
                                required
                                placeholder={t(
                                    'lab_tests.create.units_placeholder',
                                )}
                                errors={formErrors}
                            />

                            {/* Is Active Status */}
                            <FormSelect
                                name="is_active"
                                label={t(
                                    'lab_tests.create.availability_status',
                                )}
                                icon={PowerIcon}
                                value={formData.is_active ? 'true' : 'false'}
                                onChange={(v) =>
                                    handleInputChange('is_active', v === 'true')
                                }
                                options={[
                                    {
                                        value: 'true',
                                        label: t('lab_tests.create.active'),
                                    },
                                    {
                                        value: 'false',
                                        label: t('lab_tests.create.inactive'),
                                    },
                                ]}
                            />

                            {/* Description - Full Width */}
                            <FormTextarea
                                name="description"
                                label={t('lab_tests.create.test_description')}
                                icon={Bars3BottomLeftIcon}
                                value={formData.description}
                                onChange={(v) =>
                                    handleInputChange('description', v)
                                }
                                rows={4}
                                placeholder={t(
                                    'lab_tests.create.description_placeholder',
                                )}
                                className="lg:col-span-3"
                            />
                        </div>
                    </PageSection>

                    {/* Action Bar */}
                    <div className="flex flex-col items-center justify-end gap-6 sm:flex-row">
                        <Link
                            href="/lab-technician/manage-lab-definitions"
                            className="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 px-8 py-4 font-bold text-gray-500 transition-all hover:bg-gray-50 sm:w-auto dark:border-neutral-600 dark:text-neutral-400 dark:hover:bg-neutral-700"
                        >
                            <ArrowLeftIcon className="h-5 w-5" />{' '}
                            {t('common.cancel')}
                        </Link>

                        <button
                            type="submit"
                            disabled={isSubmitting}
                            className="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-10 py-4 font-bold text-white shadow-lg transition-all hover:bg-blue-700 disabled:opacity-50 sm:w-auto"
                        >
                            {isSubmitting ? (
                                <ArrowPathIcon className="h-6 w-6 animate-spin" />
                            ) : (
                                <CheckIcon className="h-6 w-6" />
                            )}
                            {t('lab_tests.create.publish')}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
