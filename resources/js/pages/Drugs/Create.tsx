import {
    HomeIcon,
    ChevronRightIcon,
    CubeIcon,
    DocumentTextIcon,
    AdjustmentsHorizontalIcon,
    ArchiveBoxArrowDownIcon,
    RectangleStackIcon,
    PlusIcon,
    TrashIcon,
    TagIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    ExclamationCircleIcon,
    XMarkIcon,
    ArrowPathIcon,
    CheckIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useState } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Batch {
    id: string;
    batch_number: string;
    expiry_date: string;
    quantity: string;
    purchase_price: string;
    sale_price: string;
    manufactured_date: string;
    notes: string;
}

interface DrugForm {
    name: string;
    dosage_unit: string;
    min_stock_level: string;
    description: string;
    batches: Batch[];
}

export default function CreateDrug() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } = useForm<
        DrugForm & { image: File | null }
    >({
        name: '',
        dosage_unit: '',
        min_stock_level: '',
        description: '',
        batches: [],
        image: null as File | null,
    });

    const [imagePreview, setImagePreview] = useState<string | null>(null);

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const addBatch = () => {
        const newBatch: Batch = {
            id: `batch-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
            batch_number: '',
            expiry_date: '',
            quantity: '',
            purchase_price: '',
            sale_price: '',
            manufactured_date: '',
            notes: '',
        };
        setData('batches', [...data.batches, newBatch]);
    };

    const removeBatch = (index: number) => {
        const newBatches = data.batches.filter((_, i) => i !== index);
        setData('batches', newBatches);
    };

    const updateBatch = (index: number, field: keyof Batch, value: string) => {
        const newBatches = [...data.batches];
        newBatches[index] = { ...newBatches[index], [field]: value };
        setData('batches', newBatches);
    };

    const submit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post(route('drugs.store'), {
            forceFormData: true,
            onSuccess: () => {
                setSuccessMessage(t('drugCreatedSuccessfully'));
                reset();
                setImagePreview(null);
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('addNewDrug')}
            headerSubtitle={t('createNewMedicationEntry')}
        >
            <Head title={t('addNewDrug')} />

            <div className="max-w-8xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" /> {t('home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-blue-600">
                            {t('manageDrugs')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {t('addNewDrug')}
                        </li>
                    </ol>
                </nav>

                {/* Alerts */}
                {successMessage && (
                    <div className="flex items-start justify-between rounded-lg border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                        <div className="flex items-center gap-3">
                            <CheckCircleIcon className="h-5 w-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" />
                            <div>
                                <h3 className="text-sm font-bold text-emerald-800 dark:text-emerald-300">
                                    {t('success')}
                                </h3>
                                <div className="mt-1 text-sm text-emerald-700 dark:text-emerald-400">
                                    {successMessage}
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setSuccessMessage(null)}
                            className="rounded-lg p-1.5 text-emerald-500 transition hover:text-emerald-700 dark:hover:text-emerald-300"
                        >
                            <XMarkIcon className="h-5 w-5" />
                        </button>
                    </div>
                )}

                {(errorMessage || Object.keys(errors).length > 0) && (
                    <div className="flex items-start justify-between rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                        <div className="flex items-center gap-3">
                            <ExclamationTriangleIcon className="h-5 w-5 flex-shrink-0 text-red-600 dark:text-red-400" />
                            <div>
                                <h3 className="text-sm font-bold text-red-800 dark:text-red-300">
                                    {t('error')}
                                </h3>
                                <div className="mt-1 text-sm text-red-700 dark:text-red-400">
                                    {errorMessage ||
                                        t('someFieldsRequireAttention')}
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setErrorMessage(null)}
                            className="rounded-lg p-1.5 text-red-500 transition hover:text-red-700 dark:hover:text-red-300"
                        >
                            <XMarkIcon className="h-5 w-5" />
                        </button>
                    </div>
                )}

                {/* Main Form Card */}
                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <form onSubmit={submit} className="space-y-8 p-6">
                        {/* Section 1: Medication Details */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <CubeIcon className="h-5 w-5" />
                                {t('medicationDetails')}
                            </h3>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="col-span-2 space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('drugName')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <DocumentTextIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData('name', e.target.value)
                                            }
                                            placeholder={t('egParacetamol')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.name ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.name && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('dosageUnit')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <AdjustmentsHorizontalIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="text"
                                            value={data.dosage_unit}
                                            onChange={(e) =>
                                                setData(
                                                    'dosage_unit',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t('egMgTabletsMl')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.dosage_unit ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.dosage_unit && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.dosage_unit}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('minimumStockLevel')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <ArchiveBoxArrowDownIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            min="0"
                                            value={data.min_stock_level}
                                            onChange={(e) =>
                                                setData(
                                                    'min_stock_level',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t('egFifty')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.min_stock_level ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.min_stock_level && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.min_stock_level}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Section 2: Batch Information */}
                        <div className="space-y-4">
                            <div className="flex items-center justify-between border-b border-gray-100 pb-2 dark:border-neutral-700">
                                <div className="flex items-center gap-2">
                                    <RectangleStackIcon className="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                                    <h3 className="text-sm font-bold tracking-wide text-blue-600 uppercase">
                                        {t('batchInformation')}
                                    </h3>
                                    <span className="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">
                                        {data.batches.length} {t('batches')}
                                    </span>
                                </div>
                                <button
                                    type="button"
                                    onClick={addBatch}
                                    className="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700 transition-colors hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/30"
                                >
                                    <PlusIcon className="h-4 w-4" />
                                    {t('addBatch')}
                                </button>
                            </div>

                            {data.batches.length === 0 ? (
                                <div className="rounded-lg border border-dashed border-gray-300 bg-gray-50 py-8 text-center dark:border-neutral-600 dark:bg-neutral-700/30">
                                    <RectangleStackIcon className="mx-auto mb-2 h-8 w-8 text-gray-400" />
                                    <p className="text-sm text-gray-500 dark:text-neutral-400">
                                        {t('noBatchesAdded')}
                                    </p>
                                    {errors.batches && (
                                        <p className="mt-2 text-xs font-bold text-red-500">
                                            {errors.batches}
                                        </p>
                                    )}
                                </div>
                            ) : (
                                <div className="space-y-4">
                                    {data.batches.map((batch, index) => {
                                        const getErr = (field: string) =>
                                            errors[
                                                `batches.${index}.${field}` as keyof typeof errors
                                            ];

                                        return (
                                            <div
                                                key={batch.id}
                                                className={`rounded-lg border bg-gray-50 p-5 transition-all dark:bg-neutral-700/30 ${Object.keys(errors).some((k) => k.startsWith(`batches.${index}`)) ? 'border-red-200 dark:border-red-500' : 'border-gray-200 dark:border-neutral-600'}`}
                                            >
                                                <div className="mb-4 flex items-center justify-between">
                                                    <h4 className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                        {t('batch')} #
                                                        {index + 1}
                                                    </h4>
                                                    {data.batches.length >
                                                        0 && (
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                removeBatch(
                                                                    index,
                                                                )
                                                            }
                                                            className="rounded p-1 text-red-600 transition-colors hover:bg-red-50 hover:text-red-800 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300"
                                                        >
                                                            <TrashIcon className="h-4 w-4" />
                                                        </button>
                                                    )}
                                                </div>

                                                <div className="grid grid-cols-2 gap-4">
                                                    <div className="col-span-2 space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t('batchNumber')}{' '}
                                                            <span className="text-red-500">
                                                                *
                                                            </span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={
                                                                batch.batch_number
                                                            }
                                                            onChange={(e) =>
                                                                updateBatch(
                                                                    index,
                                                                    'batch_number',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder={t(
                                                                'egBatchNumber',
                                                            )}
                                                            className={`w-full rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${getErr('batch_number') ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                                        />
                                                        {getErr(
                                                            'batch_number',
                                                        ) && (
                                                            <p className="text-[10px] font-bold text-red-500 uppercase">
                                                                {getErr(
                                                                    'batch_number',
                                                                )}
                                                            </p>
                                                        )}
                                                    </div>

                                                    <div className="space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t('expiryDate')}{' '}
                                                            <span className="text-red-500">
                                                                *
                                                            </span>
                                                        </label>
                                                        <input
                                                            type="date"
                                                            value={
                                                                batch.expiry_date
                                                            }
                                                            onChange={(e) =>
                                                                updateBatch(
                                                                    index,
                                                                    'expiry_date',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            className={`w-full rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${getErr('expiry_date') ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                                        />
                                                        {getErr(
                                                            'expiry_date',
                                                        ) && (
                                                            <p className="text-[10px] font-bold text-red-500 uppercase">
                                                                {getErr(
                                                                    'expiry_date',
                                                                )}
                                                            </p>
                                                        )}
                                                    </div>

                                                    <div className="space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t(
                                                                'initialQuantity',
                                                            )}{' '}
                                                            <span className="text-red-500">
                                                                *
                                                            </span>
                                                        </label>
                                                        <input
                                                            type="number"
                                                            min="1"
                                                            value={
                                                                batch.quantity
                                                            }
                                                            onChange={(e) =>
                                                                updateBatch(
                                                                    index,
                                                                    'quantity',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder={t(
                                                                'egOneHundred',
                                                            )}
                                                            className={`w-full rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${getErr('quantity') ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                                        />
                                                        {getErr('quantity') && (
                                                            <p className="text-[10px] font-bold text-red-500 uppercase">
                                                                {getErr(
                                                                    'quantity',
                                                                )}
                                                            </p>
                                                        )}
                                                    </div>

                                                    <div className="space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t('purchasePrice')}{' '}
                                                            <span className="text-red-500">
                                                                *
                                                            </span>
                                                        </label>
                                                        <div className="relative">
                                                            <span className="absolute top-1/2 left-3 -translate-y-1/2 text-sm font-medium text-gray-400">
                                                                $
                                                            </span>
                                                            <input
                                                                type="number"
                                                                step="0.01"
                                                                value={
                                                                    batch.purchase_price
                                                                }
                                                                onChange={(e) =>
                                                                    updateBatch(
                                                                        index,
                                                                        'purchase_price',
                                                                        e.target
                                                                            .value,
                                                                    )
                                                                }
                                                                placeholder={t(
                                                                    'zeroPointZeroZero',
                                                                )}
                                                                className={`w-full rounded-lg border py-2 pr-3 pl-7 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${getErr('purchase_price') ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                                            />
                                                        </div>
                                                        {getErr(
                                                            'purchase_price',
                                                        ) && (
                                                            <p className="text-[10px] font-bold text-red-500 uppercase">
                                                                {getErr(
                                                                    'purchase_price',
                                                                )}
                                                            </p>
                                                        )}
                                                    </div>

                                                    <div className="space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t('salePrice')}{' '}
                                                            <span className="ml-1 text-xs font-normal text-gray-400">
                                                                {t('optional')}
                                                            </span>
                                                        </label>
                                                        <div className="relative">
                                                            <span className="absolute top-1/2 left-3 -translate-y-1/2 text-sm font-medium text-gray-400">
                                                                $
                                                            </span>
                                                            <input
                                                                type="number"
                                                                step="0.01"
                                                                value={
                                                                    batch.sale_price
                                                                }
                                                                onChange={(e) =>
                                                                    updateBatch(
                                                                        index,
                                                                        'sale_price',
                                                                        e.target
                                                                            .value,
                                                                    )
                                                                }
                                                                placeholder={t(
                                                                    'zeroPointZeroZero',
                                                                )}
                                                                className="w-full rounded-lg border border-gray-300 py-2 pr-3 pl-7 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                                            />
                                                        </div>
                                                    </div>

                                                    <div className="space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t(
                                                                'manufacturedDate',
                                                            )}
                                                        </label>
                                                        <input
                                                            type="date"
                                                            value={
                                                                batch.manufactured_date
                                                            }
                                                            onChange={(e) =>
                                                                updateBatch(
                                                                    index,
                                                                    'manufactured_date',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                                        />
                                                    </div>

                                                    <div className="col-span-2 space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t('batchNotes')}{' '}
                                                            <span className="ml-1 text-xs font-normal text-gray-400">
                                                                {t('optional')}
                                                            </span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={batch.notes}
                                                            onChange={(e) =>
                                                                updateBatch(
                                                                    index,
                                                                    'notes',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder={t(
                                                                'anyNotesAboutBatch',
                                                            )}
                                                            className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            )}
                        </div>

                        {/* Section 3: Description */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <TagIcon className="h-5 w-5" />
                                {t('description')}{' '}
                                <span className="ml-1 text-xs font-normal text-gray-400">
                                    {t('optional')}
                                </span>
                            </h3>

                            <div className="space-y-1">
                                <textarea
                                    value={data.description}
                                    onChange={(e) =>
                                        setData('description', e.target.value)
                                    }
                                    placeholder={t('provideAdditionalDetails')}
                                    rows={3}
                                    className={`w-full resize-none rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.description ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                />
                                {errors.description && (
                                    <p className="mt-1 text-xs text-red-500">
                                        {errors.description}
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* Section 4: Drug Image */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <TagIcon className="h-5 w-5" />
                                {t('drugImage')}{' '}
                                <span className="ml-1 text-xs font-normal text-gray-400">
                                    {t('optional')}
                                </span>
                            </h3>
                            <div className="flex items-center gap-6">
                                {imagePreview ? (
                                    <div className="relative">
                                        <img
                                            src={imagePreview}
                                            alt={t('preview')}
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
                                <label className="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-600 transition-colors hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30">
                                    <span>{t('uploadImage')}</span>
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
                            </div>
                        </div>

                        {/* Form Actions */}
                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href={route('drugs.index')}
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('cancel')}
                            </Link>

                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                        {t('creating')}...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" />
                                        {t('createDrug')}
                                    </span>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
