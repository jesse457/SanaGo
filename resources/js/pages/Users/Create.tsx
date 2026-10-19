import {
    BriefcaseIcon,
    CheckIcon,
    EnvelopeIcon,
    PhotoIcon,
    UserCircleIcon,
    UserIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import { useRef, useState } from 'react';
import type { FormEvent } from 'react';
import { toast } from 'sonner';
import {
    Alert,
    Avatar,
    Button,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SectionCard,
    Select,
    Textarea,
} from '@/Components/ui';
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

    const fileInputRef = useRef<HTMLInputElement>(null);
    const [imagePreview, setImagePreview] = useState<string | null>(null);
    const [isDragging, setIsDragging] = useState(false);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const handleFile = (file: File) => {
        if (!file) {
            return;
        }

        if (
            !['image/png', 'image/jpeg', 'image/jpg', 'image/gif'].includes(
                file.type,
            )
        ) {
            toast.error(
                'That file type is not supported. Use a PNG, JPG or GIF.',
            );

            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            toast.error(
                'That image is larger than 2 MB. Choose a smaller file.',
            );

            return;
        }

        setData('profile_picture' as any, file);
        setImagePreview(URL.createObjectURL(file));
    };

    const clearPhoto = () => {
        setImagePreview(null);
        setData('profile_picture' as any, null);

        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    };

    const submit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post(route('users.store'), {
            forceFormData: true,
            onSuccess: () => {
                setSuccessMessage(t('page.users_create.userCreated'));
                reset();
                setImagePreview(null);
            },
            onError: () => {
                setErrorMessage(t('page.users_create.pleaseCheckForm'));
            },
        });
    };

    const hasErrors = Boolean(errorMessage) || Object.keys(errors).length > 0;

    return (
        <AppLayout
            headerTitle={t('page.users_create.title')}
            headerSubtitle={t('page.users_create.subtitle')}
        >
            <Head title={t('page.users_create.head_title')} />

            <div className="max-w-8xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                <PageHeader
                    icon={UserCircleIcon}
                    eyebrow={t('page.users_create.user_management')}
                    title={t('page.users_create.new_registration')}
                    description={t('page.users_create.subtitle')}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/' },
                        {
                            label: t('page.users_create.user_management'),
                            href: route('users.index'),
                        },
                        { label: t('page.users_create.new_registration') },
                    ]}
                />

                {successMessage && (
                    <Alert
                        tone="positive"
                        title={t('common.success')}
                        action={
                            <Button
                                variant="ghost"
                                size="icon"
                                icon={XMarkIcon}
                                aria-label={t('common.close')}
                                onClick={() => setSuccessMessage(null)}
                            />
                        }
                    >
                        {successMessage}
                    </Alert>
                )}

                {hasErrors && (
                    <Alert
                        tone="critical"
                        title={t('page.users_create.registration_failed')}
                        action={
                            <Button
                                variant="ghost"
                                size="icon"
                                icon={XMarkIcon}
                                aria-label={t('common.close')}
                                onClick={() => setErrorMessage(null)}
                            />
                        }
                    >
                        {errorMessage || t('page.users_create.correct_fields')}
                    </Alert>
                )}

                <form onSubmit={submit} className="space-y-6">
                    <SectionCard
                        icon={UserCircleIcon}
                        title={t('page.users_create.personal_information')}
                    >
                        <FieldSet columns={2}>
                            <Field
                                label={t('page.users_create.full_name')}
                                htmlFor="name"
                                required
                                error={errors.name}
                                className="sm:col-span-2"
                            >
                                <Input
                                    id="name"
                                    name="name"
                                    icon={UserIcon}
                                    autoComplete="name"
                                    value={data.name}
                                    onChange={(e) =>
                                        setData('name', e.target.value)
                                    }
                                    placeholder={t(
                                        'page.users_create.full_name_placeholder',
                                    )}
                                    invalid={Boolean(errors.name)}
                                />
                            </Field>

                            <Field
                                label={t('page.users_create.phone_number')}
                                htmlFor="phone_number"
                                error={errors.phone_number}
                            >
                                <Input
                                    id="phone_number"
                                    name="phone_number"
                                    type="tel"
                                    autoComplete="tel"
                                    value={data.phone_number}
                                    onChange={(e) =>
                                        setData('phone_number', e.target.value)
                                    }
                                    placeholder={t(
                                        'page.users_create.phone_placeholder',
                                    )}
                                    invalid={Boolean(errors.phone_number)}
                                />
                            </Field>

                            <Field
                                label={t('page.users_create.email_address')}
                                htmlFor="email"
                                required
                                error={errors.email}
                            >
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    autoComplete="email"
                                    icon={EnvelopeIcon}
                                    value={data.email}
                                    onChange={(e) =>
                                        setData('email', e.target.value)
                                    }
                                    placeholder={t(
                                        'page.users_create.email_placeholder',
                                    )}
                                    invalid={Boolean(errors.email)}
                                />
                            </Field>

                            <Field
                                label={t('page.users_create.gender')}
                                htmlFor="gender"
                                error={errors.gender}
                            >
                                <Select
                                    id="gender"
                                    name="gender"
                                    value={data.gender}
                                    onChange={(e) =>
                                        setData('gender', e.target.value)
                                    }
                                    invalid={Boolean(errors.gender)}
                                >
                                    <option value="">
                                        {t('page.users_create.select_gender')}
                                    </option>
                                    <option value="Male">
                                        {t('common.male')}
                                    </option>
                                    <option value="Female">
                                        {t('common.female')}
                                    </option>
                                </Select>
                            </Field>

                            <Field
                                label={t(
                                    'page.users_create.temporary_password',
                                )}
                                htmlFor="password"
                                required
                                error={errors.password}
                            >
                                <Input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autoComplete="new-password"
                                    value={data.password}
                                    onChange={(e) =>
                                        setData('password', e.target.value)
                                    }
                                    placeholder={t(
                                        'page.users_create.password_placeholder',
                                    )}
                                    invalid={Boolean(errors.password)}
                                />
                            </Field>

                            <Field
                                label={t('page.users_create.physical_address')}
                                htmlFor="address"
                                error={errors.address}
                                className="sm:col-span-2"
                            >
                                <Textarea
                                    id="address"
                                    name="address"
                                    rows={2}
                                    autoComplete="street-address"
                                    value={data.address}
                                    onChange={(e) =>
                                        setData('address', e.target.value)
                                    }
                                    placeholder={t(
                                        'page.users_create.address_placeholder',
                                    )}
                                    invalid={Boolean(errors.address)}
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    <SectionCard
                        icon={BriefcaseIcon}
                        title={t('page.users_create.employment_details')}
                    >
                        <FieldSet columns={2}>
                            <Field
                                label={t('page.users_create.department')}
                                htmlFor="department_id"
                                required
                                error={errors.department_id}
                            >
                                <Select
                                    id="department_id"
                                    name="department_id"
                                    value={data.department_id}
                                    onChange={(e) =>
                                        setData('department_id', e.target.value)
                                    }
                                    invalid={Boolean(errors.department_id)}
                                >
                                    <option value="">
                                        {t(
                                            'page.users_create.select_department',
                                        )}
                                    </option>
                                    {departments.map((dept) => (
                                        <option key={dept.id} value={dept.id}>
                                            {dept.name}
                                        </option>
                                    ))}
                                </Select>
                            </Field>

                            <Field
                                label={t('page.users_create.system_role')}
                                htmlFor="role"
                                required
                                error={errors.role}
                            >
                                <Select
                                    id="role"
                                    name="role"
                                    value={data.role}
                                    onChange={(e) =>
                                        setData('role', e.target.value)
                                    }
                                    invalid={Boolean(errors.role)}
                                >
                                    <option value="">
                                        {t('page.users_create.select_role')}
                                    </option>
                                    {roles.map((role) => (
                                        <option key={role.id} value={role.name}>
                                            {role.name.replace('_', ' ')}
                                        </option>
                                    ))}
                                </Select>
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    <SectionCard
                        icon={PhotoIcon}
                        title={t('page.users_create.profile_picture')}
                        description={t('page.users_create.max_photo_size')}
                    >
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
                            className={`flex flex-col items-start gap-4 rounded-lg border border-dashed p-4 transition-colors duration-200 ease-premium sm:flex-row sm:items-center ${
                                isDragging
                                    ? 'border-accent bg-accent-soft'
                                    : 'border-line-strong bg-surface-muted'
                            }`}
                        >
                            <Avatar
                                size="xl"
                                src={imagePreview}
                                name={
                                    data.name ||
                                    t('page.users_create.full_name')
                                }
                            />

                            <div className="min-w-0 flex-1">
                                <p className="text-[13px] font-medium text-ink">
                                    {t('page.users_create.upload_photo')}
                                </p>
                                <p className="mt-0.5 text-[12px] text-ink-subtle">
                                    {t('page.users_create.max_photo_size')}
                                </p>
                            </div>

                            <div className="flex shrink-0 items-center gap-2">
                                {imagePreview && (
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        icon={XMarkIcon}
                                        onClick={clearPhoto}
                                    >
                                        {t('common.delete')}
                                    </Button>
                                )}
                                <Button
                                    type="button"
                                    variant="secondary"
                                    size="sm"
                                    icon={PhotoIcon}
                                    onClick={() =>
                                        fileInputRef.current?.click()
                                    }
                                >
                                    {t('page.users_create.upload_photo')}
                                </Button>
                                <input
                                    ref={fileInputRef}
                                    type="file"
                                    name="profile_picture"
                                    className="sr-only"
                                    accept="image/*"
                                    onChange={(e) => {
                                        const file = e.target.files?.[0];

                                        if (file) {
                                            handleFile(file);
                                        }
                                    }}
                                />
                            </div>
                        </div>

                        {(errors as any).profile_picture && (
                            <p className="mt-2 text-[12px] leading-snug font-medium text-critical-ink">
                                {(errors as any).profile_picture}
                            </p>
                        )}
                    </SectionCard>

                    <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <Button variant="secondary" href={route('users.index')}>
                            {t('common.cancel')}
                        </Button>
                        <Button
                            type="submit"
                            icon={CheckIcon}
                            loading={processing}
                        >
                            {processing
                                ? t('page.users_create.creating')
                                : t('page.users_create.complete_registration')}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
