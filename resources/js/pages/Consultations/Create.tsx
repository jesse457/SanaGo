import { Head, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Search, Home, ChevronRight, User, Phone, AlertTriangle,
    CheckCircle, Trash2, UploadCloud, FileText, X,
    Activity, Pill, Stethoscope, FileDigit, Plus
} from 'lucide-react';
import React, { useState, useMemo } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

// Define standard Laravel models for the UI
export interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
    dob: string | null;
    gender: 'male' | 'female' | 'other' | null;
    phone_number: string | null;
    allergies: string | null;
}

export interface Medication {
    id: number;
    name: string;
    unit_price?: number;
}

export interface LabTestDefinition {
    id: number;
    test_name: string;
    code: string;
}

// Define the incoming props for the Consultation Page
export interface ConsultationPageProps {
    patients: Patient[];
    medications: Medication[];
    labTests: LabTestDefinition[];
}

// Define the form structures
export interface PrescriptionFormItem {
    medication_id: number | null;
    drug_name: string;
    dosage: string;
    frequency: string;
    duration: string;
    quantity: number;
    instructions: string;
}

export interface LabRequestFormItem {
    test_definition_id: number;
    test_name: string;
    code: string;
    urgency: 'routine' | 'urgent' | 'stat';
    reason_for_test: string;
}

export interface ConsultationForm {
    patient_id: number | string;
    chief_complaint: string;
    history_of_present_illness: string;
    physical_exam: string;
    diagnosis: string;
    treatment_plan: string;
    notes: string;
    prescription_items: PrescriptionFormItem[];
    lab_requests: LabRequestFormItem[];
    attachments: File[];
}

const ConsultationPage = ({ patients = [], medications = [], labTests = [] }: ConsultationPageProps) => {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState('clinical');
    const [patientQuery, setPatientQuery] = useState('');
    const [selectedPatient, setSelectedPatient] = useState<any>(null);

    // Smart Prescription Input State
    const [currentDrugInput, setCurrentDrugInput] = useState('');
    const [matchedDrug, setMatchedDrug] = useState<any>(null);

    const { data, setData, post, processing, errors, reset } = useForm<ConsultationForm>({
        patient_id: '',
        chief_complaint: '',
        history_of_present_illness: '',
        physical_exam: '',
        diagnosis: '',
        treatment_plan: '',
        notes: '',
        prescription_items: [],
        lab_requests: [],
        attachments: [],
    });

    // Patient Search
    const searchResults = useMemo(() => {
        if (patientQuery.length < 2) return [];
        return patients.filter(p =>
            `${p.first_name} ${p.last_name}`.toLowerCase().includes(patientQuery.toLowerCase()) ||
            p.patient_uid?.includes(patientQuery)
        );
    }, [patientQuery, patients]);

    // Drug Search (Autocomplete logic)
    const drugSuggestions = useMemo(() => {
        if (currentDrugInput.length < 2) return [];
        return medications.filter((m: any) => m.name.toLowerCase().includes(currentDrugInput.toLowerCase()));
    }, [currentDrugInput, medications]);

    const handleSelectPatient = (patient: any) => {
        setSelectedPatient(patient);
        setData('patient_id', patient.id);
        setPatientQuery('');
    };

    // --- Medication Logic ---
    const handleAddPrescription = () => {
        if (!currentDrugInput.trim()) return;

        const exactMatch = medications.find((m: any) => m.name.toLowerCase() === currentDrugInput.trim().toLowerCase());
        const medId = exactMatch ? exactMatch.id : (matchedDrug ? matchedDrug.id : null);
        const medName = exactMatch ? exactMatch.name : (matchedDrug ? matchedDrug.name : currentDrugInput.trim());

        setData('prescription_items', [
            ...data.prescription_items,
            {
                medication_id: medId,
                drug_name: medName,
                dosage: '',
                frequency: '',
                duration: '',
                quantity: 1,
                instructions: ''
            }
        ]);

        setCurrentDrugInput('');
        setMatchedDrug(null);
    };

    const removeMedication = (index: number) => {
        const newItems = [...data.prescription_items];
        newItems.splice(index, 1);
        setData('prescription_items', newItems);
    };

    const updateMedication = (index: number, field: keyof PrescriptionFormItem, value: string | number) => {
        const newItems = [...data.prescription_items];
        newItems[index] = { ...newItems[index], [field]: value };
        setData('prescription_items', newItems);
    };

    // --- Lab Logic ---
    const addLabTest = (id: string) => {
        if (!id) return;
        const test = labTests.find((t: any) => t.id === Number(id));
        if (test && !data.lab_requests.find((item: any) => item.test_definition_id === test.id)) {
            setData('lab_requests', [
                ...data.lab_requests,
                { test_definition_id: test.id, test_name: test.test_name, code: test.code, urgency: 'routine', reason_for_test: '' }
            ]);
        }
    };

    const removeLabTest = (index: number) => {
        const newItems = [...data.lab_requests];
        newItems.splice(index, 1);
        setData('lab_requests', newItems);
    };

    const updateLabTest = (index: number, field: keyof LabRequestFormItem, value: string) => {
        const newItems = [...data.lab_requests];
        newItems[index] = { ...newItems[index], [field]: value };
        setData('lab_requests', newItems);
    };

    const submitConsultation = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('consultations.store'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                reset();
                setSelectedPatient(null);
                setActiveTab('clinical');
            }
        });
    };

    return (
        <form onSubmit={submitConsultation} className="flex flex-col h-[calc(100vh-2rem)] overflow-hidden bg-gray-50 dark:bg-neutral-900 -m-8 text-gray-900 dark:text-neutral-100">
            <Head title={t('consultation')} />

            {/* Header section with theme alignment */}
            <header className="flex-shrink-0 bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 shadow-sm z-20">
                <div className="px-6 py-4 flex items-center justify-between">
                    <div>
                        <nav className="flex text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-neutral-500 mb-1">
                            <ol className="flex items-center space-x-2">
                                <li className="flex items-center cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <Home className="w-3.5 h-3.5 mr-1.5" /> {t('home')}
                                </li>
                                <li><ChevronRight className="w-3 h-3 text-gray-300 dark:text-neutral-600" /></li>
                                <li className="text-blue-600 dark:text-blue-400">{t('consultation')}</li>
                            </ol>
                        </nav>
                        <h1 className="text-xl font-bold tracking-tight">{t('patientConsultation')}</h1>
                    </div>
                </div>
            </header>

            <div className="flex flex-1 overflow-hidden">
                {/* LEFT SIDEBAR - Patient Selection & Demographics */}
                <aside className="w-full md:w-80 lg:w-96 flex-shrink-0 flex flex-col border-r border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 z-10 h-full">
                    <div className="p-4 border-b border-gray-200 dark:border-neutral-700 relative bg-gray-50/50 dark:bg-neutral-800/50">
                        <label className="block text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider mb-2">{t('searchPatient')}</label>
                        <div className="relative">
                            <Search className="absolute left-3 top-2.5 w-4 h-4 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                value={patientQuery}
                                onChange={(e) => setPatientQuery(e.target.value)}
                                className="w-full pl-9 pr-4 py-2 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-sm shadow-sm placeholder-gray-400 dark:placeholder-neutral-500 text-gray-900 dark:text-neutral-100"
                                placeholder={t('nameOrId')}
                            />

                            <AnimatePresence>
                                {searchResults.length > 0 && (
                                    <motion.div
                                        initial={{ opacity: 0, y: 5 }}
                                        animate={{ opacity: 1, y: 0 }}
                                        exit={{ opacity: 0, y: 5 }}
                                        className="absolute top-full left-0 right-0 mt-1.5 bg-white dark:bg-neutral-800 rounded-lg shadow-lg border border-gray-200 dark:border-neutral-700 overflow-hidden z-50 max-h-64 overflow-y-auto"
                                    >
                                        {searchResults.map((p: any) => (
                                            <button
                                                key={p.id}
                                                type="button"
                                                onClick={() => handleSelectPatient(p)}
                                                className="w-full text-left px-4 py-3 hover:bg-blue-50 dark:hover:bg-neutral-700 border-b border-gray-100 dark:border-neutral-700/50 last:border-0 flex items-center gap-3 transition-colors"
                                            >
                                                <div className="h-8 w-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs shadow-sm border border-blue-100 dark:border-blue-900/30">
                                                    {p.first_name?.[0]}{p.last_name?.[0]}
                                                </div>
                                                <div>
                                                    <div className="font-bold text-gray-900 dark:text-neutral-100 text-sm">{p.first_name} {p.last_name}</div>
                                                    <div className="text-[10px] font-mono text-gray-500 dark:text-neutral-400">{p.patient_uid || t('noUid')}</div>
                                                </div>
                                            </button>
                                        ))}
                                    </motion.div>
                                )}
                            </AnimatePresence>
                        </div>
                        {errors.patient_id && <p className="text-red-500 text-xs mt-2 font-semibold">{errors.patient_id}</p>}
                    </div>

                    {/* Patient Information Panel */}
                    <div className="flex-1 overflow-y-auto p-5 bg-white dark:bg-neutral-800">
                        <AnimatePresence mode="wait">
                            {selectedPatient ? (
                                <motion.div key={selectedPatient.id} initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} className="space-y-6">
                                    <div className="flex flex-col items-center text-center">
                                        <div className="w-16 h-16 rounded-xl bg-blue-600 dark:bg-blue-500 flex items-center justify-center text-white text-xl font-bold mb-3 shadow-md border-2 border-white dark:border-neutral-700">
                                            {selectedPatient.first_name?.[0]}{selectedPatient.last_name?.[0]}
                                        </div>
                                        <h2 className="text-lg font-bold text-gray-950 dark:text-neutral-50">{selectedPatient.first_name} {selectedPatient.last_name}</h2>
                                        <span className="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-50 border border-gray-200 text-gray-700 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 mt-2 shadow-sm">
                                            {selectedPatient.dob ? new Date().getFullYear() - new Date(selectedPatient.dob).getFullYear() + ' ' + t('yrs') : t('na')} • {selectedPatient.gender || t('unknown')}
                                        </span>
                                    </div>
                                    <div className="space-y-3">
                                        <div className="bg-gray-50 dark:bg-neutral-900/50 p-4 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm">
                                            <span className="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-neutral-500 flex items-center gap-1.5"><Phone className="w-3.5 h-3.5"/> {t('contact')}</span>
                                            <p className="text-sm font-semibold text-gray-800 dark:text-neutral-200 mt-1.5">{selectedPatient.phone_number || t('noContactProvided')}</p>
                                        </div>
                                        {selectedPatient.allergies && (
                                            <div className="bg-red-50 dark:bg-red-950/20 p-4 rounded-xl border border-red-100 dark:border-red-900/30 shadow-sm">
                                                <span className="text-[10px] font-black uppercase tracking-wider text-red-600 dark:text-red-400 flex items-center gap-1.5"><AlertTriangle className="w-3.5 h-3.5"/> {t('allergies')}</span>
                                                <p className="text-sm font-semibold text-red-800 dark:text-red-300 mt-1.5">{selectedPatient.allergies}</p>
                                            </div>
                                        )}
                                    </div>
                                </motion.div>
                            ) : (
                                <div className="h-full flex flex-col items-center justify-center text-gray-400 dark:text-neutral-500 text-center opacity-70">
                                    <User className="w-12 h-12 mb-3" />
                                    <p className="text-xs font-black uppercase tracking-widest">{t('selectPatient')}</p>
                                </div>
                            )}
                        </AnimatePresence>
                    </div>
                </aside>

                {/* RIGHT MAIN WORKSPACE */}
                <main className="flex-1 flex flex-col h-full bg-gray-50 dark:bg-neutral-900 min-w-0 relative">
                    {selectedPatient ? (
                        <>
                            {/* Tabs Header with Cohesive Dark/Light Indicators */}
                            <div className="bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 px-6 pt-3 flex items-center justify-between flex-shrink-0 z-10 shadow-sm">
                                <div className="flex space-x-6 overflow-x-auto no-scrollbar">
                                    {[
                                        { id: 'clinical', label: t('clinicalNotes'), icon: Stethoscope },
                                        { id: 'rx', label: t('prescriptionPad'), icon: Pill },
                                        { id: 'labs', label: t('labRequests'), icon: Activity },
                                        { id: 'files', label: t('attachments'), icon: FileDigit }
                                    ].map((tab) => (
                                        <button
                                            key={tab.id}
                                            type="button"
                                            onClick={() => setActiveTab(tab.id)}
                                            className={`relative pb-3 px-1 text-xs font-black uppercase tracking-wider flex items-center gap-2 transition-colors outline-none
                                                ${activeTab === tab.id ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 hover:text-gray-900 dark:text-neutral-400 dark:hover:text-neutral-200'}`}
                                        >
                                            <tab.icon className="w-4 h-4" />
                                            {tab.label}
                                            {activeTab === tab.id && (
                                                <motion.div layoutId="activeTab" className="absolute bottom-0 left-0 right-0 h-[2.5px] bg-blue-600 dark:bg-blue-400 rounded-t-md" />
                                            )}
                                        </button>
                                    ))}
                                </div>
                                <div className="flex items-center gap-3 pb-2.5">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="px-5 py-2 bg-blue-600 text-white rounded-lg text-xs font-black uppercase tracking-wider flex items-center gap-2 shadow-sm hover:bg-blue-700 transition-all disabled:opacity-50"
                                    >
                                        {processing ? (
                                            <span className="flex items-center gap-2"><Activity className="w-4 h-4 animate-spin"/> {t('processing')}...</span>
                                        ) : (
                                            <><CheckCircle className="w-4 h-4"/> {t('saveRecord')}</>
                                        )}
                                    </button>
                                </div>
                            </div>

                            {/* Scrollable Workspace Tab Views */}
                            <div className="flex-1 overflow-y-auto p-6 bg-gray-50 dark:bg-neutral-900">
                                <AnimatePresence mode="wait">

                                    {/* Tab 1: CLINICAL NOTES */}
                                    {activeTab === 'clinical' && (
                                        <motion.div key="clinical" initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -10 }} className="space-y-6 max-w-4xl mx-auto">
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                                                    <label className="block text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider mb-2">{t('chiefComplaint')} <span className="text-red-500">*</span></label>
                                                    <textarea
                                                        value={data.chief_complaint}
                                                        onChange={(e) => setData('chief_complaint', e.target.value)}
                                                        rows={3}
                                                        className="w-full p-3 rounded-lg border border-gray-200 dark:border-neutral-750 dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm shadow-sm transition-all outline-none"
                                                        placeholder={t('primaryReasonForVisit')}
                                                    />
                                                    {errors.chief_complaint && <p className="text-red-500 text-[10px] font-bold uppercase mt-1">{errors.chief_complaint}</p>}
                                                </div>

                                                <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                                                    <label className="block text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider mb-2">{t('diagnosis')}</label>
                                                    <textarea
                                                        value={data.diagnosis}
                                                        onChange={(e) => setData('diagnosis', e.target.value)}
                                                        rows={3}
                                                        className="w-full p-3 rounded-lg border border-gray-200 dark:border-neutral-750 dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm shadow-sm transition-all outline-none"
                                                        placeholder={t('clinicalDiagnosis')}
                                                    />
                                                </div>
                                            </div>

                                            <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                                                <label className="block text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider mb-2">{t('historyOfPresentIllness')}</label>
                                                <textarea
                                                    value={data.history_of_present_illness}
                                                    onChange={(e) => setData('history_of_present_illness', e.target.value)}
                                                    rows={4}
                                                    className="w-full p-3 rounded-lg border border-gray-200 dark:border-neutral-750 dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm shadow-sm transition-all outline-none"
                                                    placeholder={t('detailedPresentation')}
                                                />
                                            </div>

                                            <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                                                <label className="block text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider mb-2">{t('physicalExamination')}</label>
                                                <textarea
                                                    value={data.physical_exam}
                                                    onChange={(e) => setData('physical_exam', e.target.value)}
                                                    rows={4}
                                                    className="w-full p-3 rounded-lg border border-gray-200 dark:border-neutral-750 dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm shadow-sm transition-all outline-none"
                                                    placeholder={t('vitalsPhysicalFindings')}
                                                />
                                            </div>
                                        </motion.div>
                                    )}

                                    {/* Tab 2: PRESCRIPTIONS TAB */}
                                    {activeTab === 'rx' && (
                                        <motion.div key="rx" initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} className="max-w-5xl mx-auto space-y-6">
                                            {/* Prescription Input Area */}
                                            <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                                                <label className="block text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider mb-3">{t('addMedicationToPrescription')}</label>
                                                <div className="flex flex-col sm:flex-row gap-3 relative">
                                                    <div className="flex-1 relative">
                                                        <input
                                                            type="text"
                                                            value={currentDrugInput}
                                                            onChange={(e) => {
                                                                setCurrentDrugInput(e.target.value);
                                                                setMatchedDrug(null);
                                                            }}
                                                            onKeyDown={(e) => e.key === 'Enter' && (e.preventDefault(), handleAddPrescription())}
                                                            placeholder={t('typeDrugName')}
                                                            className="w-full py-2 px-4 rounded-lg border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm transition-all"
                                                        />

                                                        {/* Dynamic Drug Autocomplete dropdown */}
                                                        {drugSuggestions.length > 0 && !matchedDrug && (
                                                            <div className="absolute top-full left-0 right-0 mt-1.5 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 shadow-lg rounded-lg z-50 max-h-48 overflow-y-auto">
                                                                {drugSuggestions.map((m: any) => (
                                                                    <button
                                                                        key={m.id}
                                                                        type="button"
                                                                        onClick={() => {
                                                                            setCurrentDrugInput(m.name);
                                                                            setMatchedDrug(m);
                                                                        }}
                                                                        className="w-full text-left px-4 py-2.5 text-sm hover:bg-blue-50 dark:hover:bg-neutral-700 border-b border-gray-100 dark:border-neutral-700/55 last:border-0"
                                                                    >
                                                                        {m.name} <span className="text-[10px] font-black uppercase text-green-600 dark:text-green-400 ml-2">{t('systemMatch')}</span>
                                                                    </button>
                                                                ))}
                                                            </div>
                                                        )}
                                                    </div>
                                                    <button
                                                        type="button"
                                                        onClick={handleAddPrescription}
                                                        className="py-2.5 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-black uppercase tracking-wider transition-colors flex items-center justify-center shadow-sm whitespace-nowrap"
                                                    >
                                                        <Plus className="w-4 h-4 mr-1"/> {t('addMedication')}
                                                    </button>
                                                </div>
                                                <p className="text-[10px] font-medium text-gray-400 dark:text-neutral-500 mt-2">{t('unrecognizedItemsExternal')}</p>
                                            </div>

                                            {/* Prescriptions Items Table */}
                                            {data.prescription_items.length > 0 && (
                                                <div className="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700 overflow-x-auto">
                                                    <table className="w-full text-left whitespace-nowrap divide-y divide-gray-200 dark:divide-neutral-700">
                                                        <thead className="bg-gray-50 dark:bg-neutral-700 text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase tracking-wider">
                                                            <tr>
                                                                <th className="px-6 py-4">{t('drugName')}</th>
                                                                <th className="px-4 py-4">{t('dosage')}</th>
                                                                <th className="px-4 py-4">{t('frequency')}</th>
                                                                <th className="px-4 py-4">{t('duration')}</th>
                                                                <th className="px-4 py-4 w-20">{t('qty')}</th>
                                                                <th className="px-4 py-4">{t('instructions')}</th>
                                                                <th className="px-6 py-4"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                                            {data.prescription_items.map((item: any, i: number) => (
                                                                <tr key={i} className="hover:bg-gray-50 dark:hover:bg-neutral-750 transition-colors">
                                                                    <td className="px-6 py-4">
                                                                        <div className="flex flex-col gap-1">
                                                                            <span className="font-bold text-gray-900 dark:text-neutral-100">{item.drug_name}</span>
                                                                            {!item.medication_id && (
                                                                                <span className="inline-block max-w-max bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 text-[9px] px-2 py-0.5 rounded font-black uppercase tracking-wider border border-amber-100 dark:border-amber-950">
                                                                                    {t('external')}
                                                                                </span>
                                                                            )}
                                                                        </div>
                                                                    </td>
                                                                    <td className="px-4 py-3">
                                                                        <input type="text" placeholder={t('egOneTab')} value={item.dosage} onChange={e => updateMedication(i, 'dosage', e.target.value)} className="w-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-xs py-1.5 px-2 shadow-sm transition-all" />
                                                                    </td>
                                                                    <td className="px-4 py-3">
                                                                        <input type="text" placeholder={t('egThreeTimesDay')} value={item.frequency} onChange={e => updateMedication(i, 'frequency', e.target.value)} className="w-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-xs py-1.5 px-2 shadow-sm transition-all" />
                                                                    </td>
                                                                    <td className="px-4 py-3">
                                                                        <input type="text" placeholder={t('egFiveDays')} value={item.duration} onChange={e => updateMedication(i, 'duration', e.target.value)} className="w-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-xs py-1.5 px-2 shadow-sm transition-all" />
                                                                    </td>
                                                                    <td className="px-4 py-3">
                                                                        <input type="number" min="1" value={item.quantity} onChange={e => updateMedication(i, 'quantity', parseInt(e.target.value))} className="w-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-xs py-1.5 px-2 shadow-sm transition-all" />
                                                                    </td>
                                                                    <td className="px-4 py-3">
                                                                        <input type="text" placeholder={t('egAfterMeals')} value={item.instructions} onChange={e => updateMedication(i, 'instructions', e.target.value)} className="w-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-xs py-1.5 px-2 shadow-sm transition-all" />
                                                                    </td>
                                                                    <td className="px-6 py-3 text-right">
                                                                        <button type="button" onClick={() => removeMedication(i)} className="text-gray-400 hover:text-red-600 p-2 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors"><Trash2 className="w-4 h-4"/></button>
                                                                    </td>
                                                                </tr>
                                                            ))}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            )}
                                        </motion.div>
                                    )}

                                    {/* Tab 3: LAB REQUESTS */}
                                    {activeTab === 'labs' && (
                                        <motion.div key="labs" initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} className="max-w-5xl mx-auto space-y-6">
                                            <div className="bg-white dark:bg-neutral-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                                                <div className="flex gap-4 mb-6">
                                                    <select
                                                        onChange={(e) => { addLabTest(e.target.value); e.target.value = ""; }}
                                                        className="flex-1 py-2.5 px-3 rounded-lg border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none font-medium shadow-sm transition-all text-gray-900 dark:text-neutral-100"
                                                    >
                                                        <option value="">{t('orderNewLabTest')}</option>
                                                        {labTests.map((t: any) => <option key={t.id} value={t.id}>{t.test_name}</option>)}
                                                    </select>
                                                </div>

                                                <div className="space-y-4">
                                                    {data.lab_requests.map((item: any, i: number) => (
                                                        <div key={i} className="p-4 rounded-xl border border-gray-250 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/40 flex flex-col md:flex-row items-start md:items-center gap-4">
                                                            <div className="flex items-center gap-4 flex-shrink-0 min-w-[250px]">
                                                                <div className="w-10 h-10 rounded-lg bg-blue-50 dark:bg-neutral-700 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs uppercase border border-blue-100 dark:border-neutral-600 shadow-sm">
                                                                    {item.code || t('labAbbr')}
                                                                </div>
                                                                <div>
                                                                    <p className="font-bold text-gray-900 dark:text-neutral-100 text-sm">{item.test_name}</p>
                                                                    <p className="text-[9px] font-black uppercase text-gray-400 mt-0.5 tracking-wider">{t('pendingRequest')}</p>
                                                                </div>
                                                            </div>

                                                            <div className="flex-1 w-full grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                <select
                                                                    value={item.urgency}
                                                                    onChange={e => updateLabTest(i, 'urgency', e.target.value)}
                                                                    className={`w-full text-xs font-bold uppercase py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm transition-all ${
                                                                        item.urgency === 'stat' ? 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900' :
                                                                        item.urgency === 'urgent' ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900' :
                                                                        'bg-white dark:bg-neutral-900 text-gray-800 dark:text-neutral-100 border-gray-200 dark:border-neutral-700'
                                                                    }`}
                                                                >
                                                                    <option value="routine">{t('routineUrgency')}</option>
                                                                    <option value="urgent">{t('urgent')}</option>
                                                                    <option value="stat">{t('statImmediate')}</option>
                                                                </select>
                                                                <input
                                                                    type="text"
                                                                    placeholder={t('reasonClinicalIndications')}
                                                                    value={item.reason_for_test}
                                                                    onChange={e => updateLabTest(i, 'reason_for_test', e.target.value)}
                                                                    className="w-full text-xs rounded-lg border border-gray-250 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-gray-900 dark:text-neutral-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none py-2 px-3 shadow-sm transition-all"
                                                                />
                                                            </div>

                                                            <button type="button" onClick={() => removeLabTest(i)} className="p-2.5 text-gray-400 hover:text-red-600 bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 hover:border-red-350 transition-colors shadow-sm">
                                                                <X className="w-4 h-4"/>
                                                            </button>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        </motion.div>
                                    )}

                                    {/* Tab 4: ATTACHMENTS */}
                                    {activeTab === 'files' && (
                                        <motion.div key="files" initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} className="max-w-5xl mx-auto space-y-6">
                                            <label
                                                className="block bg-white dark:bg-neutral-800 p-10 rounded-xl shadow-sm border-2 border-dashed border-gray-200 dark:border-neutral-700 text-center hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50/20 dark:hover:bg-neutral-800 transition-all cursor-pointer group"
                                                onDragOver={(e) => { e.preventDefault(); e.currentTarget.classList.add('border-blue-500', 'bg-blue-50/20'); }}
                                                onDragLeave={(e) => { e.currentTarget.classList.remove('border-blue-500', 'bg-blue-50/20'); }}
                                                onDrop={(e) => {
                                                    e.preventDefault();
                                                    e.currentTarget.classList.remove('border-blue-500', 'bg-blue-50/20');
                                                    if (e.dataTransfer.files?.length) {
                                                        setData('attachments', Array.from(e.dataTransfer.files));
                                                    }
                                                }}
                                            >
                                                <div className="w-14 h-14 rounded-lg bg-blue-50 dark:bg-neutral-700 text-blue-600 dark:text-blue-400 mx-auto flex items-center justify-center mb-4 border border-blue-100 dark:border-neutral-600 group-hover:scale-105 transition-transform shadow-sm">
                                                    <UploadCloud className="w-6 h-6" />
                                                </div>
                                                <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100">{t('uploadExternalDocuments')}</h3>
                                                <p className="text-xs text-gray-500 dark:text-neutral-400 mt-1.5 max-w-sm mx-auto">
                                                    {data.attachments.length > 0
                                                        ? `${data.attachments.length} ${t('fileSSelected')}`
                                                        : t('clickToSelectOrDrop')}
                                                </p>

                                                <input
                                                    type="file"
                                                    multiple
                                                    onChange={e => setData('attachments', Array.from(e.target.files || []))}
                                                    className="hidden"
                                                />
                                            </label>

                                            {data.attachments.length > 0 && (
                                                <div className="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700 overflow-hidden">
                                                    <ul className="divide-y divide-gray-100 dark:divide-neutral-700">
                                                        {Array.from(data.attachments).map((file, i) => (
                                                            <li key={i} className="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-neutral-750 transition-colors">
                                                                <div className="flex items-center gap-3">
                                                                    <FileText className="w-5 h-5 text-blue-500" />
                                                                    <span className="text-sm font-bold text-gray-900 dark:text-neutral-100">{file.name}</span>
                                                                    <span className="text-[10px] font-mono text-gray-400">({(file.size / 1024).toFixed(1)} {t('kb')})</span>
                                                                </div>
                                                                <button
                                                                    type="button"
                                                                    onClick={() => {
                                                                        const files = Array.from(data.attachments);
                                                                        files.splice(i, 1);
                                                                        setData('attachments', files);
                                                                    }}
                                                                    className="text-gray-450 hover:text-red-650 p-1.5 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors"
                                                                >
                                                                    <X className="w-4 h-4" />
                                                                </button>
                                                            </li>
                                                        ))}
                                                    </ul>
                                                </div>
                                            )}
                                        </motion.div>
                                    )}
                                </AnimatePresence>
                            </div>
                        </>
                    ) : (
                        <div className="h-full flex flex-col items-center justify-center opacity-70">
                             <User className="w-16 h-16 mb-4 text-gray-300 dark:text-neutral-700 animate-pulse" />
                             <h3 className="text-sm font-black uppercase tracking-widest text-gray-500 dark:text-neutral-400">{t('selectPatientToBegin')}</h3>
                             <p className="text-xs text-gray-400 dark:text-neutral-500 mt-2">{t('searchAndSelectPatient')}</p>
                        </div>
                    )}
                </main>
            </div>
        </form>
    );
};

ConsultationPage.layout = (page: React.ReactNode) => <AppLayout children={page} headerTitle={t('consultations')} headerSubtitle={t('createMedicalRecord')} />;

export default ConsultationPage;
