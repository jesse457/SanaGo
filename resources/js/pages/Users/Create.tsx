import {
    HomeIcon,
    ChevronRightIcon,
    UserCircleIcon,
    UserIcon,
    PhoneIcon,
    EnvelopeIcon,
    PhotoIcon,
    BriefcaseIcon,
    CheckIcon,
    ArrowPathIcon,
    XMarkIcon,
    ExclamationCircleIcon,
    MapPinIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Department {
    id: number;
    name: string;
}

interface Role {
    id: number;
    name: string;
}

interface Props {
    departments: Department[];
    roles: Role[];
}

export default function CreateUser({ departments, roles }: Props) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        phone_number: '',
        email: '',
        address: '',
        gender: '',
        department_id: '',
        role: '',
        password: '',
    });

    const [imagePreview, setImagePreview] = useState<string | null>(null);
    const [isDragging, setIsDragging] = useState(false);

    const handleFile = (file: File) => {
        if (!file) return;

        if (
            !['image/png', 'image/jpeg', 'image/jpg', 'image/gif'].includes(
                file.type,
            )
        ) {
            alert('Invalid file type. Please upload an image.');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('File too large. Max 2MB allowed.');
            return;
        }

        setData('profile_picture' as any, file);
        setImagePreview(URL.createObjectURL(file));
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('users.store'), {
            forceFormData: true,
            onSuccess: () => {
                reset();
                setImagePreview(null);
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('page.users_create.title')}
            headerSubtitle={t('page.users_create.subtitle')}
        >
            <Head title={t('page.users_create.head_title')} />

            <div className="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
                <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" />{' '}
                            {t('common.home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-3 w-3" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-blue-600">
                            <Link href={route('users.index')}>
                                {t('page.users_create.user_management')}
                            </Link>
                        </li>
                        <li>
                            <ChevronRightIcon className="h-3 w-3" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {t('page.users_create.new_registration')}
                        </li>
                    </ol>
                </nav>

                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <form onSubmit={submit} className="space-y-8 p-8">
                        {Object.keys(errors).length > 0 && (
                            <div className="flex items-start justify-between rounded-xl border border-red-100 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                                <div className="flex items-center gap-3">
                                    <ExclamationCircleIcon className="h-5 w-5 flex-shrink-0 text-red-600 dark:text-red-400" />
                                    <div>
                                        <h3 className="text-sm font-bold text-red-800 dark:text-red-300">
                                            {t(
                                                'page.users_create.registration_failed',
                                            )}
                                        </h3>
                                        <div className="mt-0.5 text-xs text-red-700 dark:text-red-400">
                                            {t(
                                                'page.users_create.correct_fields',
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        <div className="space-y-6">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <UserCircleIcon className="h-5 w-5" />{' '}
                                {t('page.users_create.personal_information')}
                            </h3>

                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div className="space-y-1.5 md:col-span-2">
                                    <label className="ml-1 block text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.users_create.full_name')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <UserIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData('name', e.target.value)
                                            }
                                            placeholder={t(
                                                'page.users_create.full_name_placeholder',
                                            )}
                                            className={`w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pr-4 pl-12 text-sm font-bold focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 ${errors.name ? 'ring-2 ring-red-500' : ''}`}
                                        />
                                    </div>
                                    {errors.name && (
                                        <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                            {errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1.5">
                                    <label className="ml-1 block text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.users_create.phone_number')}
                                    </label>
                                    <div className="relative">
                                        <PhoneIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                        <input
                                            type="tel"
                                            value={data.phone_number}
                                            onChange={(e) =>
                                                setData(
                                                    'phone_number',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'page.users_create.phone_placeholder',
                                            )}
                                            className="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pr-4 pl-12 text-sm font-bold focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                        />
                                    </div>
                                    {errors.phone_number && (
                                        <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                            {errors.phone_number}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1.5">
                                    <label className="ml-1 block text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.users_create.email_address')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <EnvelopeIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                        <input
                                            type="email"
                                            value={data.email}
                                            onChange={(e) =>
                                                setData('email', e.target.value)
                                            }
                                            placeholder={t(
                                                'page.users_create.email_placeholder',
                                            )}
                                            className={`w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pr-4 pl-12 text-sm font-bold focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 ${errors.email ? 'ring-2 ring-red-500' : ''}`}
                                        />
                                    </div>
                                    {errors.email && (
                                        <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                            {errors.email}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1.5">
                                    <label className="ml-1 block text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.users_create.gender')}
                                    </label>
                                    <select
                                        value={data.gender}
                                        onChange={(e) =>
                                            setData('gender', e.target.value)
                                        }
                                        className="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                    >
                                        <option value="">
                                            {t(
                                                'page.users_create.select_gender',
                                            )}
                                        </option>
                                        <option value="Male">
                                            {t('common.male')}
                                        </option>
                                        <option value="Female">
                                            {t('common.female')}
                                        </option>
                                    </select>
                                </div>

                                <div className="space-y-1.5">
                                    <label className="ml-1 block text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t(
                                            'page.users_create.temporary_password',
                                        )}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="password"
                                        value={data.password}
                                        onChange={(e) =>
                                            setData('password', e.target.value)
                                        }
                                        placeholder={t(
                                            'page.users_create.password_placeholder',
                                        )}
                                        className={`w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 ${errors.password ? 'ring-2 ring-red-500' : ''}`}
                                    />
                                    {errors.password && (
                                        <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                            {errors.password}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1.5 md:col-span-2">
                                    <label className="ml-1 block text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t(
                                            'page.users_create.physical_address',
                                        )}
                                    </label>
                                    <div className="relative">
                                        <MapPinIcon className="absolute top-3.5 left-4 h-5 w-5 text-gray-400" />
                                        <textarea
                                            value={data.address}
                                            onChange={(e) =>
                                                setData(
                                                    'address',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'page.users_create.address_placeholder',
                                            )}
                                            rows={2}
                                            className="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 py-3 pr-4 pl-12 text-sm font-bold focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="space-y-6 pt-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <BriefcaseIcon className="h-5 w-5" />{' '}
                                {t('page.users_create.employment_details')}
                            </h3>

                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div className="space-y-1.5">
                                    <label className="ml-1 block text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.users_create.department')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={data.department_id}
                                        onChange={(e) =>
                                            setData(
                                                'department_id',
                                                e.target.value,
                                            )
                                        }
                                        className={`w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 ${errors.department_id ? 'ring-2 ring-red-500' : ''}`}
                                    >
                                        <option value="">
                                            {t(
                                                'page.users_create.select_department',
                                            )}
                                        </option>
                                        {departments.map((dept) => (
                                            <option
                                                key={dept.id}
                                                value={dept.id}
                                            >
                                                {dept.name}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.department_id && (
                                        <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                            {errors.department_id}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1.5">
                                    <label className="ml-1 block text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.users_create.system_role')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={data.role}
                                        onChange={(e) =>
                                            setData('role', e.target.value)
                                        }
                                        className={`w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 ${errors.role ? 'ring-2 ring-red-500' : ''}`}
                                    >
                                        <option value="">
                                            {t('page.users_create.select_role')}
                                        </option>
                                        {roles.map((role) => (
                                            <option
                                                key={role.id}
                                                value={role.name}
                                            >
                                                {role.name.replace('_', ' ')}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.role && (
                                        <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                            {errors.role}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="space-y-6 pt-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <PhotoIcon className="h-5 w-5" />{' '}
                                {t('page.users_create.profile_picture')}
                            </h3>

                            <div
                                onDragOver={(e) => {
                                    e.preventDefault();
                                    setIsDragging(true);
                                }}
                                onDragLeave={() => setIsDragging(false)}
                                onDrop={(e) => {
                                    e.preventDefault();
                                    setIsDragging(false);
                                    handleFile(e.dataTransfer.files[0]);
                                }}
                                className={`flex flex-col items-center justify-center rounded-xl border-2 border-dashed p-8 transition-all ${isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/10' : 'border-gray-200 bg-gray-50 hover:bg-gray-100 dark:border-neutral-600 dark:bg-neutral-700/30 dark:hover:bg-neutral-700'}`}
                            >
                                {imagePreview ? (
                                    <div className="relative mb-4">
                                        <img
                                            src={imagePreview}
                                            alt="Preview"
                                            className="h-24 w-24 rounded-xl object-cover shadow-lg"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setImagePreview(null);
                                                setData(
                                                    'profile_picture' as any,
                                                    null,
                                                );
                                            }}
                                            className="absolute -top-2 -right-2 rounded-full border border-gray-100 bg-white p-1 text-red-500 shadow-md transition-colors hover:bg-red-50 dark:border-neutral-600 dark:bg-neutral-700 dark:hover:bg-red-900/20"
                                        >
                                            <XMarkIcon className="h-4 w-4" />
                                        </button>
                                    </div>
                                ) : (
                                    <PhotoIcon className="mb-2 h-12 w-12 text-gray-300 dark:text-neutral-500" />
                                )}

                                <label className="cursor-pointer text-sm font-bold text-blue-600 hover:text-blue-700">
                                    <span>
                                        {t('page.users_create.upload_photo')}
                                    </span>
                                    <input
                                        type="file"
                                        className="sr-only"
                                        accept="image/*"
                                        onChange={(e) =>
                                            e.target.files?.[0] &&
                                            handleFile(e.target.files[0])
                                        }
                                    />
                                </label>
                                <p className="mt-1 text-xs font-bold text-gray-400 uppercase dark:text-neutral-500">
                                    {t('page.users_create.max_photo_size')}
                                </p>
                            </div>
                            {(errors as any).profile_picture && (
                                <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                    {(errors as any).profile_picture}
                                </p>
                            )}
                        </div>

                        <div className="flex items-center justify-end gap-4 border-t border-gray-100 pt-8 dark:border-neutral-700">
                            <Link
                                href={route('users.index')}
                                className="px-6 py-3 text-sm font-bold text-gray-500 transition-colors hover:text-gray-700 dark:text-neutral-300 dark:hover:text-neutral-100"
                            >
                                {t('common.cancel')}
                            </Link>

                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-8 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                            >
                                {processing ? (
                                    <>
                                        <ArrowPathIcon className="h-5 w-5 animate-spin" />
                                        {t('page.users_create.creating')}
                                    </>
                                ) : (
                                    <>
                                        <CheckIcon className="h-5 w-5" />
                                        {t(
                                            'page.users_create.complete_registration',
                                        )}
                                    </>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
