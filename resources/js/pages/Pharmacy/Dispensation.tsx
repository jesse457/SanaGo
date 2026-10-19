import { BeakerIcon, UserCircleIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import React, { useEffect, useMemo, useState } from 'react';
import { toast } from 'sonner';
import { EmptyState, PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { DispenseCartPanel } from './components/DispenseCartPanel';
import { DispenseConfirmDialog } from './components/DispenseConfirmDialog';
import { DispenseModeSwitch } from './components/DispenseModeSwitch';
import { DispensePicker } from './components/DispensePicker';
import { PrescriptionPanel } from './components/PrescriptionPanel';
import type {
    DispenseCartItem,
    DispenseMedication,
    DispensePatient,
    DispenseViewMode,
    Prescription,
} from './components/types';

interface Props {
    patients: any[];
    medications: any[];
}

export default function DispenseMedications({ patients, medications }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [viewMode, setViewMode] = useState<DispenseViewMode>('prescription');
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

            <div className="mx-auto max-w-7xl space-y-5 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={BeakerIcon}
                    eyebrow={t('pharmacyDispensing')}
                    title={t('agnosticDispensing')}
                    actions={
                        <DispenseModeSwitch
                            value={viewMode}
                            onChange={(mode) => {
                                if (mode === 'walkin') {
                                    startWalkIn();
                                } else {
                                    startPrescription();
                                }
                            }}
                            prescriptionLabel={t('prescriptions')}
                            walkInLabel={t('walkIn')}
                        />
                    }
                />

                <div className="grid grid-cols-1 gap-5 lg:grid-cols-12">
                    <div className="lg:col-span-4 xl:col-span-3">
                        <DispensePicker
                            mode={viewMode}
                            search={search}
                            onSearchChange={setSearch}
                            heading={
                                viewMode === 'prescription'
                                    ? t('selectPatientNumber')
                                    : t('searchInventoryNumber')
                            }
                            searchPlaceholder={
                                viewMode === 'prescription'
                                    ? t('searchPatientName')
                                    : t('searchMedication')
                            }
                            patients={filteredPatients as DispensePatient[]}
                            medications={
                                filteredMedications as DispenseMedication[]
                            }
                            selectedPatient={selectedPatient}
                            onSelectPatient={setSelectedPatient}
                            onAddMedication={addWalkInItem}
                            noPatientsLabel={t('noPatientsFound')}
                            noMedicationsLabel={t('noMedicationsFound')}
                            stockLabel={t('stock')}
                            idLabel={t('id')}
                        />
                    </div>

                    <main className="lg:col-span-8 xl:col-span-9">
                        <AnimatePresence mode="wait">
                            {viewMode === 'prescription' && selectedPatient ? (
                                <motion.div
                                    key="prescription-view"
                                    initial={{ opacity: 0, y: 10 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -10 }}
                                    transition={{
                                        duration: 0.22,
                                        ease: [0.16, 1, 0.3, 1],
                                    }}
                                >
                                    <PrescriptionPanel
                                        patient={selectedPatient}
                                        onDispense={(
                                            prescription: Prescription,
                                        ) =>
                                            handleStartPrescriptionDispense(
                                                selectedPatient,
                                                prescription,
                                            )
                                        }
                                        labels={{
                                            id: t('id'),
                                            activePrescriptions: t(
                                                'activePrescriptions',
                                            ),
                                            dr: t('dr'),
                                            dispensePrescription: t(
                                                'dispensePrescription',
                                            ),
                                            noActivePrescriptions: t(
                                                'noActivePrescriptions',
                                            ),
                                        }}
                                    />
                                </motion.div>
                            ) : viewMode === 'walkin' ? (
                                <motion.div
                                    key="walkin-view"
                                    initial={{ opacity: 0, y: 10 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -10 }}
                                    transition={{
                                        duration: 0.22,
                                        ease: [0.16, 1, 0.3, 1],
                                    }}
                                >
                                    <DispenseCartPanel
                                        items={data.items as DispenseCartItem[]}
                                        notes={data.notes}
                                        onNotesChange={(value) =>
                                            setData('notes', value)
                                        }
                                        onQuantityChange={updateQty}
                                        onRemove={removeItem}
                                        onCheckout={() => setIsModalOpen(true)}
                                        cartTotal={cartTotal}
                                    />
                                </motion.div>
                            ) : (
                                <motion.div
                                    key="empty-state"
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    transition={{ duration: 0.22 }}
                                >
                                    <EmptyState
                                        icon={UserCircleIcon}
                                        title={t('noPatientSelected')}
                                        description={t(
                                            'searchAndSelectPatientDispensing',
                                        )}
                                        className="h-[500px] lg:h-[650px]"
                                    />
                                </motion.div>
                            )}
                        </AnimatePresence>
                    </main>
                </div>
            </div>

            <DispenseConfirmDialog
                open={isModalOpen}
                onClose={() => setIsModalOpen(false)}
                onSubmit={submit}
                processing={processing}
                items={data.items as DispenseCartItem[]}
                errors={errors as Record<string, string | string[] | undefined>}
                viewMode={viewMode}
                cartTotal={cartTotal}
            />
        </AppLayout>
    );
}
