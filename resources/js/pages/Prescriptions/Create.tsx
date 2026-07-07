import {
    HomeIcon,
    ChevronRightIcon,
    UserIcon,
    BeakerIcon,
    DocumentTextIcon,
    PlusIcon,
    TrashIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XMarkIcon,
    ArrowPathIcon,
    CheckIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { Patient } from '@/types';

interface Medication {
    id: number;
    name: string;
    strength?: string;
    dosage_units?: string;
    unit_cost: number;
}

interface Props {
    patients: Pick<Patient, 'id' | 'first_name' | 'last_name' | 'patient_uid'>[];
    medications: Medication[];
}

interface PrescriptionItemForm {
    medication_id: string;
    dosage: string;
    frequency: string;
    duration: string;
    quantity: string;
    instructions: string;
}

export default function PrescriptionsCreate({ patients, medications }: Props) {
    const { t } = useTranslation();
    const [patientSearch] = useState('');

    const { data, setData, post, processing, errors } = useForm({
        patient_id: '',
        notes: '',
        items: [] as PrescriptionItemForm[],
    });

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const patientOptions = patients
        .filter(
            (p) =>
                p.first_name
                    .toLowerCase()
                    .includes(patientSearch.toLowerCase()) ||
                p.last_name
                    .toLowerCase()
                    .includes(patientSearch.toLowerCase()) ||
                p.patient_uid
                    .toLowerCase()
                    .includes(patientSearch.toLowerCase()),
        )
        .map((p) => ({
            value: String(p.id),
            label: `${p.last_name} ${p.first_name} — ${p.patient_uid}`,
        }));

    const addItem = () => {
        setData('items', [
            ...data.items,
            {
                medication_id: '',
                dosage: '',
                frequency: '',
                duration: '',
                quantity: '1',
                instructions: '',
            },
        ]);
    };

    const removeItem = (index: number) => {
        setData('items', data.items.filter((_, i) => i !== index));
    };

    const updateItem = (
        index: number,
        field: keyof PrescriptionItemForm,
        value: string,
    ) => {
        const updated = [...data.items];
        updated[index] = { ...updated[index], [field]: value };
        setData('items', updated);
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post(route('prescriptions.store'), {
            onSuccess: () => {
                setSuccessMessage(t('prescriptionCreatedSuccessfully'));
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    const getItemErr = (index: number, field: string) =>
        errors[`items.${index}.${field}` as keyof typeof errors];

    return (
        <AppLayout
            headerTitle={t('New Prescription')}
            headerSubtitle={t('Create a medical prescription for a patient')}
        >
            <Head title={t('New Prescription')} />

            <div className="mx-auto max-w-8xl space-y-6 px-4 sm:px-6 lg:px-8">
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
                            {t('clinical')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-blue-600">
                            {t('prescriptions')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {t('new')}
                        </li>
                    </ol>
                </nav>

                {/* Alerts */}
                {successMessage && (
                    <div className="flex items-start justify-between rounded-lg border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                        <div className="flex items-center gap-3">
                            <CheckCircleIcon className="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
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
                            <ExclamationTriangleIcon className="h-5 w-5 shrink-0 text-red-600 dark:text-red-400" />
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
                    <form onSubmit={handleSubmit} className="space-y-8 p-6">
                        {/* Section 1: Patient */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <UserIcon className="h-5 w-5" />
                                {t('patient')}
                            </h3>

                            <div className="space-y-1">
                                <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    {t('selectPatient')}{' '}
                                    <span className="text-red-500">*</span>
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <UserIcon className="h-5 w-5 text-gray-400" />
                                    </div>
                                    <select
                                        value={data.patient_id}
                                        onChange={(e) =>
                                            setData(
                                                'patient_id',
                                                e.target.value,
                                            )
                                        }
                                        className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.patient_id ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                    >
                                        <option value="">
                                            {t('searchPatientByNameOrUid')}
                                        </option>
                                        {patientOptions.map((opt) => (
                                            <option
                                                key={opt.value}
                                                value={opt.value}
                                            >
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                {errors.patient_id && (
                                    <p className="mt-1 text-xs text-red-500">
                                        {errors.patient_id}
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* Section 2: Medications */}
                        <div className="space-y-4">
                            <div className="flex items-center justify-between border-b border-gray-100 pb-2 dark:border-neutral-700">
                                <div className="flex items-center gap-2">
                                    <BeakerIcon className="h-5 w-5" />
                                    <h3 className="text-sm font-bold tracking-wide text-blue-600 uppercase">
                                        {t('medications')}
                                    </h3>
                                    <span className="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">
                                        {data.items.length}{' '}
                                        {t('items')}
                                    </span>
                                </div>
                                <button
                                    type="button"
                                    onClick={addItem}
                                    className="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700 transition-colors hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/30"
                                >
                                    <PlusIcon className="h-4 w-4" />
                                    {t('addMedication')}
                                </button>
                            </div>

                            {data.items.length === 0 ? (
                                <div className="rounded-lg border border-dashed border-gray-300 bg-gray-50 py-8 text-center dark:border-neutral-600 dark:bg-neutral-700/30">
                                    <BeakerIcon className="mx-auto mb-2 h-8 w-8 text-gray-400" />
                                    <p className="text-sm text-gray-500 dark:text-neutral-400">
                                        {t('noMedicationsAddedYet')}
                                    </p>
                                    {errors.items && (
                                        <p className="mt-2 text-xs font-bold text-red-500">
                                            {errors.items}
                                        </p>
                                    )}
                                </div>
                            ) : (
                                <div className="space-y-4">
                                    {data.items.map((item, index) => {
                                        const hasItemErrors = Object.keys(
                                            errors,
                                        ).some((k) =>
                                            k.startsWith(`items.${index}`),
                                        );

                                        return (
                                            <div
                                                key={index}
                                                className={`rounded-lg border bg-gray-50 p-5 transition-all dark:bg-neutral-700/30 ${hasItemErrors ? 'border-red-200 dark:border-red-500' : 'border-gray-200 dark:border-neutral-600'}`}
                                            >
                                                <div className="mb-4 flex items-center justify-between">
                                                    <h4 className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                        {t('medication')} #
                                                        {index + 1}
                                                    </h4>
                                                    {data.items.length >
                                                        0 && (
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                removeItem(
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
                                                            {t('medication')}{' '}
                                                            <span className="text-red-500">
                                                                *
                                                            </span>
                                                        </label>
                                                        <select
                                                            value={
                                                                item.medication_id
                                                            }
                                                            onChange={(e) =>
                                                                updateItem(
                                                                    index,
                                                                    'medication_id',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            className={`w-full rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${getItemErr(index, 'medication_id') ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                                        >
                                                            <option value="">
                                                                {t(
                                                                    'selectMedication',
                                                                )}
                                                            </option>
                                                            {medications.map(
                                                                (m) => (
                                                                    <option
                                                                        key={
                                                                            m.id
                                                                        }
                                                                        value={String(
                                                                            m.id,
                                                                        )}
                                                                    >
                                                                        {m.name}{' '}
                                                                        {m.strength ??
                                                                            ''}{' '}
                                                                        ({m.dosage_units ??
                                                                            ''}
                                                                        )
                                                                    </option>
                                                                ),
                                                            )}
                                                        </select>
                                                        {getItemErr(
                                                            index,
                                                            'medication_id',
                                                        ) && (
                                                            <p className="text-[10px] font-bold text-red-500 uppercase">
                                                                {getItemErr(
                                                                    index,
                                                                    'medication_id',
                                                                )}
                                                            </p>
                                                        )}
                                                    </div>

                                                    <div className="space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t('dosage')}{' '}
                                                            <span className="text-red-500">
                                                                *
                                                            </span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={item.dosage}
                                                            onChange={(e) =>
                                                                updateItem(
                                                                    index,
                                                                    'dosage',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder={t(
                                                                'e.g. 500mg, 10ml',
                                                            )}
                                                            className={`w-full rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${getItemErr(index, 'dosage') ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                                        />
                                                        {getItemErr(
                                                            index,
                                                            'dosage',
                                                        ) && (
                                                            <p className="text-[10px] font-bold text-red-500 uppercase">
                                                                {getItemErr(
                                                                    index,
                                                                    'dosage',
                                                                )}
                                                            </p>
                                                        )}
                                                    </div>

                                                    <div className="space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t('frequency')}{' '}
                                                            <span className="text-red-500">
                                                                *
                                                            </span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={
                                                                item.frequency
                                                            }
                                                            onChange={(e) =>
                                                                updateItem(
                                                                    index,
                                                                    'frequency',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder={t(
                                                                'e.g. 3x daily, every 8h',
                                                            )}
                                                            className={`w-full rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${getItemErr(index, 'frequency') ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                                        />
                                                        {getItemErr(
                                                            index,
                                                            'frequency',
                                                        ) && (
                                                            <p className="text-[10px] font-bold text-red-500 uppercase">
                                                                {getItemErr(
                                                                    index,
                                                                    'frequency',
                                                                )}
                                                            </p>
                                                        )}
                                                    </div>

                                                    <div className="space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t('duration')}{' '}
                                                            <span className="text-red-500">
                                                                *
                                                            </span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={
                                                                item.duration
                                                            }
                                                            onChange={(e) =>
                                                                updateItem(
                                                                    index,
                                                                    'duration',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder={t(
                                                                'e.g. 7 days, 2 weeks',
                                                            )}
                                                            className={`w-full rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${getItemErr(index, 'duration') ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                                        />
                                                        {getItemErr(
                                                            index,
                                                            'duration',
                                                        ) && (
                                                            <p className="text-[10px] font-bold text-red-500 uppercase">
                                                                {getItemErr(
                                                                    index,
                                                                    'duration',
                                                                )}
                                                            </p>
                                                        )}
                                                    </div>

                                                    <div className="space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t('quantity')}{' '}
                                                            <span className="text-red-500">
                                                                *
                                                            </span>
                                                        </label>
                                                        <input
                                                            type="number"
                                                            min="1"
                                                            value={
                                                                item.quantity
                                                            }
                                                            onChange={(e) =>
                                                                updateItem(
                                                                    index,
                                                                    'quantity',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            className={`w-full rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${getItemErr(index, 'quantity') ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                                        />
                                                        {getItemErr(
                                                            index,
                                                            'quantity',
                                                        ) && (
                                                            <p className="text-[10px] font-bold text-red-500 uppercase">
                                                                {getItemErr(
                                                                    index,
                                                                    'quantity',
                                                                )}
                                                            </p>
                                                        )}
                                                    </div>

                                                    <div className="space-y-1">
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                            {t('instructions')}
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={
                                                                item.instructions
                                                            }
                                                            onChange={(e) =>
                                                                updateItem(
                                                                    index,
                                                                    'instructions',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder={t(
                                                                'e.g. Take after meals',
                                                            )}
                                                            className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    })}

                                    <button
                                        type="button"
                                        onClick={addItem}
                                        className="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700 transition-colors hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/30"
                                    >
                                        <PlusIcon className="h-4 w-4" />
                                        {t('addAnotherMedication')}
                                    </button>
                                </div>
                            )}
                        </div>

                        {/* Section 3: Notes */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <DocumentTextIcon className="h-5 w-5" />
                                {t('notes')}
                            </h3>

                            <div className="space-y-1">
                                <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    {t('prescriptionNotes')}
                                </label>
                                <textarea
                                    value={data.notes}
                                    onChange={(e) =>
                                        setData('notes', e.target.value)
                                    }
                                    placeholder={t('prescriptionNotes')}
                                    rows={3}
                                    className={`w-full resize-none rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.notes ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                />
                                {errors.notes && (
                                    <p className="mt-1 text-xs text-red-500">
                                        {errors.notes}
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* Form Actions */}
                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href={route('prescriptions.index')}
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
                                        {t('saving')}...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" />
                                        {t('createPrescription')}
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
