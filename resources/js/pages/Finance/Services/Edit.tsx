import { TagIcon, ArrowPathIcon, CheckIcon } from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Service {
    id: number;
    name: string;
    code: string;
    category: string;
    description: string | null;
    default_price: number;
    is_active: boolean;
    requires_upfront_payment: boolean;
}

export default function ServicesEdit({
    service,
    categories,
}: {
    service: Service;
    /** `ServiceCategory` values, so the options are never a second list to keep. */
    categories: string[];
}) {
    const { t } = useTranslation();

    const categoryOptions = categories.map((value) => ({
        value,
        label: t(`finance.services.index.category_${value}`),
    }));

    const { data, setData, put, processing, errors } = useForm({
        name: service.name,
        code: service.code,
        category: service.category,
        description: service.description || '',
        default_price: service.default_price,
        is_active: service.is_active,
        requires_upfront_payment: service.requires_upfront_payment,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('finance.services.update', service.id));
    };

    return (
        <AppLayout
            headerTitle={t('finance.services.edit.title')}
            headerSubtitle={t('finance.services.edit.subtitle', {
                name: service.name,
            })}
        >
            <Head
                title={t('finance.services.edit.subtitle', {
                    name: service.name,
                })}
            />

            <div className="mx-auto max-w-3xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('nav.home'), href: route('dashboard') },
                        {
                            label: t('nav.services_catalog'),
                            href: route('finance.services.index'),
                        },
                        { label: service.name },
                    ]}
                />

                <PageSection
                    title={t('finance.services.edit.details')}
                    icon={TagIcon}
                >
                    <form onSubmit={handleSubmit} className="space-y-6 p-6">
                        <div className="grid gap-6 sm:grid-cols-2">
                            <FormInput
                                name="name"
                                label={t('finance.services.edit.name')}
                                required
                                errors={errors}
                                value={data.name}
                                onChange={(v) => setData('name', v)}
                            />
                            <FormInput
                                name="code"
                                label={t('finance.services.index.code')}
                                required
                                errors={errors}
                                value={data.code}
                                onChange={(v) => setData('code', v)}
                            />
                        </div>

                        <FormSelect
                            name="category"
                            label={t('finance.services.index.category')}
                            required
                            value={data.category}
                            onChange={(v) => setData('category', v)}
                            options={categoryOptions}
                        />

                        <FormInput
                            name="default_price"
                            label={t('finance.services.edit.price')}
                            type="number"
                            required
                            errors={errors}
                            value={data.default_price}
                            onChange={(v) =>
                                setData('default_price', Number(v))
                            }
                        />

                        <FormTextarea
                            name="description"
                            label={t('finance.services.edit.description')}
                            errors={errors}
                            value={data.description}
                            onChange={(v) => setData('description', v)}
                            rows={3}
                        />

                        <div className="space-y-4 border-t border-line pt-4">
                            <div className="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    id="is_active"
                                    checked={data.is_active}
                                    onChange={(e) =>
                                        setData('is_active', e.target.checked)
                                    }
                                    className="rounded border-line-strong bg-surface text-accent-ink focus:ring-blue-500"
                                />
                                <label
                                    htmlFor="is_active"
                                    className="text-sm font-medium text-ink-muted"
                                >
                                    {t('finance.services.edit.active')}
                                </label>
                            </div>

                            <div className="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    id="requires_upfront_payment"
                                    checked={data.requires_upfront_payment}
                                    onChange={(e) =>
                                        setData(
                                            'requires_upfront_payment',
                                            e.target.checked,
                                        )
                                    }
                                    className="rounded border-line-strong bg-surface text-accent-ink focus:ring-blue-500"
                                />
                                <label
                                    htmlFor="requires_upfront_payment"
                                    className="text-sm font-medium text-ink-muted"
                                >
                                    {t('finance.services.edit.upfront')}
                                </label>
                            </div>
                        </div>

                        <div className="flex items-center justify-end gap-3 border-t border-line pt-4">
                            <Link
                                href={route('finance.services.index')}
                                className="rounded-lg border border-line-strong bg-surface px-4 py-2 text-sm font-medium text-ink-muted hover:bg-surface-hover"
                            >
                                {t('common.cancel')}
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-accent px-6 py-3 text-sm font-bold text-ink-inverse transition-colors hover:bg-accent-hover disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />{' '}
                                        {t('finance.services.edit.saving')}
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" />{' '}
                                        {t('finance.services.edit.save')}
                                    </span>
                                )}
                            </button>
                        </div>
                    </form>
                </PageSection>
            </div>
        </AppLayout>
    );
}
