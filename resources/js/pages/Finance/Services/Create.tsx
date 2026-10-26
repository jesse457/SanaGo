import { CheckIcon, TagIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import {
    Button,
    Checkbox,
    Field,
    FieldSet,
    Input,
    PageHeader,
    Select,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { FormPageCard } from '@/pages/Radiology/components';

export default function ServicesCreate({
    categories,
}: {
    categories: string[];
}) {
    const { t } = useTranslation();

    const categoryOptions = categories.map((value) => ({
        value,
        label: t(`finance.services.index.category_${value}`),
    }));
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        code: '',
        category: 'consultation',
        description: '',
        default_price: 0,
        is_active: true,
        requires_upfront_payment: false,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('finance.services.store'), {
            onSuccess: () => {
                reset();
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('finance.services.create.title')}
            headerSubtitle={t('finance.services.create.subtitle')}
        >
            <Head title={t('finance.services.create.title')} />

            <div className="mx-auto flex max-w-5xl flex-col gap-6">
                <PageHeader
                    title={t('finance.services.create.title')}
                    description={t('finance.services.create.subtitle')}
                    breadcrumbs={[
                        { label: t('finance.services.create.services') },
                        { label: t('finance.services.create.title') },
                    ]}
                />

                <FormPageCard
                    title={t('finance.services.create.details')}
                    headingIcon={TagIcon}
                    onSubmit={handleSubmit}
                    footer={
                        <>
                            <Button
                                variant="secondary"
                                href={route('finance.services.index')}
                            >
                                {t('common.cancel')}
                            </Button>
                            <Button
                                type="submit"
                                loading={processing}
                                icon={CheckIcon}
                            >
                                {processing
                                    ? t('finance.services.create.creating')
                                    : t('finance.services.create.create')}
                            </Button>
                        </>
                    }
                >
                    <FieldSet columns={2}>
                        <Field
                            label={t('finance.services.create.name')}
                            htmlFor="name"
                            required
                            error={errors.name}
                        >
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                                placeholder={t(
                                    'finance.services.create.name_placeholder',
                                )}
                                invalid={Boolean(errors.name)}
                            />
                        </Field>

                        <Field
                            label={t('finance.services.create.code')}
                            htmlFor="code"
                            required
                            error={errors.code}
                        >
                            <Input
                                id="code"
                                type="text"
                                value={data.code}
                                onChange={(e) =>
                                    setData('code', e.target.value)
                                }
                                placeholder={t(
                                    'finance.services.create.code_placeholder',
                                )}
                                invalid={Boolean(errors.code)}
                            />
                        </Field>

                        <Field
                            label={t('finance.services.create.category')}
                            htmlFor="category"
                            required
                            error={errors.category}
                        >
                            <Select
                                id="category"
                                value={data.category}
                                onChange={(e) =>
                                    setData('category', e.target.value)
                                }
                                invalid={Boolean(errors.category)}
                            >
                                {categoryOptions.map((option) => (
                                    <option
                                        key={option.value}
                                        value={option.value}
                                    >
                                        {option.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>

                        <Field
                            label={t('finance.services.create.price')}
                            htmlFor="default_price"
                            required
                            error={errors.default_price}
                        >
                            <Input
                                id="default_price"
                                type="number"
                                value={data.default_price}
                                onChange={(e) =>
                                    setData(
                                        'default_price',
                                        Number(e.target.value),
                                    )
                                }
                                placeholder="3000"
                                invalid={Boolean(errors.default_price)}
                            />
                        </Field>

                        <Field
                            label={t('finance.services.create.description')}
                            htmlFor="description"
                            error={errors.description}
                            className="sm:col-span-2"
                        >
                            <Textarea
                                id="description"
                                value={data.description}
                                onChange={(e) =>
                                    setData('description', e.target.value)
                                }
                                rows={3}
                                placeholder={t(
                                    'finance.services.create.description_placeholder',
                                )}
                                invalid={Boolean(errors.description)}
                            />
                        </Field>
                    </FieldSet>

                    <div className="flex flex-col gap-3 border-t border-line pt-4">
                        <Checkbox
                            id="is_active"
                            checked={data.is_active}
                            onChange={(e) =>
                                setData('is_active', e.target.checked)
                            }
                            label={t('finance.services.create.active')}
                        />
                        <Checkbox
                            id="requires_upfront_payment"
                            checked={data.requires_upfront_payment}
                            onChange={(e) =>
                                setData(
                                    'requires_upfront_payment',
                                    e.target.checked,
                                )
                            }
                            label={t('finance.services.create.upfront')}
                        />
                    </div>
                </FormPageCard>
            </div>
        </AppLayout>
    );
}
