import {
    MagnifyingGlassIcon,
    UserGroupIcon,
    ShoppingBagIcon,
    CalendarIcon,
    UserCircleIcon,
    ArrowPathIcon,
    PlusIcon,
    TrashIcon,
    MinusIcon,
    BeakerIcon,
    DocumentTextIcon,
    XCircleIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import React, { useState, useMemo, useEffect } from 'react';
import { toast, Toaster } from 'sonner';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormTextarea from '@/Components/FormTextarea';
import Modal from '@/Components/Modal';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Props {
    patients: any[];
    medications: any[];
}

export default function DispenseMedications({ patients, medications }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [viewMode, setViewMode] = useState<'prescription' | 'walkin'>(
        'prescription',
    );
    const [selectedPatient, setSelectedPatient] = useState<any>(null);
    const [isModalOpen, setIsModalOpen] = useState(false);

    const { data, setData, post, processing, reset, errors } = useForm({
        patient_id: null as number | null,
        prescription_id: null as number | null,
        is_walk_in: false,
        items: [] as any[],
        notes: '',
    });

    // Clear search when switching modes
    useEffect(() => {
        // eslint-disable-next-line react-hooks/set-state-in-effect
        setSearch('');
    }, [viewMode]);

    // --- Logic for Prescription Flow ---
    const handleStartPrescriptionDispense = (
        patient: any,
        prescription: any,
    ) => {
        setData({
            patient_id: patient.id,
            prescription_id: prescription.id,
            is_walk_in: false,
            notes: '',
            items: prescription.items.map((i: any) => ({
                medication_id: i.medication_id,
                name: i.medication.name,
                quantity: i.quantity,
                max: i.quantity,
                stock: i.medication.total_stock,
                price: i.medication.unit_cost,
            })),
        });
        setIsModalOpen(true);
    };

    // --- Logic for Walk-in Flow ---
    const startWalkIn = () => {
        setViewMode('walkin');
        setSelectedPatient(null);
        setData({
            patient_id: null,
            prescription_id: null,
            is_walk_in: true,
            notes: '',
            items: [],
        });
    };

    const startPrescription = () => {
        setViewMode('prescription');
        setData('items', []);
    };

    const addWalkInItem = (med: any) => {
        if (data.items.find((i) => i.medication_id === med.id)) {
            return;
        }

        setData('items', [
            ...data.items,
            {
                medication_id: med.id,
                name: med.name,
                quantity: 1,
                max: med.total_stock,
                stock: med.total_stock,
                price: med.unit_cost,
            },
        ]);
    };

    const removeItem = (idx: number) => {
        setData(
            'items',
            data.items.filter((_, i) => i !== idx),
        );
    };

    const updateQty = (idx: number, qty: number) => {
        if (isNaN(qty)) {
            return;
        }

        const newItems = [...data.items];
        newItems[idx].quantity = Math.min(Math.max(1, qty), newItems[idx].max);
        setData('items', newItems);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('dispensation.store'), {
            onSuccess: () => {
                setIsModalOpen(false);
                reset();
                toast.success(t('medicationDispensedSuccessfully'));
            },
            onError: (errors) => {
                const firstError = Object.values(errors).flat()[0];
                toast.error(firstError || t('failedToDispense'));
            },
        });
    };

    // Filtered Lists
    const filteredPatients = useMemo(
        () =>
            patients.filter((p) =>
                `${p.first_name} ${p.last_name}`
                    .toLowerCase()
                    .includes(search.toLowerCase()),
            ),
        [patients, search],
    );

    const filteredMedications = useMemo(
        () =>
            medications.filter((m) =>
                m.name.toLowerCase().includes(search.toLowerCase()),
            ),
        [medications, search],
    );

    // Calculations
    const cartTotal = data.items.reduce(
        (acc, item) => acc + item.price * item.quantity,
        0,
    );

    return (
        <AppLayout
            headerTitle={t('pharmacyDispensing')}
            headerSubtitle={t('agnosticDispensing')}
        >
            <Head title={t('dispensing')} />
            <Toaster position="top-right" richColors />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* Agnostic Header Controls */}
                <div className="flex flex-col items-start justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-2 shadow-sm sm:flex-row sm:items-center dark:border-neutral-700 dark:bg-neutral-800">
                    <div className="flex w-full rounded-xl bg-gray-100/80 p-1 sm:w-fit">
                        <button
                            onClick={startPrescription}
                            className={`flex flex-1 items-center justify-center gap-2 rounded-lg px-6 py-2.5 text-sm font-semibold transition-all duration-200 sm:flex-none ${viewMode === 'prescription' ? 'bg-white text-blue-600 shadow-sm ring-1 ring-gray-200/50' : 'text-gray-500 hover:text-gray-700 dark:text-neutral-300 dark:text-neutral-500'}`}
                        >
                            <UserGroupIcon className="h-5 w-5" />{' '}
                            {t('prescriptions')}
                        </button>
                        <button
                            onClick={startWalkIn}
                            className={`flex flex-1 items-center justify-center gap-2 rounded-lg px-6 py-2.5 text-sm font-semibold transition-all duration-200 sm:flex-none ${viewMode === 'walkin' ? 'bg-white text-emerald-600 shadow-sm ring-1 ring-gray-200/50' : 'text-gray-500 hover:text-gray-700 dark:text-neutral-300 dark:text-neutral-500'}`}
                        >
                            <ShoppingBagIcon className="h-5 w-5" />{' '}
                            {t('walkIn')}
                        </button>
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    {/* SIDEBAR: Search & Selection */}
                    <aside className="flex h-[500px] flex-col rounded-2xl border border-gray-200 bg-white shadow-sm lg:col-span-4 lg:h-[650px] xl:col-span-3 dark:border-neutral-700 dark:bg-neutral-800">
                        <div className="space-y-3 rounded-t-2xl border-b border-gray-100 bg-gray-50/30 p-4 dark:border-neutral-700 dark:bg-neutral-700/30">
                            <h3 className="text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-neutral-500">
                                {viewMode === 'prescription'
                                    ? t('selectPatientNumber')
                                    : t('searchInventoryNumber')}
                            </h3>
                            <FormInput
                                name="search"
                                icon={MagnifyingGlassIcon}
                                placeholder={
                                    viewMode === 'prescription'
                                        ? t('searchPatientName')
                                        : t('searchMedication')
                                }
                                value={search}
                                onChange={(value) => setSearch(value)}
                            />
                        </div>

                        <div className="custom-scrollbar flex-grow space-y-1 overflow-y-auto p-3">
                            {viewMode === 'prescription' ? (
                                filteredPatients.length > 0 ? (
                                    filteredPatients.map((p) => (
                                        <button
                                            key={p.id}
                                            onClick={() =>
                                                setSelectedPatient(p)
                                            }
                                            className={`w-full rounded-xl p-3 text-left transition-all duration-200 ${selectedPatient?.id === p.id ? 'bg-blue-50 ring-1 ring-blue-200' : 'hover:bg-gray-50 dark:hover:bg-neutral-700'}`}
                                        >
                                            <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100">
                                                {p.first_name} {p.last_name}
                                            </p>
                                            <p className="mt-0.5 font-mono text-xs text-gray-500 dark:text-neutral-500">
                                                {t('id')}: {p.patient_uid}
                                            </p>
                                        </button>
                                    ))
                                ) : (
                                    <div className="p-6 text-center text-sm text-gray-400 dark:text-neutral-500">
                                        {t('noPatientsFound')}
                                    </div>
                                )
                            ) : filteredMedications.length > 0 ? (
                                filteredMedications.map((m) => (
                                    <button
                                        key={m.id}
                                        onClick={() => addWalkInItem(m)}
                                        className="group flex w-full items-center justify-between rounded-xl p-3 text-left transition-all duration-200 hover:bg-emerald-50 hover:ring-1 hover:ring-emerald-100"
                                    >
                                        <div>
                                            <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100">
                                                {m.name}
                                            </p>
                                            <p className="mt-0.5 text-xs text-gray-500 dark:text-neutral-500">
                                                {t('stock')}:{' '}
                                                <span className="font-medium text-gray-700 dark:text-neutral-300">
                                                    {m.total_stock}
                                                </span>{' '}
                                                {m.dosage_units}
                                            </p>
                                        </div>
                                        <div className="rounded-lg bg-emerald-100 p-1.5 opacity-0 transition-opacity group-hover:opacity-100">
                                            <PlusIcon className="h-4 w-4 text-emerald-600" />
                                        </div>
                                    </button>
                                ))
                            ) : (
                                <div className="p-6 text-center text-sm text-gray-400 dark:text-neutral-500">
                                    {t('noMedicationsFound')}
                                </div>
                            )}
                        </div>
                    </aside>

                    {/* MAIN STAGE */}
                    <main className="lg:col-span-8 xl:col-span-9">
                        <AnimatePresence mode="wait">
                            {viewMode === 'prescription' && selectedPatient ? (
                                <motion.div
                                    key="prescription-view"
                                    initial={{ opacity: 0, y: 10 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -10 }}
                                    className="space-y-4"
                                >
                                    {/* Patient Profile Header */}
                                    <div className="flex flex-col items-start justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:p-6 dark:border-neutral-700 dark:bg-neutral-800">
                                        <div className="flex items-center gap-4">
                                            <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-xl font-bold text-white shadow-lg shadow-blue-200/50">
                                                {selectedPatient.first_name[0]}
                                                {selectedPatient.last_name[0]}
                                            </div>
                                            <div>
                                                <h2 className="text-xl font-bold tracking-tight text-gray-900 dark:text-neutral-100">
                                                    {selectedPatient.first_name}{' '}
                                                    {selectedPatient.last_name}
                                                </h2>
                                                <div className="mt-1 flex items-center gap-2">
                                                    <span className="rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500 dark:text-neutral-500">
                                                        {t('id')}:{' '}
                                                        {
                                                            selectedPatient.patient_uid
                                                        }
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">
                                            <DocumentTextIcon className="h-4 w-4" />
                                            {
                                                selectedPatient.prescriptions
                                                    .length
                                            }{' '}
                                            {t('activePrescriptions')}
                                        </div>
                                    </div>

                                    {/* Prescriptions List */}
                                    <div className="grid gap-4 xl:grid-cols-2">
                                        {selectedPatient.prescriptions.map(
                                            (pr: any) => (
                                                <div
                                                    key={pr.id}
                                                    className="flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-colors hover:border-blue-300 dark:border-neutral-700 dark:bg-neutral-800"
                                                >
                                                    <div className="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 p-4 dark:border-neutral-700 dark:bg-neutral-700/50">
                                                        <div className="flex items-center gap-3">
                                                            <div className="rounded-lg border border-gray-100 bg-white p-2 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                                                <CalendarIcon className="h-5 w-5 text-blue-500" />
                                                            </div>
                                                            <div>
                                                                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                                    {new Date(
                                                                        pr.prescription_date,
                                                                    ).toLocaleDateString()}
                                                                </p>
                                                                <p className="text-xs font-medium text-gray-500 dark:text-neutral-500">
                                                                    {t('dr')}{' '}
                                                                    {
                                                                        pr
                                                                            .doctor
                                                                            ?.name
                                                                    }
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="flex-grow space-y-3 p-4">
                                                        {pr.items.map(
                                                            (item: any) => (
                                                                <div
                                                                    key={
                                                                        item.id
                                                                    }
                                                                    className="flex items-center justify-between border-b border-gray-50 pb-2 text-sm last:border-0 last:pb-0"
                                                                >
                                                                    <div className="flex items-center gap-2">
                                                                        <BeakerIcon className="h-4 w-4 text-gray-400 dark:text-neutral-500" />
                                                                        <span className="font-medium text-gray-700 dark:text-neutral-300">
                                                                            {
                                                                                item
                                                                                    .medication
                                                                                    .name
                                                                            }
                                                                        </span>
                                                                    </div>
                                                                    <div className="flex flex-col items-end">
                                                                        <span className="font-bold text-gray-900 dark:text-neutral-100">
                                                                            x
                                                                            {
                                                                                item.quantity
                                                                            }
                                                                        </span>
                                                                        <span className="text-[10px] text-gray-400 dark:text-neutral-500">
                                                                            {
                                                                                item.dosage
                                                                            }
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            ),
                                                        )}
                                                    </div>
                                                    <div className="border-t border-gray-100 bg-gray-50/50 p-4 dark:border-neutral-700 dark:bg-neutral-700/50">
                                                        <button
                                                            onClick={() =>
                                                                handleStartPrescriptionDispense(
                                                                    selectedPatient,
                                                                    pr,
                                                                )
                                                            }
                                                            className="w-full rounded-xl bg-blue-600 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                                        >
                                                            {t(
                                                                'dispensePrescription',
                                                            )}
                                                        </button>
                                                    </div>
                                                </div>
                                            ),
                                        )}
                                        {selectedPatient.prescriptions
                                            .length === 0 && (
                                            <div className="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center xl:col-span-2 dark:border-neutral-700 dark:bg-neutral-700">
                                                <p className="text-sm text-gray-500 dark:text-neutral-500">
                                                    {t('noActivePrescriptions')}
                                                </p>
                                            </div>
                                        )}
                                    </div>
                                </motion.div>
                            ) : viewMode === 'walkin' ? (
                                <motion.div
                                    key="walkin-view"
                                    initial={{ opacity: 0, y: 10 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -10 }}
                                    className="flex h-[500px] flex-col rounded-2xl border border-gray-200 bg-white shadow-sm lg:h-[650px] dark:border-neutral-700 dark:bg-neutral-800"
                                >
                                    <div className="flex items-center justify-between rounded-t-2xl border-b border-gray-100 bg-gray-50/30 p-4 dark:border-neutral-700 dark:bg-neutral-700/30">
                                        <h3 className="text-sm font-bold tracking-wide text-gray-900 uppercase dark:text-neutral-100">
                                            {t('walkInCart')}
                                        </h3>
                                        {data.items.length > 0 && (
                                            <span className="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                                {data.items.length} {t('items')}
                                            </span>
                                        )}
                                    </div>

                                    <div className="custom-scrollbar flex-grow overflow-y-auto p-2 sm:p-4">
                                        {data.items.length > 0 ? (
                                            <div className="space-y-3">
                                                <AnimatePresence>
                                                    {data.items.map(
                                                        (item, idx) => (
                                                            <motion.div
                                                                key={
                                                                    item.medication_id
                                                                }
                                                                initial={{
                                                                    opacity: 0,
                                                                    scale: 0.95,
                                                                }}
                                                                animate={{
                                                                    opacity: 1,
                                                                    scale: 1,
                                                                }}
                                                                exit={{
                                                                    opacity: 0,
                                                                    scale: 0.95,
                                                                }}
                                                                className="flex flex-col justify-between gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-colors hover:border-emerald-200 sm:flex-row sm:items-center dark:border-neutral-700 dark:bg-neutral-800"
                                                            >
                                                                <div className="flex-1">
                                                                    <p className="text-base font-bold text-gray-900 dark:text-neutral-100">
                                                                        {
                                                                            item.name
                                                                        }
                                                                    </p>
                                                                    <p className="mt-1 text-xs font-medium text-gray-500 dark:text-neutral-500">
                                                                        {t(
                                                                            'currencySymbol',
                                                                        )}
                                                                        {parseFloat(
                                                                            item.price,
                                                                        ).toFixed(
                                                                            2,
                                                                        )}{' '}
                                                                        {t(
                                                                            'perUnit',
                                                                        )}
                                                                    </p>
                                                                </div>

                                                                <div className="flex w-full items-center justify-between gap-6 sm:w-auto sm:justify-end">
                                                                    <div className="flex items-center rounded-xl border border-gray-200 bg-gray-50 p-1 dark:border-neutral-700 dark:bg-neutral-700">
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                updateQty(
                                                                                    idx,
                                                                                    item.quantity -
                                                                                        1,
                                                                                )
                                                                            }
                                                                            className="rounded-lg p-1.5 text-gray-600 transition-all hover:bg-white hover:shadow-sm dark:text-neutral-400"
                                                                        >
                                                                            <MinusIcon className="h-4 w-4" />
                                                                        </button>
                                                                        <input
                                                                            type="number"
                                                                            value={
                                                                                item.quantity
                                                                            }
                                                                            onChange={(
                                                                                e,
                                                                            ) =>
                                                                                updateQty(
                                                                                    idx,
                                                                                    parseInt(
                                                                                        e
                                                                                            .target
                                                                                            .value,
                                                                                    ),
                                                                                )
                                                                            }
                                                                            className="w-12 border-none bg-transparent p-0 text-center text-sm font-bold focus:ring-0"
                                                                        />
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                updateQty(
                                                                                    idx,
                                                                                    item.quantity +
                                                                                        1,
                                                                                )
                                                                            }
                                                                            className="rounded-lg p-1.5 text-gray-600 transition-all hover:bg-white hover:shadow-sm dark:text-neutral-400"
                                                                        >
                                                                            <PlusIcon className="h-4 w-4" />
                                                                        </button>
                                                                    </div>

                                                                    <div className="w-16 text-right">
                                                                        <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                                            {t(
                                                                                'currencySymbol',
                                                                            )}
                                                                            {(
                                                                                item.price *
                                                                                item.quantity
                                                                            ).toFixed(
                                                                                2,
                                                                            )}
                                                                        </p>
                                                                    </div>

                                                                    <button
                                                                        onClick={() =>
                                                                            removeItem(
                                                                                idx,
                                                                            )
                                                                        }
                                                                        className="rounded-xl p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-500 dark:text-neutral-500 dark:hover:bg-red-900/20"
                                                                    >
                                                                        <TrashIcon className="h-5 w-5" />
                                                                    </button>
                                                                </div>
                                                            </motion.div>
                                                        ),
                                                    )}
                                                </AnimatePresence>
                                            </div>
                                        ) : (
                                            <div className="flex h-full items-center justify-center">
                                                <EmptyState
                                                    icon={ShoppingBagIcon}
                                                    title={t('yourCartIsEmpty')}
                                                    description={t(
                                                        'selectMedicationsFromInventory',
                                                    )}
                                                />
                                            </div>
                                        )}
                                    </div>

                                    <div className="space-y-4 rounded-b-2xl border-t border-gray-100 bg-gray-50/50 p-4 sm:p-5 dark:border-neutral-700 dark:bg-neutral-700/50">
                                        {data.items.length > 0 && (
                                            <div className="flex items-center justify-between px-2">
                                                <span className="text-sm font-semibold text-gray-600 dark:text-neutral-400">
                                                    {t('totalAmount')}:
                                                </span>
                                                <span className="text-2xl font-black text-gray-900 dark:text-neutral-100">
                                                    {t('currencySymbol')}
                                                    {cartTotal.toFixed(2)}
                                                </span>
                                            </div>
                                        )}
                                        <FormTextarea
                                            name="notes"
                                            placeholder={t('addInternalNotes')}
                                            value={data.notes}
                                            onChange={(value) =>
                                                setData('notes', value)
                                            }
                                            rows={2}
                                        />
                                        <button
                                            onClick={() => setIsModalOpen(true)}
                                            disabled={data.items.length === 0}
                                            className="w-full rounded-xl bg-emerald-600 py-3.5 text-sm font-bold tracking-wide text-white uppercase shadow-md shadow-emerald-200 transition-all hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:shadow-none"
                                        >
                                            {t('reviewAndCheckout')}
                                        </button>
                                    </div>
                                </motion.div>
                            ) : (
                                <motion.div
                                    key="empty-state"
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    className="flex h-[500px] items-center justify-center lg:h-[650px]"
                                >
                                    <EmptyState
                                        icon={UserCircleIcon}
                                        title={t('noPatientSelected')}
                                        description={t(
                                            'searchAndSelectPatientDispensing',
                                        )}
                                    />
                                </motion.div>
                            )}
                        </AnimatePresence>
                    </main>
                </div>
            </div>

            {/* Confirmation Modal */}
            <Modal
                isOpen={isModalOpen}
                onClose={() => setIsModalOpen(false)}
                title={t('confirmDispensation')}
                maxWidth="max-w-lg"
            >
                <form onSubmit={submit}>
                    <p className="mb-4 text-sm text-gray-500 dark:text-neutral-500">
                        {t('pleaseReviewQuantities')}
                    </p>

                    {Object.keys(errors).length > 0 && (
                        <div className="mb-4 space-y-1 rounded-xl border border-red-200 bg-red-50 p-3 dark:border-red-800 dark:bg-red-900/20">
                            {Object.entries(errors).map(([key, messages]) => (
                                <div
                                    key={key}
                                    className="flex items-start gap-2 text-sm text-red-700 dark:text-red-400"
                                >
                                    <XCircleIcon className="mt-0.5 h-4 w-4 shrink-0" />
                                    <span>
                                        {Array.isArray(messages)
                                            ? messages[0]
                                            : String(messages)}
                                    </span>
                                </div>
                            ))}
                        </div>
                    )}

                    <div className="mb-4 max-h-64 space-y-3 overflow-y-auto">
                        {data.items.map((item: any) => (
                            <div
                                key={item.medication_id}
                                className="flex items-center justify-between rounded-2xl border border-gray-100 bg-gray-50 p-4 dark:border-neutral-700 dark:bg-neutral-700/50"
                            >
                                <div>
                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                        {item.name}
                                    </p>
                                    <p className="mt-0.5 text-xs text-gray-500 dark:text-neutral-500">
                                        {t('inventoryPostDispense')}:{' '}
                                        <span className="font-medium text-gray-700 dark:text-neutral-300">
                                            {item.stock - item.quantity}
                                        </span>
                                    </p>
                                </div>
                                <div className="text-right">
                                    <p className="text-lg font-black text-blue-600">
                                        x{item.quantity}
                                    </p>
                                    {item.price && (
                                        <p className="text-xs font-semibold text-gray-500 dark:text-neutral-500">
                                            {t('currencySymbol')}
                                            {(
                                                item.price * item.quantity
                                            ).toFixed(2)}
                                        </p>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="space-y-4 border-t border-gray-100 pt-4 dark:border-neutral-700">
                        <div className="flex items-end justify-between pb-2">
                            <div>
                                <p className="text-xs font-bold tracking-wider text-gray-400 uppercase dark:text-neutral-500">
                                    {t('totalItems')}
                                </p>
                                <p className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                                    {data.items.reduce(
                                        (acc, i) => acc + i.quantity,
                                        0,
                                    )}{' '}
                                    {t('units')}
                                </p>
                            </div>
                            {viewMode === 'walkin' && (
                                <div className="text-right">
                                    <p className="text-xs font-bold tracking-wider text-gray-400 uppercase dark:text-neutral-500">
                                        {t('totalCost')}
                                    </p>
                                    <p className="text-2xl font-black text-gray-900 dark:text-neutral-100">
                                        {t('currencySymbol')}
                                        {cartTotal.toFixed(2)}
                                    </p>
                                </div>
                            )}
                        </div>

                        <div className="flex gap-3 pt-2">
                            <button
                                type="button"
                                onClick={() => setIsModalOpen(false)}
                                className="flex-1 rounded-xl bg-gray-100 px-4 py-3 text-sm font-bold text-gray-700 transition-colors hover:bg-gray-200 dark:text-neutral-300"
                            >
                                {t('cancel')}
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className={`flex flex-[2] items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-bold text-white shadow-md transition-all ${viewMode === 'walkin' ? 'bg-emerald-600 shadow-emerald-200 hover:bg-emerald-700' : 'bg-blue-600 shadow-blue-200 hover:bg-blue-700'} disabled:opacity-70`}
                            >
                                {processing && (
                                    <ArrowPathIcon className="h-5 w-5 animate-spin" />
                                )}
                                {t('confirmAndProcess')}
                            </button>
                        </div>
                    </div>
                </form>
            </Modal>

            <style>{`
                .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 10px; }
            `}</style>
        </AppLayout>
    );
}
