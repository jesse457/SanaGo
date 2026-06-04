import {
    HomeIcon,
    ChevronRightIcon,
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
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

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

    const inputStyle = (error: string | undefined) => `
        w-full pl-12 pr-4 py-3 bg-white dark:bg-neutral-800 border rounded-lg
        font-semibold text-sm transition-all outline-none
        ${error ? 'border-red-500 focus:ring-2 focus:ring-red-500' : 'border-gray-200 dark:border-neutral-700 focus:ring-2 focus:ring-blue-500'}
        dark:text-neutral-100
    `;

    const selectStyle = (error: string | undefined) => `
        w-full px-4 py-3 bg-white dark:bg-neutral-800 border rounded-lg
        font-semibold text-sm transition-all outline-none
        ${error ? 'border-red-500 focus:ring-2 focus:ring-red-500' : 'border-gray-200 dark:border-neutral-700 focus:ring-2 focus:ring-blue-500'}
        dark:text-neutral-100
    `;

    return (
        <AppLayout
            headerTitle={t('page.patients_create.title')}
            headerSubtitle={t('page.patients_create.subtitle')}
        >
            <Head title={t('page.patients_create.head_title')} />

            <div className="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" />{' '}
                            {t('common.home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-3 w-3" />
                        </li>
                        <li>
                            <Link
                                href={route('patients.index')}
                                className="hover:text-blue-600"
                            >
                                {t('page.patients_create.patient_management')}
                            </Link>
                        </li>
                        <li>
                            <ChevronRightIcon className="h-3 w-3" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {t('page.patients_create.new_registration')}
                        </li>
                    </ol>
                </nav>

                <form onSubmit={submit} className="space-y-6">
                    {/* Main Card */}
                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <div className="flex items-center gap-4 border-b border-gray-100 bg-gray-50/50 p-6 dark:border-neutral-700 dark:bg-neutral-800/50">
                            <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white shadow-sm">
                                <UserPlusIcon className="h-5 w-5" />
                            </div>
                            <div>
                                <h3 className="text-lg font-bold tracking-tight text-gray-900 dark:text-neutral-100">
                                    {t(
                                        'page.patients_create.basic_information',
                                    )}
                                </h3>
                                <p className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    {t('page.patients_create.identity_details')}
                                </p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2 lg:grid-cols-3">
                            {/* First Name */}
                            <div className="space-y-1.5">
                                <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    {t('page.patients_create.first_name')}{' '}
                                    <span className="text-red-500">*</span>
                                </label>
                                <div className="relative">
                                    <UserIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                    <input
                                        type="text"
                                        value={data.first_name}
                                        onChange={(e) =>
                                            setData(
                                                'first_name',
                                                e.target.value,
                                            )
                                        }
                                        className={inputStyle(
                                            errors.first_name,
                                        )}
                                        placeholder={t(
                                            'page.patients_create.first_name_placeholder',
                                        )}
                                    />
                                </div>
                                {errors.first_name && (
                                    <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                        {errors.first_name}
                                    </p>
                                )}
                            </div>

                            {/* Last Name */}
                            <div className="space-y-1.5">
                                <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    {t('page.patients_create.last_name')}{' '}
                                    <span className="text-red-500">*</span>
                                </label>
                                <div className="relative">
                                    <UserIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                    <input
                                        type="text"
                                        value={data.last_name}
                                        onChange={(e) =>
                                            setData('last_name', e.target.value)
                                        }
                                        className={inputStyle(errors.last_name)}
                                        placeholder={t(
                                            'page.patients_create.last_name_placeholder',
                                        )}
                                    />
                                </div>
                                {errors.last_name && (
                                    <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                        {errors.last_name}
                                    </p>
                                )}
                            </div>

                            {/* DOB */}
                            <div className="space-y-1.5">
                                <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    {t('page.patients_create.date_of_birth')}{' '}
                                    <span className="text-red-500">*</span>
                                </label>
                                <div className="relative">
                                    <CalendarIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                    <input
                                        type="date"
                                        value={data.date_of_birth}
                                        onChange={(e) =>
                                            setData(
                                                'date_of_birth',
                                                e.target.value,
                                            )
                                        }
                                        className={inputStyle(undefined)}
                                    />
                                </div>
                            </div>

                            {/* Gender */}
                            <div className="space-y-1.5">
                                <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    {t('page.patients_create.gender')}{' '}
                                    <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={data.gender}
                                    onChange={(e) =>
                                        setData('gender', e.target.value)
                                    }
                                    className={selectStyle(errors.gender)}
                                >
                                    <option value="">
                                        {t(
                                            'page.patients_create.select_gender',
                                        )}
                                    </option>
                                    <option value="male">
                                        {t('common.male')}
                                    </option>
                                    <option value="female">
                                        {t('common.female')}
                                    </option>
                                    <option value="other">
                                        {t('common.other')}
                                    </option>
                                </select>
                            </div>

                            {/* Blood Type */}
                            <div className="space-y-1.5">
                                <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    {t('page.patients_create.blood_type')}
                                </label>
                                <div className="relative">
                                    <BeakerIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                    <select
                                        value={data.blood_type}
                                        onChange={(e) =>
                                            setData(
                                                'blood_type',
                                                e.target.value,
                                            )
                                        }
                                        className={inputStyle(undefined)}
                                    >
                                        <option value="">
                                            {t('page.patients_create.unknown')}
                                        </option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>
                            </div>

                            {/* Allergies */}
                            <div className="space-y-1.5">
                                <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    {t('page.patients_create.allergies')}
                                </label>
                                <div className="relative">
                                    <ExclamationTriangleIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                    <input
                                        type="text"
                                        value={data.allergies}
                                        onChange={(e) =>
                                            setData('allergies', e.target.value)
                                        }
                                        className={inputStyle(undefined)}
                                        placeholder={t(
                                            'page.patients_create.allergies_placeholder',
                                        )}
                                    />
                                </div>
                            </div>
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
                                <div className="space-y-1.5">
                                    <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.patients_create.phone_number')}
                                    </label>
                                    <div className="relative">
                                        <PhoneIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                        <input
                                            type="text"
                                            value={data.phone}
                                            onChange={(e) =>
                                                setData('phone', e.target.value)
                                            }
                                            className={inputStyle(undefined)}
                                            placeholder={t(
                                                'page.patients_create.phone_placeholder',
                                            )}
                                        />
                                    </div>
                                </div>

                                <div className="space-y-1.5">
                                    <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t(
                                            'page.patients_create.email_address',
                                        )}
                                    </label>
                                    <div className="relative">
                                        <EnvelopeIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                        <input
                                            type="email"
                                            value={data.email}
                                            onChange={(e) =>
                                                setData('email', e.target.value)
                                            }
                                            className={inputStyle(errors.email)}
                                            placeholder={t(
                                                'page.patients_create.email_placeholder',
                                            )}
                                        />
                                    </div>
                                </div>

                                <div className="space-y-1.5 sm:col-span-2">
                                    <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t(
                                            'page.patients_create.residential_address',
                                        )}
                                    </label>
                                    <div className="relative">
                                        <MapPinIcon className="absolute top-4 left-4 h-5 w-5 text-gray-400" />
                                        <textarea
                                            rows={2}
                                            value={data.address}
                                            onChange={(e) =>
                                                setData(
                                                    'address',
                                                    e.target.value,
                                                )
                                            }
                                            className="w-full resize-none rounded-lg border border-gray-200 bg-white py-3 pr-4 pl-12 text-sm font-semibold transition-all outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                                            placeholder={t(
                                                'page.patients_create.address_placeholder',
                                            )}
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Action Bar */}
                    <div className="flex flex-col items-center justify-end gap-4 pb-12 sm:flex-row">
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
