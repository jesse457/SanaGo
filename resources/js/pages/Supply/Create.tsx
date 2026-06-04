import {
    ArrowLeftIcon,
    ArrowPathIcon,
    BellAlertIcon,
    CheckIcon,
    CubeIcon,
    CurrencyDollarIcon,
    InboxStackIcon,
    PhotoIcon,
    ScaleIcon,
    Squares2X2Icon,
    TagIcon,
    XMarkIcon,
    ArchiveBoxIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useState } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

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

    return (
        <AppLayout
            headerTitle={
                isEdit
                    ? t('page.supply_create.update_inventory')
                    : t('page.supply_create.stock_registration')
            }
            headerSubtitle={t('page.supply_create.subtitle')}
        >
            <Head
                title={
                    isEdit
                        ? t('page.supply_create.edit_supply')
                        : t('page.supply_create.add_supply')
                }
            />

            <div className="mx-auto max-w-6xl space-y-6 px-4 pb-20 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: t('page.supply_create.inventory') },
                        {
                            label: isEdit
                                ? t('page.supply_create.modify_item')
                                : t('page.supply_create.new_supply'),
                        },
                    ]}
                />

                <form onSubmit={handleSubmit} className="space-y-6">
                    <PageSection
                        icon={ArchiveBoxIcon}
                        title={t('page.supply_create.supply_details')}
                        description={t('page.supply_create.identification')}
                    >
                        <div className="grid grid-cols-1 gap-6 p-8 md:grid-cols-2 lg:grid-cols-3">
                            <FormInput
                                name="name"
                                label={t('page.supply_create.supply_name')}
                                icon={TagIcon}
                                required
                                value={data.name}
                                onChange={(v) => setData('name', v)}
                                errors={errors}
                                placeholder="e.g. Sterile Surgical Gloves (Size 7.5)"
                                className="lg:col-span-2"
                            />

                            <FormSelect
                                name="category"
                                label={t('page.supply_create.stock_category')}
                                icon={Squares2X2Icon}
                                value={data.category}
                                onChange={(v) => setData('category', v)}
                                errors={errors}
                                options={[
                                    {
                                        value: 'General',
                                        label: t(
                                            'page.supply_create.category_general',
                                        ),
                                    },
                                    {
                                        value: 'PPE',
                                        label: t(
                                            'page.supply_create.category_ppe',
                                        ),
                                    },
                                    {
                                        value: 'Pharmacy',
                                        label: t(
                                            'page.supply_create.category_pharmacy',
                                        ),
                                    },
                                    {
                                        value: 'IV Fluids',
                                        label: t(
                                            'page.supply_create.category_iv_fluids',
                                        ),
                                    },
                                    {
                                        value: 'Wound Care',
                                        label: t(
                                            'page.supply_create.category_wound_care',
                                        ),
                                    },
                                    {
                                        value: 'Consumables',
                                        label: t(
                                            'page.supply_create.category_consumables',
                                        ),
                                    },
                                ]}
                            />

                            <FormInput
                                name="unit_of_measure"
                                label={t('page.supply_create.unit_of_measure')}
                                icon={ScaleIcon}
                                value={data.unit_of_measure}
                                onChange={(v) => setData('unit_of_measure', v)}
                                errors={errors}
                                placeholder="e.g. Box of 50, Unit, Liter"
                            />

                            <FormInput
                                name="cost_per_unit"
                                label={t('page.supply_create.cost_per_unit')}
                                icon={CurrencyDollarIcon}
                                type="number"
                                step="0.01"
                                value={data.cost_per_unit}
                                onChange={(v) => setData('cost_per_unit', v)}
                                errors={errors}
                                placeholder="0.00"
                            />
                        </div>
                    </PageSection>

                    <PageSection
                        icon={InboxStackIcon}
                        title={t('page.supply_create.stock_thresholds')}
                        description={t('page.supply_create.inventory_levels')}
                    >
                        <div className="grid grid-cols-1 gap-6 p-8 md:grid-cols-2">
                            <FormInput
                                name="current_stock"
                                label={
                                    isEdit
                                        ? t(
                                              'page.supply_create.current_stock_level',
                                          )
                                        : t(
                                              'page.supply_create.initial_stock_quantity',
                                          )
                                }
                                icon={InboxStackIcon}
                                type="number"
                                value={data.current_stock}
                                onChange={(v) => setData('current_stock', v)}
                                errors={errors}
                                placeholder="0"
                            />

                            <div>
                                <FormInput
                                    name="min_stock_level"
                                    label={t(
                                        'page.supply_create.low_stock_threshold',
                                    )}
                                    icon={BellAlertIcon}
                                    type="number"
                                    value={data.min_stock_level}
                                    onChange={(v) =>
                                        setData('min_stock_level', Number(v))
                                    }
                                    errors={errors}
                                    placeholder="10"
                                />
                                <p className="mt-1 ml-1 text-xs font-bold tracking-tight text-blue-600 uppercase dark:text-blue-400">
                                    {t('page.supply_create.alert_trigger_note')}
                                </p>
                            </div>
                        </div>
                    </PageSection>

                    <PageSection
                        icon={PhotoIcon}
                        title={t('page.supply_create.item_image')}
                        description={t('page.supply_create.optional_photo')}
                    >
                        <div className="p-8">
                            <div className="flex items-center gap-6">
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
                                                setData('image', null);
                                            }}
                                            className="absolute -top-2 -right-2 rounded-full border border-gray-100 bg-white p-1 text-red-500 shadow-md hover:bg-red-50 dark:border-neutral-600 dark:bg-neutral-700 dark:hover:bg-red-900/20"
                                        >
                                            <XMarkIcon className="h-4 w-4" />
                                        </button>
                                    </div>
                                ) : (
                                    <div className="flex h-24 w-24 items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-100 dark:border-neutral-600 dark:bg-neutral-700">
                                        <CubeIcon className="h-8 w-8 text-gray-400" />
                                    </div>
                                )}
                                <label className="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-blue-50 px-4 py-2 text-sm font-bold text-blue-600 transition-colors hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30">
                                    <span>
                                        {t('page.supply_create.upload_image')}
                                    </span>
                                    <input
                                        type="file"
                                        className="sr-only"
                                        accept="image/*"
                                        onChange={(e) => {
                                            const file = e.target.files?.[0];

                                            if (file) {
                                                setData('image', file);
                                                setImagePreview(
                                                    URL.createObjectURL(file),
                                                );
                                            }
                                        }}
                                    />
                                </label>
                                <p className="text-xs text-gray-400 dark:text-neutral-500">
                                    {t('page.supply_create.max_upload_size')}
                                </p>
                            </div>
                        </div>
                    </PageSection>

                    {/* Footer Actions */}
                    <div className="flex flex-col items-center justify-end gap-4 sm:flex-row">
                        <Link
                            href={route('supplies.index')}
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
                                <CheckIcon className="h-5 w-5" />
                            )}
                            {isEdit
                                ? t('page.supply_create.update_inventory')
                                : t('page.supply_create.register_supply')}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
