import { Head, Link, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    MagnifyingGlassIcon, UserGroupIcon, ShoppingBagIcon,
    CalendarIcon, UserCircleIcon, ArrowPathIcon, PlusIcon,
    TrashIcon, MinusIcon, BeakerIcon, CurrencyDollarIcon,
    DocumentTextIcon,
    ExclamationTriangleIcon, CheckCircleIcon, XCircleIcon
} from '@heroicons/react/24/outline';
import React, { useState, useMemo, useEffect } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { toast, Toaster } from 'sonner';

interface Props {
    patients: any[];
    medications: any[];
}

export default function DispenseMedications({ patients, medications }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [viewMode, setViewMode] = useState<'prescription' | 'walkin'>('prescription');
    const [selectedPatient, setSelectedPatient] = useState<any>(null);
    const [isModalOpen, setIsModalOpen] = useState(false);

    const { data, setData, post, processing, reset, errors } = useForm({
        patient_id: null as number | null,
        prescription_id: null as number | null,
        is_walk_in: false,
        items: [] as any[],
        notes: ''
    });

    // Clear search when switching modes
    useEffect(() => {
        setSearch('');
    }, [viewMode]);

    // --- Logic for Prescription Flow ---
    const handleStartPrescriptionDispense = (patient: any, prescription: any) => {
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
                price: i.medication.unit_cost
            }))
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
            items: []
        });
    };

    const startPrescription = () => {
        setViewMode('prescription');
        setData('items', []);
    };

    const addWalkInItem = (med: any) => {
        if (data.items.find(i => i.medication_id === med.id)) return;
        setData('items', [...data.items, {
            medication_id: med.id,
            name: med.name,
            quantity: 1,
            max: med.total_stock,
            stock: med.total_stock,
            price: med.unit_cost
        }]);
    };

    const removeItem = (idx: number) => {
        setData('items', data.items.filter((_, i) => i !== idx));
    };

    const updateQty = (idx: number, qty: number) => {
        if (isNaN(qty)) return;
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
    const filteredPatients = useMemo(() => 
        patients.filter(p => `${p.first_name} ${p.last_name}`.toLowerCase().includes(search.toLowerCase())),
    [patients, search]);

    const filteredMedications = useMemo(() => 
        medications.filter(m => m.name.toLowerCase().includes(search.toLowerCase())),
    [medications, search]);

    // Calculations
    const cartTotal = data.items.reduce((acc, item) => acc + (item.price * item.quantity), 0);

    return (
        <AppLayout headerTitle={t('pharmacyDispensing')} headerSubtitle={t('agnosticDispensing')}>
            <Head title={t('dispensing')} />
            <Toaster position="top-right" richColors />

            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-12">
                
                {/* Agnostic Header Controls */}
                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-neutral-800 p-2 rounded-2xl border border-gray-200 dark:border-neutral-700 shadow-sm">
                    <div className="flex p-1 bg-gray-100/80 rounded-xl w-full sm:w-fit">
                        <button 
                            onClick={startPrescription}
                            className={`flex-1 sm:flex-none flex justify-center items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 ${viewMode === 'prescription' ? 'bg-white text-blue-600 shadow-sm ring-1 ring-gray-200/50' : 'text-gray-500 dark:text-neutral-500 hover:text-gray-700 dark:text-neutral-300'}`}
                        >
                            <UserGroupIcon className="w-5 h-5" /> {t('prescriptions')}
                        </button>
                        <button 
                            onClick={startWalkIn}
                            className={`flex-1 sm:flex-none flex justify-center items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 ${viewMode === 'walkin' ? 'bg-white text-emerald-600 shadow-sm ring-1 ring-gray-200/50' : 'text-gray-500 dark:text-neutral-500 hover:text-gray-700 dark:text-neutral-300'}`}
                        >
                            <ShoppingBagIcon className="w-5 h-5" /> {t('walkIn')}
                        </button>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    
                    {/* SIDEBAR: Search & Selection */}
                    <aside className="lg:col-span-4 xl:col-span-3 bg-white dark:bg-neutral-800 rounded-2xl border border-gray-200 dark:border-neutral-700 shadow-sm flex flex-col h-[500px] lg:h-[650px]">
                        <div className="p-4 border-b border-gray-100 dark:border-neutral-700 space-y-3 bg-gray-50/30 dark:bg-neutral-700/30 dark:bg-neutral-700/30 rounded-t-2xl">
                            <h3 className="text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase tracking-wider">
                                {viewMode === 'prescription' ? t('selectPatientNumber') : t('searchInventoryNumber')}
                            </h3>
                            <div className="relative">
                                <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                                <input 
                                    type="text" 
                                    placeholder={viewMode === 'prescription' ? t('searchPatientName') : t('searchMedication')}
                                    value={search} 
                                    onChange={e => setSearch(e.target.value)}
                                    className="w-full pl-9 pr-4 py-2 bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-shadow"
                                />
                            </div>
                        </div>
                        
                        <div className="flex-grow overflow-y-auto p-3 space-y-1 custom-scrollbar">
                            {viewMode === 'prescription' ? (
                                filteredPatients.length > 0 ? filteredPatients.map(p => (
                                    <button 
                                        key={p.id} 
                                        onClick={() => setSelectedPatient(p)} 
                                        className={`w-full text-left p-3 rounded-xl transition-all duration-200 ${selectedPatient?.id === p.id ? 'bg-blue-50 ring-1 ring-blue-200' : 'hover:bg-gray-50 dark:hover:bg-neutral-700'}`}
                                    >
                                        <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100">{p.first_name} {p.last_name}</p>
                                        <p className="text-xs text-gray-500 dark:text-neutral-500 font-mono mt-0.5">{t('id')}: {p.patient_uid}</p>
                                    </button>
                                )) : (
                                    <div className="text-center p-6 text-gray-400 dark:text-neutral-500 text-sm">{t('noPatientsFound')}</div>
                                )
                            ) : (
                                filteredMedications.length > 0 ? filteredMedications.map(m => (
                                    <button 
                                        key={m.id} 
                                        onClick={() => addWalkInItem(m)} 
                                        className="w-full text-left p-3 rounded-xl hover:bg-emerald-50 hover:ring-1 hover:ring-emerald-100 group flex justify-between items-center transition-all duration-200"
                                    >
                                        <div>
                                            <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100">{m.name}</p>
                                            <p className="text-xs text-gray-500 dark:text-neutral-500 mt-0.5">{t('stock')}: <span className="font-medium text-gray-700 dark:text-neutral-300">{m.total_stock}</span> {m.dosage_units}</p>
                                        </div>
                                        <div className="bg-emerald-100 p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                            <PlusIcon className="w-4 h-4 text-emerald-600" />
                                        </div>
                                    </button>
                                )) : (
                                    <div className="text-center p-6 text-gray-400 dark:text-neutral-500 text-sm">{t('noMedicationsFound')}</div>
                                )
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
                                    <div className="bg-white dark:bg-neutral-800 p-5 sm:p-6 rounded-2xl border border-gray-200 dark:border-neutral-700 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div className="flex items-center gap-4">
                                            <div className="h-14 w-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-200/50">
                                                {selectedPatient.first_name[0]}{selectedPatient.last_name[0]}
                                            </div>
                                            <div>
                                                <h2 className="text-xl font-bold text-gray-900 dark:text-neutral-100 tracking-tight">{selectedPatient.first_name} {selectedPatient.last_name}</h2>
                                                <div className="flex items-center gap-2 mt-1">
                                                    <span className="text-xs text-gray-500 dark:text-neutral-500 font-medium bg-gray-100 px-2 py-0.5 rounded-md">{t('id')}: {selectedPatient.patient_uid}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="px-4 py-2 bg-blue-50 rounded-xl text-blue-700 text-sm font-semibold border border-blue-100 flex items-center gap-2">
                                            <DocumentTextIcon className="w-4 h-4" />
                                            {selectedPatient.prescriptions.length} {t('activePrescriptions')}
                                        </div>
                                    </div>

                                    {/* Prescriptions List */}
                                    <div className="grid xl:grid-cols-2 gap-4">
                                        {selectedPatient.prescriptions.map((pr: any) => (
                                            <div key={pr.id} className="bg-white dark:bg-neutral-800 rounded-2xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden hover:border-blue-300 transition-colors flex flex-col">
                                                <div className="p-4 border-b border-gray-100 dark:border-neutral-700 bg-gray-50/50 dark:bg-neutral-700/50 dark:bg-neutral-700/50 flex justify-between items-center">
                                                    <div className="flex items-center gap-3">
                                                        <div className="bg-white dark:bg-neutral-800 p-2 rounded-lg shadow-sm border border-gray-100 dark:border-neutral-700">
                                                            <CalendarIcon className="w-5 h-5 text-blue-500" />
                                                        </div>
                                                        <div>
                                                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{new Date(pr.prescription_date).toLocaleDateString()}</p>
                                                            <p className="text-xs text-gray-500 dark:text-neutral-500 font-medium">{t('dr')} {pr.doctor?.name}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="p-4 space-y-3 flex-grow">
                                                    {pr.items.map((item: any) => (
                                                        <div key={item.id} className="flex justify-between items-center text-sm border-b border-gray-50 pb-2 last:border-0 last:pb-0">
                                                            <div className="flex items-center gap-2">
                                                                <BeakerIcon className="w-4 h-4 text-gray-400 dark:text-neutral-500" />
                                                                <span className="font-medium text-gray-700 dark:text-neutral-300">{item.medication.name}</span>
                                                            </div>
                                                            <div className="flex flex-col items-end">
                                                                <span className="font-bold text-gray-900 dark:text-neutral-100">x{item.quantity}</span>
                                                                <span className="text-[10px] text-gray-400 dark:text-neutral-500">{item.dosage}</span>
                                                            </div>
                                                        </div>
                                                    ))}
                                                </div>
                                                <div className="p-4 bg-gray-50/50 dark:bg-neutral-700/50 dark:bg-neutral-700/50 border-t border-gray-100 dark:border-neutral-700">
                                                    <button 
                                                        onClick={() => handleStartPrescriptionDispense(selectedPatient, pr)} 
                                                        className="w-full py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                                    >
                                                        {t('dispensePrescription')}
                                                    </button>
                                                </div>
                                            </div>
                                        ))}
                                        {selectedPatient.prescriptions.length === 0 && (
                                            <div className="xl:col-span-2 p-8 text-center bg-gray-50 dark:bg-neutral-700 rounded-2xl border border-dashed border-gray-200 dark:border-neutral-700">
                                                <p className="text-gray-500 dark:text-neutral-500 text-sm">{t('noActivePrescriptions')}</p>
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
                                    className="bg-white dark:bg-neutral-800 rounded-2xl border border-gray-200 dark:border-neutral-700 shadow-sm flex flex-col h-[500px] lg:h-[650px]"
                                >
                                    <div className="p-4 border-b border-gray-100 dark:border-neutral-700 flex justify-between items-center bg-gray-50/30 dark:bg-neutral-700/30 dark:bg-neutral-700/30 rounded-t-2xl">
                                        <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">{t('walkInCart')}</h3>
                                        {data.items.length > 0 && (
                                            <span className="text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full">
                                                {data.items.length} {t('items')}
                                            </span>
                                        )}
                                    </div>
                                    
                                    <div className="flex-grow overflow-y-auto p-2 sm:p-4 custom-scrollbar">
                                        {data.items.length > 0 ? (
                                            <div className="space-y-3">
                                                <AnimatePresence>
                                                    {data.items.map((item, idx) => (
                                                        <motion.div 
                                                            key={item.medication_id}
                                                            initial={{ opacity: 0, scale: 0.95 }}
                                                            animate={{ opacity: 1, scale: 1 }}
                                                            exit={{ opacity: 0, scale: 0.95 }}
                                                            className="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white dark:bg-neutral-800 border border-gray-100 dark:border-neutral-700 rounded-2xl shadow-sm hover:border-emerald-200 transition-colors gap-4"
                                                        >
                                                            <div className="flex-1">
                                                                <p className="text-base font-bold text-gray-900 dark:text-neutral-100">{item.name}</p>
                                                                <p className="text-xs text-gray-500 dark:text-neutral-500 font-medium mt-1">{t('currencySymbol')}{parseFloat(item.price).toFixed(2)} {t('perUnit')}</p>
                                                            </div>
                                                            
                                                            <div className="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto">
                                                                <div className="flex items-center bg-gray-50 dark:bg-neutral-700 rounded-xl p-1 border border-gray-200 dark:border-neutral-700">
                                                                    <button 
                                                                        type="button"
                                                                        onClick={() => updateQty(idx, item.quantity - 1)}
                                                                        className="p-1.5 hover:bg-white hover:shadow-sm rounded-lg text-gray-600 dark:text-neutral-400 transition-all"
                                                                    >
                                                                        <MinusIcon className="w-4 h-4"/>
                                                                    </button>
                                                                    <input 
                                                                        type="number" 
                                                                        value={item.quantity} 
                                                                        onChange={e => updateQty(idx, parseInt(e.target.value))}
                                                                        className="w-12 text-center bg-transparent border-none text-sm font-bold focus:ring-0 p-0"
                                                                    />
                                                                    <button 
                                                                        type="button"
                                                                        onClick={() => updateQty(idx, item.quantity + 1)}
                                                                        className="p-1.5 hover:bg-white hover:shadow-sm rounded-lg text-gray-600 dark:text-neutral-400 transition-all"
                                                                    >
                                                                        <PlusIcon className="w-4 h-4"/>
                                                                    </button>
                                                                </div>
                                                                
                                                                <div className="text-right w-16">
                                                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{t('currencySymbol')}{(item.price * item.quantity).toFixed(2)}</p>
                                                                </div>

                                                                <button onClick={() => removeItem(idx)} className="p-2 text-gray-400 dark:text-neutral-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors">
                                                                    <TrashIcon className="w-5 h-5"/>
                                                                </button>
                                                            </div>
                                                        </motion.div>
                                                    ))}
                                                </AnimatePresence>
                                            </div>
                                        ) : (
                                            <div className="flex flex-col items-center justify-center h-full text-gray-400 dark:text-neutral-500 space-y-3">
                                                <div className="p-4 bg-gray-50 dark:bg-neutral-700 rounded-full">
                                                    <ShoppingBagIcon className="w-10 h-10 text-gray-300" />
                                                </div>
                                                <p className="text-sm font-medium">{t('yourCartIsEmpty')}</p>
                                                <p className="text-xs text-gray-400 dark:text-neutral-500">{t('selectMedicationsFromInventory')}</p>
                                            </div>
                                        )}
                                    </div>
                                    
                                    <div className="p-4 sm:p-5 border-t border-gray-100 dark:border-neutral-700 bg-gray-50/50 dark:bg-neutral-700/50 dark:bg-neutral-700/50 rounded-b-2xl space-y-4">
                                        {data.items.length > 0 && (
                                            <div className="flex justify-between items-center px-2">
                                                <span className="text-sm font-semibold text-gray-600 dark:text-neutral-400">{t('totalAmount')}:</span>
                                                <span className="text-2xl font-black text-gray-900 dark:text-neutral-100">{t('currencySymbol')}{cartTotal.toFixed(2)}</span>
                                            </div>
                                        )}
                                        <textarea 
                                            placeholder={t('addInternalNotes')}
                                            value={data.notes}
                                            onChange={e => setData('notes', e.target.value)}
                                            className="w-full text-sm rounded-xl border-gray-200 dark:border-neutral-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-shadow resize-none" 
                                            rows={2}
                                        />
                                        <button 
                                            onClick={() => setIsModalOpen(true)}
                                            disabled={data.items.length === 0}
                                            className="w-full py-3.5 bg-emerald-600 text-white text-sm font-bold uppercase tracking-wide rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-200 disabled:opacity-50 disabled:shadow-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
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
                                    className="bg-white dark:bg-neutral-800 rounded-2xl border border-dashed border-gray-300 h-[500px] lg:h-[650px] flex flex-col items-center justify-center text-center p-12"
                                >
                                    <UserCircleIcon className="w-16 h-16 text-gray-200 mb-4" />
                                    <h3 className="text-base font-bold text-gray-500 dark:text-neutral-500">{t('noPatientSelected')}</h3>
                                    <p className="text-sm text-gray-400 dark:text-neutral-500 mt-2 max-w-sm">
                                        {t('searchAndSelectPatientDispensing')}
                                    </p>
                                </motion.div>
                            )}
                        </AnimatePresence>
                    </main>
                </div>
            </div>

            {/* Confirmation Modal */}
            <AnimatePresence>
                {isModalOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
                        <motion.div 
                            initial={{ opacity: 0 }} 
                            animate={{ opacity: 1 }} 
                            exit={{ opacity: 0 }}
                            onClick={() => setIsModalOpen(false)} 
                            className="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" 
                        />
                        <motion.div 
                            initial={{ scale: 0.95, opacity: 0, y: 20 }} 
                            animate={{ scale: 1, opacity: 1, y: 0 }} 
                            exit={{ scale: 0.95, opacity: 0, y: 20 }}
                            className="bg-white dark:bg-neutral-800 rounded-3xl shadow-2xl w-full max-w-lg relative z-10 overflow-hidden flex flex-col max-h-full"
                        >
                            <form onSubmit={submit} className="flex flex-col max-h-full">
                                <div className="p-6 border-b border-gray-100 dark:border-neutral-700 bg-white dark:bg-neutral-800 shrink-0">
                                    <h3 className="text-xl font-black text-gray-900 dark:text-neutral-100">{t('confirmDispensation')}</h3>
                                    <p className="text-sm text-gray-500 dark:text-neutral-500 mt-1">{t('pleaseReviewQuantities')}</p>
                                    {Object.keys(errors).length > 0 && (
                                        <div className="mt-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 space-y-1">
                                            {Object.entries(errors).map(([key, messages]) => (
                                                <div key={key} className="flex items-start gap-2 text-sm text-red-700 dark:text-red-400">
                                                    <XCircleIcon className="w-4 h-4 mt-0.5 shrink-0" />
                                                    <span>{Array.isArray(messages) ? messages[0] : String(messages)}</span>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>

                                <div className="p-6 overflow-y-auto space-y-3 bg-gray-50/50 dark:bg-neutral-700/50 dark:bg-neutral-700/50">
                                    {data.items.map((item: any) => (
                                        <div key={item.medication_id} className="flex justify-between items-center bg-white dark:bg-neutral-800 p-4 rounded-2xl border border-gray-100 dark:border-neutral-700 shadow-sm">
                                            <div>
                                                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{item.name}</p>
                                                <p className="text-xs text-gray-500 dark:text-neutral-500 mt-0.5">{t('inventoryPostDispense')}: <span className="font-medium text-gray-700 dark:text-neutral-300">{item.stock - item.quantity}</span></p>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-lg font-black text-blue-600">x{item.quantity}</p>
                                                {item.price && (
                                                    <p className="text-xs font-semibold text-gray-500 dark:text-neutral-500">{t('currencySymbol')}{(item.price * item.quantity).toFixed(2)}</p>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                <div className="p-6 bg-white dark:bg-neutral-800 border-t border-gray-100 dark:border-neutral-700 shrink-0 space-y-4">
                                    <div className="flex justify-between items-end pb-4 border-b border-gray-100 dark:border-neutral-700">
                                        <div className="space-y-1">
                                            <p className="text-xs font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">{t('totalItems')}</p>
                                            <p className="text-lg font-bold text-gray-900 dark:text-neutral-100">{data.items.reduce((acc, i) => acc + i.quantity, 0)} {t('units')}</p>
                                        </div>
                                        {viewMode === 'walkin' && (
                                            <div className="space-y-1 text-right">
                                                <p className="text-xs font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">{t('totalCost')}</p>
                                                <p className="text-2xl font-black text-gray-900 dark:text-neutral-100">{t('currencySymbol')}{cartTotal.toFixed(2)}</p>
                                            </div>
                                        )}
                                    </div>

                                    <div className="flex gap-3 pt-2">
                                        <button 
                                            type="button" 
                                            onClick={() => setIsModalOpen(false)} 
                                            className="flex-1 py-3 px-4 bg-gray-100 text-gray-700 dark:text-neutral-300 text-sm font-bold rounded-xl hover:bg-gray-200 transition-colors"
                                        >
                                            {t('cancel')}
                                        </button>
                                        <button 
                                            type="submit" 
                                            disabled={processing}
                                            className={`flex-[2] py-3 px-4 text-white text-sm font-bold rounded-xl shadow-md transition-all flex justify-center items-center gap-2 ${viewMode === 'walkin' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-200'} disabled:opacity-70`}
                                        >
                                            {processing && <ArrowPathIcon className="w-5 h-5 animate-spin"/>}
                                            {t('confirmAndProcess')}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>

            <style>{`
                .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 10px; }
            `}</style>
        </AppLayout>
    );
}