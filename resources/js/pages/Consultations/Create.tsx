import { Head, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Search,
    Home,
    ChevronRight,
    User,
    Phone,
    AlertTriangle,
    CheckCircle,
    Trash2,
    UploadCloud,
    FileText,
    X,
    Activity,
    Pill,
    Stethoscope,
    FileDigit,
    Plus,
} from 'lucide-react';
import React, { useState, useMemo } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

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

const ConsultationPage = ({
    patients = [],
    medications = [],
    labTests = [],
}: ConsultationPageProps) => {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState('clinical');
    const [patientQuery, setPatientQuery] = useState('');
    const [selectedPatient, setSelectedPatient] = useState<any>(null);

    // Smart Prescription Input State
    const [currentDrugInput, setCurrentDrugInput] = useState('');
    const [matchedDrug, setMatchedDrug] = useState<any>(null);

    const { data, setData, post, processing, errors, reset } =
        useForm<ConsultationForm>({
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
        if (patientQuery.length < 2) {
            return [];
        }

        return patients.filter(
            (p) =>
                `${p.first_name} ${p.last_name}`
                    .toLowerCase()
                    .includes(patientQuery.toLowerCase()) ||
                p.patient_uid?.includes(patientQuery),
        );
    }, [patientQuery, patients]);

    // Drug Search (Autocomplete logic)
    const drugSuggestions = useMemo(() => {
        if (currentDrugInput.length < 2) {
            return [];
        }

        return medications.filter((m: any) =>
            m.name.toLowerCase().includes(currentDrugInput.toLowerCase()),
        );
    }, [currentDrugInput, medications]);

    const handleSelectPatient = (patient: any) => {
        setSelectedPatient(patient);
        setData('patient_id', patient.id);
        setPatientQuery('');
    };

    // --- Medication Logic ---
    const handleAddPrescription = () => {
        if (!currentDrugInput.trim()) {
            return;
        }

        const exactMatch = medications.find(
            (m: any) =>
                m.name.toLowerCase() === currentDrugInput.trim().toLowerCase(),
        );
        const medId = exactMatch
            ? exactMatch.id
            : matchedDrug
              ? matchedDrug.id
              : null;
        const medName = exactMatch
            ? exactMatch.name
            : matchedDrug
              ? matchedDrug.name
              : currentDrugInput.trim();

        setData('prescription_items', [
            ...data.prescription_items,
            {
                medication_id: medId,
                drug_name: medName,
                dosage: '',
                frequency: '',
                duration: '',
                quantity: 1,
                instructions: '',
            },
        ]);

        setCurrentDrugInput('');
        setMatchedDrug(null);
    };

    const removeMedication = (index: number) => {
        const newItems = [...data.prescription_items];
        newItems.splice(index, 1);
        setData('prescription_items', newItems);
    };

    const updateMedication = (
        index: number,
        field: keyof PrescriptionFormItem,
        value: string | number,
    ) => {
        const newItems = [...data.prescription_items];
        newItems[index] = { ...newItems[index], [field]: value };
        setData('prescription_items', newItems);
    };

    // --- Lab Logic ---
    const addLabTest = (id: string) => {
        if (!id) {
            return;
        }

        const test = labTests.find((t: any) => t.id === Number(id));

        if (
            test &&
            !data.lab_requests.find(
                (item: any) => item.test_definition_id === test.id,
            )
        ) {
            setData('lab_requests', [
                ...data.lab_requests,
                {
                    test_definition_id: test.id,
                    test_name: test.test_name,
                    code: test.code,
                    urgency: 'routine',
                    reason_for_test: '',
                },
            ]);
        }
    };

    const removeLabTest = (index: number) => {
        const newItems = [...data.lab_requests];
        newItems.splice(index, 1);
        setData('lab_requests', newItems);
    };

    const updateLabTest = (
        index: number,
        field: keyof LabRequestFormItem,
        value: string,
    ) => {
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
            },
        });
    };

    return (
        <form
            onSubmit={submitConsultation}
            className="-m-8 flex h-[calc(100vh-2rem)] flex-col overflow-hidden bg-gray-50 text-gray-900 dark:bg-neutral-900 dark:text-neutral-100"
        >
            <Head title={t('consultation')} />

            {/* Header section with theme alignment */}
            <header className="z-20 flex-shrink-0 border-b border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div className="flex items-center justify-between px-6 py-4">
                    <div>
                        <nav className="mb-1 flex text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-neutral-500">
                            <ol className="flex items-center space-x-2">
                                <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600 dark:hover:text-blue-400">
                                    <Home className="mr-1.5 h-3.5 w-3.5" />{' '}
                                    {t('home')}
                                </li>
                                <li>
                                    <ChevronRight className="h-3 w-3 text-gray-300 dark:text-neutral-600" />
                                </li>
                                <li className="text-blue-600 dark:text-blue-400">
                                    {t('consultation')}
                                </li>
                            </ol>
                        </nav>
                        <h1 className="text-xl font-bold tracking-tight">
                            {t('patientConsultation')}
                        </h1>
                    </div>
                </div>
            </header>

            <div className="flex flex-1 overflow-hidden">
                {/* LEFT SIDEBAR - Patient Selection & Demographics */}
                <aside className="z-10 flex h-full w-full flex-shrink-0 flex-col border-r border-gray-200 bg-white md:w-80 lg:w-96 dark:border-neutral-700 dark:bg-neutral-800">
                    <div className="relative border-b border-gray-200 bg-gray-50/50 p-4 dark:border-neutral-700 dark:bg-neutral-800/50">
                        <label className="mb-2 block text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-neutral-400">
                            {t('searchPatient')}
                        </label>
                        <div className="relative">
                            <Search className="absolute top-2.5 left-3 h-4 w-4 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                value={patientQuery}
                                onChange={(e) =>
                                    setPatientQuery(e.target.value)
                                }
                                className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:placeholder-neutral-500"
                                placeholder={t('nameOrId')}
                            />

                            <AnimatePresence>
                                {searchResults.length > 0 && (
                                    <motion.div
                                        initial={{ opacity: 0, y: 5 }}
                                        animate={{ opacity: 1, y: 0 }}
                                        exit={{ opacity: 0, y: 5 }}
                                        className="absolute top-full right-0 left-0 z-50 mt-1.5 max-h-64 overflow-hidden overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-neutral-700 dark:bg-neutral-800"
                                    >
                                        {searchResults.map((p: any) => (
                                            <button
                                                key={p.id}
                                                type="button"
                                                onClick={() =>
                                                    handleSelectPatient(p)
                                                }
                                                className="flex w-full items-center gap-3 border-b border-gray-100 px-4 py-3 text-left transition-colors last:border-0 hover:bg-blue-50 dark:border-neutral-700/50 dark:hover:bg-neutral-700"
                                            >
                                                <div className="flex h-8 w-8 items-center justify-center rounded-lg border border-blue-100 bg-blue-50 text-xs font-bold text-blue-600 shadow-sm dark:border-blue-900/30 dark:bg-blue-900/20 dark:text-blue-400">
                                                    {p.first_name?.[0]}
                                                    {p.last_name?.[0]}
                                                </div>
                                                <div>
                                                    <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                        {p.first_name}{' '}
                                                        {p.last_name}
                                                    </div>
                                                    <div className="font-mono text-[10px] text-gray-500 dark:text-neutral-400">
                                                        {p.patient_uid ||
                                                            t('noUid')}
                                                    </div>
                                                </div>
                                            </button>
                                        ))}
                                    </motion.div>
                                )}
                            </AnimatePresence>
                        </div>
                        {errors.patient_id && (
                            <p className="mt-2 text-xs font-semibold text-red-500">
                                {errors.patient_id}
                            </p>
                        )}
                    </div>

                    {/* Patient Information Panel */}
                    <div className="flex-1 overflow-y-auto bg-white p-5 dark:bg-neutral-800">
                        <AnimatePresence mode="wait">
                            {selectedPatient ? (
                                <motion.div
                                    key={selectedPatient.id}
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    exit={{ opacity: 0 }}
                                    className="space-y-6"
                                >
                                    <div className="flex flex-col items-center text-center">
                                        <div className="mb-3 flex h-16 w-16 items-center justify-center rounded-xl border-2 border-white bg-blue-600 text-xl font-bold text-white shadow-md dark:border-neutral-700 dark:bg-blue-500">
                                            {selectedPatient.first_name?.[0]}
                                            {selectedPatient.last_name?.[0]}
                                        </div>
                                        <h2 className="text-lg font-bold text-gray-950 dark:text-neutral-50">
                                            {selectedPatient.first_name}{' '}
                                            {selectedPatient.last_name}
                                        </h2>
                                        <span className="mt-2 inline-flex items-center rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-700 shadow-sm dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                            {selectedPatient.dob
                                                ? new Date().getFullYear() -
                                                  new Date(
                                                      selectedPatient.dob,
                                                  ).getFullYear() +
                                                  ' ' +
                                                  t('yrs')
                                                : t('na')}{' '}
                                            •{' '}
                                            {selectedPatient.gender ||
                                                t('unknown')}
                                        </span>
                                    </div>
                                    <div className="space-y-3">
                                        <div className="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900/50">
                                            <span className="flex items-center gap-1.5 text-[10px] font-black tracking-wider text-gray-400 uppercase dark:text-neutral-500">
                                                <Phone className="h-3.5 w-3.5" />{' '}
                                                {t('contact')}
                                            </span>
                                            <p className="mt-1.5 text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                                {selectedPatient.phone_number ||
                                                    t('noContactProvided')}
                                            </p>
                                        </div>
                                        {selectedPatient.allergies && (
                                            <div className="rounded-xl border border-red-100 bg-red-50 p-4 shadow-sm dark:border-red-900/30 dark:bg-red-950/20">
                                                <span className="flex items-center gap-1.5 text-[10px] font-black tracking-wider text-red-600 uppercase dark:text-red-400">
                                                    <AlertTriangle className="h-3.5 w-3.5" />{' '}
                                                    {t('allergies')}
                                                </span>
                                                <p className="mt-1.5 text-sm font-semibold text-red-800 dark:text-red-300">
                                                    {selectedPatient.allergies}
                                                </p>
                                            </div>
                                        )}
                                    </div>
                                </motion.div>
                            ) : (
                                <div className="flex h-full flex-col items-center justify-center text-center text-gray-400 opacity-70 dark:text-neutral-500">
                                    <User className="mb-3 h-12 w-12" />
                                    <p className="text-xs font-black tracking-widest uppercase">
                                        {t('selectPatient')}
                                    </p>
                                </div>
                            )}
                        </AnimatePresence>
                    </div>
                </aside>

                {/* RIGHT MAIN WORKSPACE */}
                <main className="relative flex h-full min-w-0 flex-1 flex-col bg-gray-50 dark:bg-neutral-900">
                    {selectedPatient ? (
                        <>
                            {/* Tabs Header with Cohesive Dark/Light Indicators */}
                            <div className="z-10 flex flex-shrink-0 items-center justify-between border-b border-gray-200 bg-white px-6 pt-3 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                <div className="no-scrollbar flex space-x-6 overflow-x-auto">
                                    {[
                                        {
                                            id: 'clinical',
                                            label: t('clinicalNotes'),
                                            icon: Stethoscope,
                                        },
                                        {
                                            id: 'rx',
                                            label: t('prescriptionPad'),
                                            icon: Pill,
                                        },
                                        {
                                            id: 'labs',
                                            label: t('labRequests'),
                                            icon: Activity,
                                        },
                                        {
                                            id: 'files',
                                            label: t('attachments'),
                                            icon: FileDigit,
                                        },
                                    ].map((tab) => (
                                        <button
                                            key={tab.id}
                                            type="button"
                                            onClick={() => setActiveTab(tab.id)}
                                            className={`relative flex items-center gap-2 px-1 pb-3 text-xs font-black tracking-wider uppercase transition-colors outline-none ${activeTab === tab.id ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 hover:text-gray-900 dark:text-neutral-400 dark:hover:text-neutral-200'}`}
                                        >
                                            <tab.icon className="h-4 w-4" />
                                            {tab.label}
                                            {activeTab === tab.id && (
                                                <motion.div
                                                    layoutId="activeTab"
                                                    className="absolute right-0 bottom-0 left-0 h-[2.5px] rounded-t-md bg-blue-600 dark:bg-blue-400"
                                                />
                                            )}
                                        </button>
                                    ))}
                                </div>
                                <div className="flex items-center gap-3 pb-2.5">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-xs font-black tracking-wider text-white uppercase shadow-sm transition-all hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        {processing ? (
                                            <span className="flex items-center gap-2">
                                                <Activity className="h-4 w-4 animate-spin" />{' '}
                                                {t('processing')}...
                                            </span>
                                        ) : (
                                            <>
                                                <CheckCircle className="h-4 w-4" />{' '}
                                                {t('saveRecord')}
                                            </>
                                        )}
                                    </button>
                                </div>
                            </div>

                            {/* Scrollable Workspace Tab Views */}
                            <div className="flex-1 overflow-y-auto bg-gray-50 p-6 dark:bg-neutral-900">
                                <AnimatePresence mode="wait">
                                    {/* Tab 1: CLINICAL NOTES */}
                                    {activeTab === 'clinical' && (
                                        <motion.div
                                            key="clinical"
                                            initial={{ opacity: 0, y: 10 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            exit={{ opacity: 0, y: -10 }}
                                            className="mx-auto max-w-4xl space-y-6"
                                        >
                                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                                <div className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                                    <label className="mb-2 block text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-neutral-400">
                                                        {t('chiefComplaint')}{' '}
                                                        <span className="text-red-500">
                                                            *
                                                        </span>
                                                    </label>
                                                    <textarea
                                                        value={
                                                            data.chief_complaint
                                                        }
                                                        onChange={(e) =>
                                                            setData(
                                                                'chief_complaint',
                                                                e.target.value,
                                                            )
                                                        }
                                                        rows={3}
                                                        className="dark:border-neutral-750 w-full rounded-lg border border-gray-200 p-3 text-sm shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900"
                                                        placeholder={t(
                                                            'primaryReasonForVisit',
                                                        )}
                                                    />
                                                    {errors.chief_complaint && (
                                                        <p className="mt-1 text-[10px] font-bold text-red-500 uppercase">
                                                            {
                                                                errors.chief_complaint
                                                            }
                                                        </p>
                                                    )}
                                                </div>

                                                <div className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                                    <label className="mb-2 block text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-neutral-400">
                                                        {t('diagnosis')}
                                                    </label>
                                                    <textarea
                                                        value={data.diagnosis}
                                                        onChange={(e) =>
                                                            setData(
                                                                'diagnosis',
                                                                e.target.value,
                                                            )
                                                        }
                                                        rows={3}
                                                        className="dark:border-neutral-750 w-full rounded-lg border border-gray-200 p-3 text-sm shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900"
                                                        placeholder={t(
                                                            'clinicalDiagnosis',
                                                        )}
                                                    />
                                                </div>
                                            </div>

                                            <div className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                                <label className="mb-2 block text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-neutral-400">
                                                    {t(
                                                        'historyOfPresentIllness',
                                                    )}
                                                </label>
                                                <textarea
                                                    value={
                                                        data.history_of_present_illness
                                                    }
                                                    onChange={(e) =>
                                                        setData(
                                                            'history_of_present_illness',
                                                            e.target.value,
                                                        )
                                                    }
                                                    rows={4}
                                                    className="dark:border-neutral-750 w-full rounded-lg border border-gray-200 p-3 text-sm shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900"
                                                    placeholder={t(
                                                        'detailedPresentation',
                                                    )}
                                                />
                                            </div>

                                            <div className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                                <label className="mb-2 block text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-neutral-400">
                                                    {t('physicalExamination')}
                                                </label>
                                                <textarea
                                                    value={data.physical_exam}
                                                    onChange={(e) =>
                                                        setData(
                                                            'physical_exam',
                                                            e.target.value,
                                                        )
                                                    }
                                                    rows={4}
                                                    className="dark:border-neutral-750 w-full rounded-lg border border-gray-200 p-3 text-sm shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900"
                                                    placeholder={t(
                                                        'vitalsPhysicalFindings',
                                                    )}
                                                />
                                            </div>
                                        </motion.div>
                                    )}

                                    {/* Tab 2: PRESCRIPTIONS TAB */}
                                    {activeTab === 'rx' && (
                                        <motion.div
                                            key="rx"
                                            initial={{ opacity: 0, y: 10 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            className="mx-auto max-w-5xl space-y-6"
                                        >
                                            {/* Prescription Input Area */}
                                            <div className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                                <label className="mb-3 block text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-neutral-400">
                                                    {t(
                                                        'addMedicationToPrescription',
                                                    )}
                                                </label>
                                                <div className="relative flex flex-col gap-3 sm:flex-row">
                                                    <div className="relative flex-1">
                                                        <input
                                                            type="text"
                                                            value={
                                                                currentDrugInput
                                                            }
                                                            onChange={(e) => {
                                                                setCurrentDrugInput(
                                                                    e.target
                                                                        .value,
                                                                );
                                                                setMatchedDrug(
                                                                    null,
                                                                );
                                                            }}
                                                            onKeyDown={(e) =>
                                                                e.key ===
                                                                    'Enter' &&
                                                                (e.preventDefault(),
                                                                handleAddPrescription())
                                                            }
                                                            placeholder={t(
                                                                'typeDrugName',
                                                            )}
                                                            className="w-full rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900"
                                                        />

                                                        {/* Dynamic Drug Autocomplete dropdown */}
                                                        {drugSuggestions.length >
                                                            0 &&
                                                            !matchedDrug && (
                                                                <div className="absolute top-full right-0 left-0 z-50 mt-1.5 max-h-48 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                                                                    {drugSuggestions.map(
                                                                        (
                                                                            m: any,
                                                                        ) => (
                                                                            <button
                                                                                key={
                                                                                    m.id
                                                                                }
                                                                                type="button"
                                                                                onClick={() => {
                                                                                    setCurrentDrugInput(
                                                                                        m.name,
                                                                                    );
                                                                                    setMatchedDrug(
                                                                                        m,
                                                                                    );
                                                                                }}
                                                                                className="w-full border-b border-gray-100 px-4 py-2.5 text-left text-sm last:border-0 hover:bg-blue-50 dark:border-neutral-700/55 dark:hover:bg-neutral-700"
                                                                            >
                                                                                {
                                                                                    m.name
                                                                                }{' '}
                                                                                <span className="ml-2 text-[10px] font-black text-green-600 uppercase dark:text-green-400">
                                                                                    {t(
                                                                                        'systemMatch',
                                                                                    )}
                                                                                </span>
                                                                            </button>
                                                                        ),
                                                                    )}
                                                                </div>
                                                            )}
                                                    </div>
                                                    <button
                                                        type="button"
                                                        onClick={
                                                            handleAddPrescription
                                                        }
                                                        className="flex items-center justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-xs font-black tracking-wider whitespace-nowrap text-white uppercase shadow-sm transition-colors hover:bg-blue-700"
                                                    >
                                                        <Plus className="mr-1 h-4 w-4" />{' '}
                                                        {t('addMedication')}
                                                    </button>
                                                </div>
                                                <p className="mt-2 text-[10px] font-medium text-gray-400 dark:text-neutral-500">
                                                    {t(
                                                        'unrecognizedItemsExternal',
                                                    )}
                                                </p>
                                            </div>

                                            {/* Prescriptions Items Table */}
                                            {data.prescription_items.length >
                                                0 && (
                                                <div className="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                                    <table className="w-full divide-y divide-gray-200 text-left whitespace-nowrap dark:divide-neutral-700">
                                                        <thead className="bg-gray-50 text-xs font-bold tracking-wider text-gray-500 uppercase dark:bg-neutral-700 dark:text-neutral-500">
                                                            <tr>
                                                                <th className="px-6 py-4">
                                                                    {t(
                                                                        'drugName',
                                                                    )}
                                                                </th>
                                                                <th className="px-4 py-4">
                                                                    {t(
                                                                        'dosage',
                                                                    )}
                                                                </th>
                                                                <th className="px-4 py-4">
                                                                    {t(
                                                                        'frequency',
                                                                    )}
                                                                </th>
                                                                <th className="px-4 py-4">
                                                                    {t(
                                                                        'duration',
                                                                    )}
                                                                </th>
                                                                <th className="w-20 px-4 py-4">
                                                                    {t('qty')}
                                                                </th>
                                                                <th className="px-4 py-4">
                                                                    {t(
                                                                        'instructions',
                                                                    )}
                                                                </th>
                                                                <th className="px-6 py-4"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                                            {data.prescription_items.map(
                                                                (
                                                                    item: any,
                                                                    i: number,
                                                                ) => (
                                                                    <tr
                                                                        key={i}
                                                                        className="dark:hover:bg-neutral-750 transition-colors hover:bg-gray-50"
                                                                    >
                                                                        <td className="px-6 py-4">
                                                                            <div className="flex flex-col gap-1">
                                                                                <span className="font-bold text-gray-900 dark:text-neutral-100">
                                                                                    {
                                                                                        item.drug_name
                                                                                    }
                                                                                </span>
                                                                                {!item.medication_id && (
                                                                                    <span className="inline-block max-w-max rounded border border-amber-100 bg-amber-50 px-2 py-0.5 text-[9px] font-black tracking-wider text-amber-600 uppercase dark:border-amber-950 dark:bg-amber-900/20 dark:text-amber-400">
                                                                                        {t(
                                                                                            'external',
                                                                                        )}
                                                                                    </span>
                                                                                )}
                                                                            </div>
                                                                        </td>
                                                                        <td className="px-4 py-3">
                                                                            <input
                                                                                type="text"
                                                                                placeholder={t(
                                                                                    'egOneTab',
                                                                                )}
                                                                                value={
                                                                                    item.dosage
                                                                                }
                                                                                onChange={(
                                                                                    e,
                                                                                ) =>
                                                                                    updateMedication(
                                                                                        i,
                                                                                        'dosage',
                                                                                        e
                                                                                            .target
                                                                                            .value,
                                                                                    )
                                                                                }
                                                                                className="w-full rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-xs shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900"
                                                                            />
                                                                        </td>
                                                                        <td className="px-4 py-3">
                                                                            <input
                                                                                type="text"
                                                                                placeholder={t(
                                                                                    'egThreeTimesDay',
                                                                                )}
                                                                                value={
                                                                                    item.frequency
                                                                                }
                                                                                onChange={(
                                                                                    e,
                                                                                ) =>
                                                                                    updateMedication(
                                                                                        i,
                                                                                        'frequency',
                                                                                        e
                                                                                            .target
                                                                                            .value,
                                                                                    )
                                                                                }
                                                                                className="w-full rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-xs shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900"
                                                                            />
                                                                        </td>
                                                                        <td className="px-4 py-3">
                                                                            <input
                                                                                type="text"
                                                                                placeholder={t(
                                                                                    'egFiveDays',
                                                                                )}
                                                                                value={
                                                                                    item.duration
                                                                                }
                                                                                onChange={(
                                                                                    e,
                                                                                ) =>
                                                                                    updateMedication(
                                                                                        i,
                                                                                        'duration',
                                                                                        e
                                                                                            .target
                                                                                            .value,
                                                                                    )
                                                                                }
                                                                                className="w-full rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-xs shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900"
                                                                            />
                                                                        </td>
                                                                        <td className="px-4 py-3">
                                                                            <input
                                                                                type="number"
                                                                                min="1"
                                                                                value={
                                                                                    item.quantity
                                                                                }
                                                                                onChange={(
                                                                                    e,
                                                                                ) =>
                                                                                    updateMedication(
                                                                                        i,
                                                                                        'quantity',
                                                                                        parseInt(
                                                                                            e
                                                                                                .target
                                                                                                .value,
                                                                                        ),
                                                                                    )
                                                                                }
                                                                                className="w-full rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-xs shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900"
                                                                            />
                                                                        </td>
                                                                        <td className="px-4 py-3">
                                                                            <input
                                                                                type="text"
                                                                                placeholder={t(
                                                                                    'egAfterMeals',
                                                                                )}
                                                                                value={
                                                                                    item.instructions
                                                                                }
                                                                                onChange={(
                                                                                    e,
                                                                                ) =>
                                                                                    updateMedication(
                                                                                        i,
                                                                                        'instructions',
                                                                                        e
                                                                                            .target
                                                                                            .value,
                                                                                    )
                                                                                }
                                                                                className="w-full rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-xs shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900"
                                                                            />
                                                                        </td>
                                                                        <td className="px-6 py-3 text-right">
                                                                            <button
                                                                                type="button"
                                                                                onClick={() =>
                                                                                    removeMedication(
                                                                                        i,
                                                                                    )
                                                                                }
                                                                                className="rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40"
                                                                            >
                                                                                <Trash2 className="h-4 w-4" />
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                ),
                                                            )}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            )}
                                        </motion.div>
                                    )}

                                    {/* Tab 3: LAB REQUESTS */}
                                    {activeTab === 'labs' && (
                                        <motion.div
                                            key="labs"
                                            initial={{ opacity: 0, y: 10 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            className="mx-auto max-w-5xl space-y-6"
                                        >
                                            <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                                <div className="mb-6 flex gap-4">
                                                    <select
                                                        onChange={(e) => {
                                                            addLabTest(
                                                                e.target.value,
                                                            );
                                                            e.target.value = '';
                                                        }}
                                                        className="flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm font-medium text-gray-900 shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100"
                                                    >
                                                        <option value="">
                                                            {t(
                                                                'orderNewLabTest',
                                                            )}
                                                        </option>
                                                        {labTests.map(
                                                            (t: any) => (
                                                                <option
                                                                    key={t.id}
                                                                    value={t.id}
                                                                >
                                                                    {
                                                                        t.test_name
                                                                    }
                                                                </option>
                                                            ),
                                                        )}
                                                    </select>
                                                </div>

                                                <div className="space-y-4">
                                                    {data.lab_requests.map(
                                                        (
                                                            item: any,
                                                            i: number,
                                                        ) => (
                                                            <div
                                                                key={i}
                                                                className="border-gray-250 flex flex-col items-start gap-4 rounded-xl border bg-gray-50 p-4 md:flex-row md:items-center dark:border-neutral-700 dark:bg-neutral-800/40"
                                                            >
                                                                <div className="flex min-w-[250px] flex-shrink-0 items-center gap-4">
                                                                    <div className="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-100 bg-blue-50 text-xs font-bold text-blue-600 uppercase shadow-sm dark:border-neutral-600 dark:bg-neutral-700 dark:text-blue-400">
                                                                        {item.code ||
                                                                            t(
                                                                                'labAbbr',
                                                                            )}
                                                                    </div>
                                                                    <div>
                                                                        <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                                            {
                                                                                item.test_name
                                                                            }
                                                                        </p>
                                                                        <p className="mt-0.5 text-[9px] font-black tracking-wider text-gray-400 uppercase">
                                                                            {t(
                                                                                'pendingRequest',
                                                                            )}
                                                                        </p>
                                                                    </div>
                                                                </div>

                                                                <div className="grid w-full flex-1 grid-cols-1 gap-3 md:grid-cols-2">
                                                                    <select
                                                                        value={
                                                                            item.urgency
                                                                        }
                                                                        onChange={(
                                                                            e,
                                                                        ) =>
                                                                            updateLabTest(
                                                                                i,
                                                                                'urgency',
                                                                                e
                                                                                    .target
                                                                                    .value,
                                                                            )
                                                                        }
                                                                        className={`w-full rounded-lg border py-2 text-xs font-bold uppercase shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 ${
                                                                            item.urgency ===
                                                                            'stat'
                                                                                ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950/20 dark:text-red-400'
                                                                                : item.urgency ===
                                                                                    'urgent'
                                                                                  ? 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950/20 dark:text-amber-400'
                                                                                  : 'border-gray-200 bg-white text-gray-800 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100'
                                                                        }`}
                                                                    >
                                                                        <option value="routine">
                                                                            {t(
                                                                                'routineUrgency',
                                                                            )}
                                                                        </option>
                                                                        <option value="urgent">
                                                                            {t(
                                                                                'urgent',
                                                                            )}
                                                                        </option>
                                                                        <option value="stat">
                                                                            {t(
                                                                                'statImmediate',
                                                                            )}
                                                                        </option>
                                                                    </select>
                                                                    <input
                                                                        type="text"
                                                                        placeholder={t(
                                                                            'reasonClinicalIndications',
                                                                        )}
                                                                        value={
                                                                            item.reason_for_test
                                                                        }
                                                                        onChange={(
                                                                            e,
                                                                        ) =>
                                                                            updateLabTest(
                                                                                i,
                                                                                'reason_for_test',
                                                                                e
                                                                                    .target
                                                                                    .value,
                                                                            )
                                                                        }
                                                                        className="border-gray-250 w-full rounded-lg border bg-white px-3 py-2 text-xs text-gray-900 shadow-sm transition-all outline-none focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100"
                                                                    />
                                                                </div>

                                                                <button
                                                                    type="button"
                                                                    onClick={() =>
                                                                        removeLabTest(
                                                                            i,
                                                                        )
                                                                    }
                                                                    className="hover:border-red-350 rounded-lg border border-gray-200 bg-white p-2.5 text-gray-400 shadow-sm transition-colors hover:text-red-600 dark:border-neutral-700 dark:bg-neutral-800"
                                                                >
                                                                    <X className="h-4 w-4" />
                                                                </button>
                                                            </div>
                                                        ),
                                                    )}
                                                </div>
                                            </div>
                                        </motion.div>
                                    )}

                                    {/* Tab 4: ATTACHMENTS */}
                                    {activeTab === 'files' && (
                                        <motion.div
                                            key="files"
                                            initial={{ opacity: 0, y: 10 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            className="mx-auto max-w-5xl space-y-6"
                                        >
                                            <label
                                                className="group block cursor-pointer rounded-xl border-2 border-dashed border-gray-200 bg-white p-10 text-center shadow-sm transition-all hover:border-blue-500 hover:bg-blue-50/20 dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-blue-400 dark:hover:bg-neutral-800"
                                                onDragOver={(e) => {
                                                    e.preventDefault();
                                                    e.currentTarget.classList.add(
                                                        'border-blue-500',
                                                        'bg-blue-50/20',
                                                    );
                                                }}
                                                onDragLeave={(e) => {
                                                    e.currentTarget.classList.remove(
                                                        'border-blue-500',
                                                        'bg-blue-50/20',
                                                    );
                                                }}
                                                onDrop={(e) => {
                                                    e.preventDefault();
                                                    e.currentTarget.classList.remove(
                                                        'border-blue-500',
                                                        'bg-blue-50/20',
                                                    );

                                                    if (
                                                        e.dataTransfer.files
                                                            ?.length
                                                    ) {
                                                        setData(
                                                            'attachments',
                                                            Array.from(
                                                                e.dataTransfer
                                                                    .files,
                                                            ),
                                                        );
                                                    }
                                                }}
                                            >
                                                <div className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-lg border border-blue-100 bg-blue-50 text-blue-600 shadow-sm transition-transform group-hover:scale-105 dark:border-neutral-600 dark:bg-neutral-700 dark:text-blue-400">
                                                    <UploadCloud className="h-6 w-6" />
                                                </div>
                                                <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                    {t(
                                                        'uploadExternalDocuments',
                                                    )}
                                                </h3>
                                                <p className="mx-auto mt-1.5 max-w-sm text-xs text-gray-500 dark:text-neutral-400">
                                                    {data.attachments.length > 0
                                                        ? `${data.attachments.length} ${t('fileSSelected')}`
                                                        : t(
                                                              'clickToSelectOrDrop',
                                                          )}
                                                </p>

                                                <input
                                                    type="file"
                                                    multiple
                                                    onChange={(e) =>
                                                        setData(
                                                            'attachments',
                                                            Array.from(
                                                                e.target
                                                                    .files ||
                                                                    [],
                                                            ),
                                                        )
                                                    }
                                                    className="hidden"
                                                />
                                            </label>

                                            {data.attachments.length > 0 && (
                                                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                                    <ul className="divide-y divide-gray-100 dark:divide-neutral-700">
                                                        {Array.from(
                                                            data.attachments,
                                                        ).map((file, i) => (
                                                            <li
                                                                key={i}
                                                                className="dark:hover:bg-neutral-750 flex items-center justify-between px-5 py-3 transition-colors hover:bg-gray-50"
                                                            >
                                                                <div className="flex items-center gap-3">
                                                                    <FileText className="h-5 w-5 text-blue-500" />
                                                                    <span className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                                        {
                                                                            file.name
                                                                        }
                                                                    </span>
                                                                    <span className="font-mono text-[10px] text-gray-400">
                                                                        (
                                                                        {(
                                                                            file.size /
                                                                            1024
                                                                        ).toFixed(
                                                                            1,
                                                                        )}{' '}
                                                                        {t(
                                                                            'kb',
                                                                        )}
                                                                        )
                                                                    </span>
                                                                </div>
                                                                <button
                                                                    type="button"
                                                                    onClick={() => {
                                                                        const files =
                                                                            Array.from(
                                                                                data.attachments,
                                                                            );
                                                                        files.splice(
                                                                            i,
                                                                            1,
                                                                        );
                                                                        setData(
                                                                            'attachments',
                                                                            files,
                                                                        );
                                                                    }}
                                                                    className="text-gray-450 hover:text-red-650 rounded-lg p-1.5 transition-colors hover:bg-red-50 dark:hover:bg-red-950/40"
                                                                >
                                                                    <X className="h-4 w-4" />
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
                        <div className="flex h-full flex-col items-center justify-center opacity-70">
                            <User className="mb-4 h-16 w-16 animate-pulse text-gray-300 dark:text-neutral-700" />
                            <h3 className="text-sm font-black tracking-widest text-gray-500 uppercase dark:text-neutral-400">
                                {t('selectPatientToBegin')}
                            </h3>
                            <p className="mt-2 text-xs text-gray-400 dark:text-neutral-500">
                                {t('searchAndSelectPatient')}
                            </p>
                        </div>
                    )}
                </main>
            </div>
        </form>
    );
};

ConsultationPage.layout = (page: React.ReactNode) => (
    <AppLayout
        children={page}
        headerTitle="Consultations"
        headerSubtitle="Create Medical Record"
    />
);

export default ConsultationPage;
