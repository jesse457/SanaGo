import {
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
import { useState } from 'react';
import type { FormEvent } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

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
        if (!file) {
            return;
        }

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

    const submit = (e: FormEvent) => {
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

            <div className="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        {
                            href: route('users.index'),
                            label: t('page.users_create.user_management'),
                        },
                        {
                            label: t('page.users_create.new_registration'),
                        },
                    ]}
                />

                <PageSection>
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
                                <FormInput
                                    name="name"
                                    label={t('page.users_create.full_name')}
                                    icon={UserIcon}
                                    value={data.name}
                                    onChange={(val) => setData('name', val)}
                                    placeholder={t(
                                        'page.users_create.full_name_placeholder',
                                    )}
                                    errors={errors}
                                    required
                                    className="md:col-span-2"
                                />

                                <FormInput
                                    name="phone_number"
                                    label={t('page.users_create.phone_number')}
                                    icon={PhoneIcon}
                                    type="tel"
                                    value={data.phone_number}
                                    onChange={(val) =>
                                        setData('phone_number', val)
                                    }
                                    placeholder={t(
                                        'page.users_create.phone_placeholder',
                                    )}
                                    errors={errors}
                                />

                                <FormInput
                                    name="email"
                                    label={t('page.users_create.email_address')}
                                    icon={EnvelopeIcon}
                                    type="email"
                                    value={data.email}
                                    onChange={(val) => setData('email', val)}
                                    placeholder={t(
                                        'page.users_create.email_placeholder',
                                    )}
                                    errors={errors}
                                    required
                                />

                                <FormSelect
                                    name="gender"
                                    label={t('page.users_create.gender')}
                                    value={data.gender}
                                    onChange={(val) => setData('gender', val)}
                                    errors={errors}
                                    options={[
                                        {
                                            value: 'Male',
                                            label: t('common.male'),
                                        },
                                        {
                                            value: 'Female',
                                            label: t('common.female'),
                                        },
                                    ]}
                                    placeholder={t(
                                        'page.users_create.select_gender',
                                    )}
                                />

                                <FormInput
                                    name="password"
                                    label={t(
                                        'page.users_create.temporary_password',
                                    )}
                                    type="password"
                                    value={data.password}
                                    onChange={(val) => setData('password', val)}
                                    placeholder={t(
                                        'page.users_create.password_placeholder',
                                    )}
                                    errors={errors}
                                    required
                                />

                                <FormTextarea
                                    name="address"
                                    label={t(
                                        'page.users_create.physical_address',
                                    )}
                                    icon={MapPinIcon}
                                    value={data.address}
                                    onChange={(val) => setData('address', val)}
                                    placeholder={t(
                                        'page.users_create.address_placeholder',
                                    )}
                                    errors={errors}
                                    rows={2}
                                    className="md:col-span-2"
                                />
                            </div>
                        </div>

                        <div className="space-y-6 pt-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <BriefcaseIcon className="h-5 w-5" />{' '}
                                {t('page.users_create.employment_details')}
                            </h3>

                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormSelect
                                    name="department_id"
                                    label={t('page.users_create.department')}
                                    value={data.department_id}
                                    onChange={(val) =>
                                        setData('department_id', val)
                                    }
                                    errors={errors}
                                    options={departments.map((dept) => ({
                                        value: dept.id,
                                        label: dept.name,
                                    }))}
                                    placeholder={t(
                                        'page.users_create.select_department',
                                    )}
                                    required
                                />

                                <FormSelect
                                    name="role"
                                    label={t('page.users_create.system_role')}
                                    value={data.role}
                                    onChange={(val) => setData('role', val)}
                                    errors={errors}
                                    options={roles.map((role) => ({
                                        value: role.name,
                                        label: role.name.replace('_', ' '),
                                    }))}
                                    placeholder={t(
                                        'page.users_create.select_role',
                                    )}
                                    required
                                />
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
                </PageSection>
            </div>
        </AppLayout>
    );
}
