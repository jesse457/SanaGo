import {
    UserPlusIcon,
    UserIcon,
    EnvelopeIcon,
    PhoneIcon,
    MapPinIcon,
    CalendarIcon,
    ArrowLeftIcon,
    CheckCircleIcon,
    ArrowPathIcon,
    BeakerIcon,
    ExclamationTriangleIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, Link } from '@inertiajs/react';
import React from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

export default function CreatePatient() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        date_of_birth: '',
        gender: '',
        address: '',
        blood_type: '',
        allergies: '',
        emergency_contact_name: '',
        emergency_contact_phone: '',
        photo: null as File | null,
    });

    const [imagePreview, setImagePreview] = React.useState<string | null>(null);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('patients.store'), { forceFormData: true });
    };

    return (
        <AppLayout
            headerTitle={t('page.patients_create.title')}
            headerSubtitle={t('page.patients_create.subtitle')}
        >
            <Head title={t('page.patients_create.head_title')} />

            <div className="mx-auto max-w-6xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        {
                            label: t('page.patients_create.patient_management'),
                            href: route('patients.index'),
                        },
                        { label: t('page.patients_create.new_registration') },
                    ]}
                />

                <form onSubmit={submit} className="space-y-6">
                    {/* Main Card */}
                    <PageSection
                        icon={UserPlusIcon}
                        title={t('page.patients_create.basic_information')}
                        description={t('page.patients_create.identity_details')}
                    >
                        <div className="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2 lg:grid-cols-3">
                            <FormInput
                                name="first_name"
                                label={t('page.patients_create.first_name')}
                                icon={UserIcon}
                                value={data.first_name}
                                onChange={(v) => setData('first_name', v)}
                                placeholder={t(
                                    'page.patients_create.first_name_placeholder',
                                )}
                                errors={errors}
                                required
                            />

                            <FormInput
                                name="last_name"
                                label={t('page.patients_create.last_name')}
                                icon={UserIcon}
                                value={data.last_name}
                                onChange={(v) => setData('last_name', v)}
                                placeholder={t(
                                    'page.patients_create.last_name_placeholder',
                                )}
                                errors={errors}
                                required
                            />

                            <FormInput
                                name="date_of_birth"
                                label={t('page.patients_create.date_of_birth')}
                                icon={CalendarIcon}
                                type="date"
                                value={data.date_of_birth}
                                onChange={(v) => setData('date_of_birth', v)}
                                errors={errors}
                                required
                            />

                            <FormSelect
                                name="gender"
                                label={t('page.patients_create.gender')}
                                value={data.gender}
                                onChange={(v) => setData('gender', v)}
                                errors={errors}
                                required
                                placeholder={t(
                                    'page.patients_create.select_gender',
                                )}
                                options={[
                                    { value: 'male', label: t('common.male') },
                                    {
                                        value: 'female',
                                        label: t('common.female'),
                                    },
                                    {
                                        value: 'other',
                                        label: t('common.other'),
                                    },
                                ]}
                            />

                            <FormSelect
                                name="blood_type"
                                label={t('page.patients_create.blood_type')}
                                icon={BeakerIcon}
                                value={data.blood_type}
                                onChange={(v) => setData('blood_type', v)}
                                errors={errors}
                                placeholder={t('page.patients_create.unknown')}
                                options={[
                                    { value: 'A+', label: 'A+' },
                                    { value: 'A-', label: 'A-' },
                                    { value: 'B+', label: 'B+' },
                                    { value: 'B-', label: 'B-' },
                                    { value: 'O+', label: 'O+' },
                                    { value: 'O-', label: 'O-' },
                                    { value: 'AB+', label: 'AB+' },
                                    { value: 'AB-', label: 'AB-' },
                                ]}
                            />

                            <FormInput
                                name="allergies"
                                label={t('page.patients_create.allergies')}
                                icon={ExclamationTriangleIcon}
                                value={data.allergies}
                                onChange={(v) => setData('allergies', v)}
                                placeholder={t(
                                    'page.patients_create.allergies_placeholder',
                                )}
                                errors={errors}
                            />
                        </div>

                        {/* Photo Upload */}
                        <div className="border-t border-gray-100 bg-gray-50/30 p-6 dark:border-neutral-700 dark:bg-neutral-800/30">
                            <h3 className="mb-6 flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-neutral-100">
                                <PhoneIcon className="h-5 w-5 text-blue-500" />
                                {t('page.patients_create.patient_photo')}
                            </h3>
                            <div className="flex items-center gap-6">
                                <div
                                    onDragOver={(e) => {
                                        e.preventDefault();
                                    }}
                                    onDrop={(e) => {
                                        e.preventDefault();
                                        const file = e.dataTransfer.files[0];

                                        if (
                                            file &&
                                            file.type.startsWith('image/')
                                        ) {
                                            setData('photo', file);
                                            setImagePreview(
                                                URL.createObjectURL(file),
                                            );
                                        }
                                    }}
                                    className="relative"
                                >
                                    {imagePreview ? (
                                        <div className="relative">
                                            <img
                                                src={imagePreview}
                                                alt="Preview"
                                                className="h-24 w-24 rounded-xl object-cover shadow-lg"
                                            />
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    setImagePreview(null);
                                                    setData('photo', null);
                                                }}
                                                className="absolute -top-2 -right-2 rounded-full border border-gray-100 bg-white p-1 text-red-500 shadow-md hover:bg-red-50 dark:border-neutral-600 dark:bg-neutral-700 dark:hover:bg-red-900/20"
                                            >
                                                <ExclamationTriangleIcon className="h-4 w-4" />
                                            </button>
                                        </div>
                                    ) : (
                                        <div className="flex h-24 w-24 items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-100 dark:border-neutral-600 dark:bg-neutral-700">
                                            <UserPlusIcon className="h-8 w-8 text-gray-400" />
                                        </div>
                                    )}
                                </div>
                                <label className="cursor-pointer text-sm font-bold text-blue-600 hover:text-blue-700">
                                    <span>
                                        {t('page.patients_create.upload_photo')}
                                    </span>
                                    <input
                                        type="file"
                                        className="sr-only"
                                        accept="image/*"
                                        onChange={(e) => {
                                            const file = e.target.files?.[0];

                                            if (file) {
                                                setData('photo', file);
                                                setImagePreview(
                                                    URL.createObjectURL(file),
                                                );
                                            }
                                        }}
                                    />
                                </label>
                                <p className="text-xs font-bold text-gray-400 uppercase dark:text-neutral-500">
                                    {t('page.patients_create.max_photo_size')}
                                </p>
                            </div>
                        </div>

                        {/* Contact Section */}
                        <div className="border-t border-gray-100 bg-gray-50/30 p-6 dark:border-neutral-700 dark:bg-neutral-800/30">
                            <h3 className="mb-6 flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-neutral-100">
                                <PhoneIcon className="h-5 w-5 text-blue-500" />
                                {t('page.patients_create.contact_emergency')}
                            </h3>

                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <FormInput
                                    name="phone"
                                    label={t(
                                        'page.patients_create.phone_number',
                                    )}
                                    icon={PhoneIcon}
                                    value={data.phone}
                                    onChange={(v) => setData('phone', v)}
                                    placeholder={t(
                                        'page.patients_create.phone_placeholder',
                                    )}
                                    errors={errors}
                                />

                                <FormInput
                                    name="email"
                                    label={t(
                                        'page.patients_create.email_address',
                                    )}
                                    icon={EnvelopeIcon}
                                    type="email"
                                    value={data.email}
                                    onChange={(v) => setData('email', v)}
                                    placeholder={t(
                                        'page.patients_create.email_placeholder',
                                    )}
                                    errors={errors}
                                />

                                <FormTextarea
                                    name="address"
                                    label={t(
                                        'page.patients_create.residential_address',
                                    )}
                                    icon={MapPinIcon}
                                    value={data.address}
                                    onChange={(v) => setData('address', v)}
                                    placeholder={t(
                                        'page.patients_create.address_placeholder',
                                    )}
                                    errors={errors}
                                    rows={2}
                                    className="sm:col-span-2"
                                />
                            </div>
                        </div>
                    </PageSection>

                    {/* Action Bar */}
                    <div className="flex flex-col items-center justify-end gap-4 sm:flex-row">
                        <Link
                            href={route('patients.index')}
                            className="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-8 py-4 font-bold text-gray-500 transition-all hover:bg-gray-50 sm:w-auto dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            <ArrowLeftIcon className="h-5 w-5" />{' '}
                            {t('common.cancel')}
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-10 py-4 font-bold text-white shadow-sm transition-all hover:bg-blue-700 disabled:opacity-50 sm:w-auto"
                        >
                            {processing ? (
                                <ArrowPathIcon className="h-5 w-5 animate-spin" />
                            ) : (
                                <CheckCircleIcon className="h-5 w-5" />
                            )}
                            {t('page.patients_create.save_registration')}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
