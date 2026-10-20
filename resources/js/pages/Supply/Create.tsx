import {
    ArchiveBoxIcon,
    BellAlertIcon,
    CurrencyDollarIcon,
    InboxStackIcon,
    PhotoIcon,
    ScaleIcon,
    TagIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import {
    Alert,
    Button,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SectionCard,
    Select,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { DrugImageUpload } from '../Drugs/components/DrugImageUpload';
import { StockLevelSummary } from './components/StockLevelSummary';

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

    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: supply?.name || '',
        category: supply?.category || 'General',
        unit_of_measure: supply?.unit_of_measure || '',
        cost_per_unit: supply?.cost_per_unit || '',
        current_stock: supply?.current_stock ?? '',
        min_stock_level: supply?.min_stock_level ?? 10,
        image: null as File | null,
    });

    const [imagePreview, setImagePreview] = useState<string | null>(null);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);

        if (isEdit) {
            put(route('supplies.update', supply.id), {
                forceFormData: true,
                onSuccess: () => {
                    setSuccessMessage(
                        t('page.supply_create.updatedSuccessfully'),
                    );
                },
                onError: () => {
                    setErrorMessage(t('page.supply_create.pleaseCheckForm'));
                },
            });
        } else {
            post(route('supplies.store'), {
                forceFormData: true,
                onSuccess: () => {
                    setSuccessMessage(
                        t('page.supply_create.createdSuccessfully'),
                    );
                    reset();
                    setImagePreview(null);
                },
                onError: () => {
                    setErrorMessage(t('page.supply_create.pleaseCheckForm'));
                },
            });
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

            <div className="mx-auto max-w-4xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={ArchiveBoxIcon}
                    eyebrow={t('page.supply_create.inventory')}
                    title={
                        isEdit
                            ? t('page.supply_create.update_inventory')
                            : t('page.supply_create.stock_registration')
                    }
                    description={t('page.supply_create.subtitle')}
                    breadcrumbs={[
                        {
                            label: t('page.supply_create.inventory'),
                            href: route('supplies.index'),
                        },
                        {
                            label: isEdit
                                ? t('page.supply_create.modify_item')
                                : t('page.supply_create.new_supply'),
                        },
                    ]}
                />

                {successMessage && (
                    <Alert
                        tone="positive"
                        title={t('common.success')}
                        action={
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                icon={XMarkIcon}
                                onClick={() => setSuccessMessage(null)}
                            />
                        }
                    >
                        {successMessage}
                    </Alert>
                )}

                {(errorMessage || Object.keys(errors).length > 0) && (
                    <Alert
                        tone="critical"
                        title={t('common.error')}
                        action={
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                icon={XMarkIcon}
                                onClick={() => setErrorMessage(null)}
                            />
                        }
                    >
                        {errorMessage || t('common.someFieldsRequireAttention')}
                    </Alert>
                )}

                <form onSubmit={handleSubmit} className="space-y-5">
                    <SectionCard
                        title={t('page.supply_create.supply_details')}
                        icon={ArchiveBoxIcon}
                    >
                        <FieldSet columns={2}>
                            <Field
                                className="sm:col-span-2"
                                label={t('page.supply_create.supply_name')}
                                htmlFor="supply-name"
                                required
                                error={errors.name}
                            >
                                <Input
                                    id="supply-name"
                                    icon={TagIcon}
                                    value={data.name}
                                    invalid={Boolean(errors.name)}
                                    onChange={(event) =>
                                        setData('name', event.target.value)
                                    }
                                    placeholder="e.g. Sterile Surgical Gloves (Size 7.5)"
                                />
                            </Field>

                            <Field
                                label={t('page.supply_create.stock_category')}
                                htmlFor="supply-category"
                                error={errors.category}
                            >
                                <Select
                                    id="supply-category"
                                    value={data.category}
                                    invalid={Boolean(errors.category)}
                                    onChange={(event) =>
                                        setData('category', event.target.value)
                                    }
                                >
                                    <option value="General">
                                        {t(
                                            'page.supply_create.category_general',
                                        )}
                                    </option>
                                    <option value="PPE">
                                        {t('page.supply_create.category_ppe')}
                                    </option>
                                    <option value="Pharmacy">
                                        {t(
                                            'page.supply_create.category_pharmacy',
                                        )}
                                    </option>
                                    <option value="IV Fluids">
                                        {t(
                                            'page.supply_create.category_iv_fluids',
                                        )}
                                    </option>
                                    <option value="Wound Care">
                                        {t(
                                            'page.supply_create.category_wound_care',
                                        )}
                                    </option>
                                    <option value="Consumables">
                                        {t(
                                            'page.supply_create.category_consumables',
                                        )}
                                    </option>
                                </Select>
                            </Field>

                            <Field
                                label={t('page.supply_create.unit_of_measure')}
                                htmlFor="supply-unit"
                                error={errors.unit_of_measure}
                            >
                                <Input
                                    id="supply-unit"
                                    icon={ScaleIcon}
                                    value={data.unit_of_measure}
                                    invalid={Boolean(errors.unit_of_measure)}
                                    onChange={(event) =>
                                        setData(
                                            'unit_of_measure',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="e.g. Box of 50, Unit, Liter"
                                />
                            </Field>

                            <Field
                                label={t('page.supply_create.cost_per_unit')}
                                htmlFor="supply-cost"
                                error={errors.cost_per_unit}
                            >
                                <Input
                                    id="supply-cost"
                                    type="number"
                                    step="0.01"
                                    icon={CurrencyDollarIcon}
                                    value={data.cost_per_unit}
                                    invalid={Boolean(errors.cost_per_unit)}
                                    onChange={(event) =>
                                        setData(
                                            'cost_per_unit',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="0.00"
                                    className="tabular-nums"
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    <SectionCard
                        title={t('page.supply_create.stock_thresholds')}
                        icon={InboxStackIcon}
                    >
                        <FieldSet columns={2}>
                            <Field
                                label={
                                    isEdit
                                        ? t(
                                              'page.supply_create.current_stock_level',
                                          )
                                        : t(
                                              'page.supply_create.initial_stock_quantity',
                                          )
                                }
                                htmlFor="supply-current-stock"
                                error={errors.current_stock}
                            >
                                <Input
                                    id="supply-current-stock"
                                    type="number"
                                    icon={InboxStackIcon}
                                    value={data.current_stock}
                                    invalid={Boolean(errors.current_stock)}
                                    onChange={(event) =>
                                        setData(
                                            'current_stock',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="0"
                                    className="tabular-nums"
                                />
                                <StockLevelSummary
                                    currentStock={data.current_stock}
                                    minStockLevel={data.min_stock_level}
                                />
                            </Field>

                            <Field
                                label={t(
                                    'page.supply_create.low_stock_threshold',
                                )}
                                htmlFor="supply-min-stock"
                                error={errors.min_stock_level}
                                hint={t(
                                    'page.supply_create.alert_trigger_note',
                                )}
                            >
                                <Input
                                    id="supply-min-stock"
                                    type="number"
                                    icon={BellAlertIcon}
                                    value={data.min_stock_level}
                                    invalid={Boolean(errors.min_stock_level)}
                                    onChange={(event) =>
                                        setData(
                                            'min_stock_level',
                                            Number(event.target.value),
                                        )
                                    }
                                    placeholder="10"
                                    className="tabular-nums"
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    <SectionCard
                        title={t('page.supply_create.item_image')}
                        description={t('common.optional')}
                        icon={PhotoIcon}
                    >
                        <DrugImageUpload
                            preview={imagePreview}
                            uploadLabel={t('page.supply_create.upload_image')}
                            previewAlt={t('page.supply_create.item_image')}
                            removeLabel={t('page.supply_create.item_image')}
                            onSelect={(file) => {
                                setData('image', file);
                                setImagePreview(URL.createObjectURL(file));
                            }}
                            onClear={() => {
                                setImagePreview(null);
                                setData('image', null);
                            }}
                        />
                    </SectionCard>

                    <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <Button
                            variant="secondary"
                            href={route('supplies.index')}
                        >
                            {t('common.cancel')}
                        </Button>
                        <Button type="submit" loading={processing}>
                            {processing
                                ? isEdit
                                    ? `${t('page.supply_create.updating')}...`
                                    : `${t('page.supply_create.creating')}...`
                                : isEdit
                                  ? t('page.supply_create.update_inventory')
                                  : t('page.supply_create.register_supply')}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
