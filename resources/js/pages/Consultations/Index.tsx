import { Head, Link, router, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Home,
    ChevronRight,
    Cake,
    User,
    Phone,
    ArrowRightLeft,
    ClipboardList,
    Heart,
    Clock,
    Paperclip,
    ArrowRight,
    FileText,
    Activity,
    Loader2,
} from 'lucide-react';
import React, { useState, useMemo } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { format, isToday, isYesterday, parseISO, isValid } from 'date-fns';

interface Patient {
    id: number;
    patient_uid: string;
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string | null;
    age: number;
    photo_url: string | null;
    gender: 'male' | 'female' | 'other';
    address: string | null;
    last_visit_date: string | null;
}
// --- Types ---
interface Props {
    patient: Patient; // Using any for flexibility, but follows your Model structure
}

const PatientProfilePage = ({ patient }: Props) => {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState('history');
    const [confirmAdmission, setConfirmAdmission] = useState(false);
    const { post, processing } = useForm({});

    // --- Dynamic Grouping Helper ---
    const groupDataByDate = (items: any[], dateField: string) => {
        const groups = { today: [], yesterday: [], older: [] };
        if (!items) return groups;

        items.forEach((item) => {
            const dateValue = item[dateField];
            if (!dateValue) return;
            const date = parseISO(dateValue);
            if (!isValid(date)) return;

            if (isToday(date)) groups.today.push(item);
            else if (isYesterday(date)) groups.yesterday.push(item);
            else groups.older.push(item);
        });
        return groups;
    };

    const requestAdmission = () => {
         post(route('request-admission', patient.id), {
                onSuccess: () => setConfirmAdmission(false)
            });
    };
    // Memoize the buckets to prevent re-sorting on every render
    const historyBuckets = useMemo(
        () => groupDataByDate(patient.medical_records || [], 'created_at'),
        [patient.medical_records],
    );

    const vitalsBuckets = useMemo(
        () => groupDataByDate(patient.vitals || [], 'recorded_at'),
        [patient.vitals],
    );

    // Animation Variants (Exact same as original)
    const containerVariants = {
        hidden: { opacity: 0 },
        visible: { opacity: 1, transition: { staggerChildren: 0.1 } },
    };

    const itemVariants = {
        hidden: { opacity: 0, y: 20 },
        visible: { opacity: 1, y: 0 },
    };

    return (
        <div className="-m-8 h-full w-full overflow-y-auto bg-gray-50 font-sans text-gray-600 dark:bg-neutral-900 dark:text-neutral-300">
            <Head
                title={`${patient.first_name} ${patient.last_name} - ${t('profile')}`}
            />

            <div className="mx-auto max-w-7xl p-8 pt-6">
                {/* Breadcrumb - Exact same styles */}
                <nav className="mb-6 flex" aria-label="Breadcrumb">
                    <ol className="inline-flex items-center space-x-2">
                        <li>
                            <Link
                                href="/"
                                className="flex items-center text-[10px] font-black tracking-widest text-gray-400 uppercase transition-colors hover:text-blue-600"
                            >
                                <Home className="mr-1.5 h-3 w-3" /> {t('home')}
                            </Link>
                        </li>
                        <li>
                            <ChevronRight className="h-3 w-3 text-gray-300" />
                        </li>
                        <li>
                            <Link
                                href={route('patients.index')}
                                className="text-[10px] font-black tracking-widest text-gray-400 uppercase hover:text-blue-600"
                            >
                                {t('patients')}
                            </Link>
                        </li>
                        <li>
                            <ChevronRight className="h-3 w-3 text-gray-300" />
                        </li>
                        <li>
                            <span className="text-[10px] font-black tracking-widest text-gray-900 uppercase dark:text-neutral-100">
                                {patient.first_name} {patient.last_name}
                            </span>
                        </li>
                    </ol>
                </nav>

                {/* Patient Header Card - Exact same styles */}
                <motion.div
                    initial={{ opacity: 0, y: -20 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="mb-8 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
                >
                    <div className="flex flex-col items-start justify-between gap-6 lg:flex-row">
                        <div className="flex w-full flex-col items-center gap-6 sm:flex-row sm:items-start">
                            <div className="relative">
                                <div className="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-3xl font-black text-white uppercase shadow-xl ring-4 ring-white dark:ring-neutral-700">
                                    {patient.first_name[0]}
                                    {patient.last_name[0]}
                                </div>
                            </div>

                            <div className="flex-1 text-center sm:text-left">
                                <div className="mb-2 flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <h1 className="text-3xl font-black tracking-tight text-gray-900 uppercase dark:text-neutral-100">
                                        {patient.first_name} {patient.last_name}
                                    </h1>
                                    <span className="inline-flex items-center rounded-lg border border-gray-200 bg-gray-100 px-3 py-1 text-[10px] font-black tracking-widest text-gray-600 uppercase dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                        {patient.patient_uid}
                                    </span>
                                </div>

                                <div className="mt-4 flex flex-wrap justify-center gap-x-8 gap-y-2 text-[10px] font-black tracking-widest text-gray-400 uppercase sm:justify-start">
                                    <div className="flex items-center gap-2">
                                        <Cake className="h-4 w-4" />
                                        <span className="text-gray-900 dark:text-neutral-200">
                                            {patient.age
                                                ? `${patient.age} ${t('yrsOld')}`
                                                : t('ageNa')}
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <User className="h-4 w-4" />
                                        <span className="text-gray-900 uppercase dark:text-neutral-200">
                                            {patient.gender}
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Phone className="h-4 w-4" />
                                        <span className="text-gray-900 dark:text-neutral-200">
                                            {patient.phone || t('noPhone')}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="flex-shrink-0">
                            <button
                                onClick={() => {
                                    requestAdmission();
                                }}
                                className="inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-3 text-[10px] font-black tracking-widest text-white uppercase shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-1 hover:bg-blue-700 active:scale-95"
                            >
                                {processing ? (
                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                ) : (
                                    <ArrowRightLeft className="mr-2 h-4 w-4" />
                                )}
                                {t('requestAdmission')}
                            </button>
                        </div>
                    </div>
                </motion.div>

                {/* Tabs Navigation - Exact same styles */}
                <div className="mb-8 flex space-x-10 border-b border-gray-200 dark:border-neutral-700">
                    {['history', 'vitals'].map((t) => (
                        <button
                            key={t}
                            onClick={() => setActiveTab(t)}
                            className={`relative pb-4 text-[10px] font-black tracking-[0.2em] uppercase transition-colors outline-none ${activeTab === t ? 'text-blue-600' : 'text-gray-400 hover:text-gray-600'}`}
                        >
                            <div className="flex items-center gap-2">
                                {t === 'history' ? (
                                    <ClipboardList className="h-4 w-4" />
                                ) : (
                                    <Heart className="h-4 w-4" />
                                )}
                                {t === 'history'
                                    ? t('consultationHistory')
                                    : t('vitalSigns')}
                            </div>
                            {activeTab === t && (
                                <motion.div
                                    layoutId="tabLine"
                                    className="absolute right-0 bottom-0 left-0 h-1 rounded-t-full bg-blue-600"
                                />
                            )}
                        </button>
                    ))}
                </div>

                {/* TABS CONTENT */}
                <div className="min-h-[400px]">
                    <AnimatePresence mode="wait">
                        {activeTab === 'history' ? (
                            <motion.div
                                key="history"
                                variants={containerVariants}
                                initial="hidden"
                                animate="visible"
                                exit="hidden"
                                className="space-y-12"
                            >
                                {Object.entries(historyBuckets).map(
                                    ([bucket, records]: [string, any]) =>
                                        records.length > 0 && (
                                            <div key={bucket}>
                                                <div className="mb-6 flex items-center">
                                                    <span className="text-[10px] font-black tracking-[0.3em] text-gray-400 uppercase">
                                                        {bucket}
                                                    </span>
                                                    <div className="ml-6 h-px flex-1 bg-gray-200 dark:bg-neutral-700"></div>
                                                </div>

                                                <div className="space-y-6">
                                                    {records.map(
                                                        (record: any) => (
                                                            <motion.div
                                                                key={record.id}
                                                                variants={
                                                                    itemVariants
                                                                }
                                                                className="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-blue-500 dark:border-neutral-700 dark:bg-neutral-800"
                                                            >
                                                                <div className="mb-6 flex items-start justify-between">
                                                                    <div>
                                                                        <h4 className="text-lg font-black tracking-tight text-gray-900 uppercase dark:text-neutral-100">
                                                                            {t('clinicalRecord')}
                                                                        </h4>
                                                                        <p className="mt-1 text-[10px] font-bold text-gray-400 uppercase">
                                                                            {format(
                                                                                parseISO(
                                                                                    record.created_at,
                                                                                ),
                                                                                'MMM dd, yyyy • hh:mm a',
                                                                            )}
                                                                        </p>
                                                                    </div>
                                                                    <span className="rounded-lg border border-gray-200 bg-gray-50 px-3 py-1 text-[10px] font-black tracking-widest text-gray-500 uppercase dark:border-neutral-700 dark:bg-neutral-900">
                                                                        {t('dr')}{' '}
                                                                        {record
                                                                            .doctor
                                                                            ?.name ||
                                                                            t('unknown')}
                                                                    </span>
                                                                </div>

                                                                <div className="grid grid-cols-1 gap-8 md:grid-cols-2">
                                                                    <div>
                                                                        <h5 className="mb-2 text-[10px] font-black tracking-widest text-gray-400 uppercase">
                                                                            {t('diagnosis')}
                                                                        </h5>
                                                                        <p className="rounded-xl border border-blue-100 bg-blue-50 p-3 text-sm font-bold tracking-tight text-blue-700 uppercase dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                                            {record.diagnosis ||
                                                                                t('noDiagnosisRecorded')}
                                                                        </p>
                                                                        <h5 className="mt-4 mb-2 text-[10px] font-black tracking-widest text-gray-400 uppercase">
                                                                            {t('chiefComplaint')}
                                                                        </h5>
                                                                        <p className="text-sm font-medium text-gray-700 italic dark:text-neutral-300">
                                                                            "
                                                                            {
                                                                                record.chief_complaint
                                                                            }
                                                                            "
                                                                        </p>
                                                                    </div>
                                                                    <div>
                                                                        <h5 className="mb-2 text-[10px] font-black tracking-widest text-gray-400 uppercase">
                                                                            {t('treatmentPlan')}
                                                                        </h5>
                                                                        <p className="mb-4 text-sm font-bold tracking-tight text-gray-700 uppercase dark:text-neutral-300">
                                                                            {record.treatment_plan ||
                                                                                t('na')}
                                                                        </p>
                                                                        <h5 className="mb-2 text-[10px] font-black tracking-widest text-gray-400 uppercase">
                                                                            {t('attachments')}
                                                                        </h5>
                                                                        <div className="flex gap-4">
                                                                            {record
                                                                                .attachments
                                                                                ?.length >
                                                                            0 ? (
                                                                                record.attachments.map(
                                                                                    (
                                                                                        att: any,
                                                                                        idx: number,
                                                                                    ) => (
                                                                                        <button
                                                                                            key={
                                                                                                idx
                                                                                            }
                                                                                            className="flex items-center text-[10px] font-black tracking-widest text-gray-400 uppercase transition-colors hover:text-blue-600"
                                                                                        >
                                                                                            <Paperclip className="mr-1.5 h-3 w-3" />{' '}
                                                                                            {t('file')}{' '}
                                                                                            {idx +
                                                                                                1}
                                                                                        </button>
                                                                                    ),
                                                                                )
                                                                            ) : (
                                                                                <span className="text-[10px] font-bold text-gray-400 uppercase italic">
                                                                                    {t('noFiles')}
                                                                                </span>
                                                                            )}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </motion.div>
                                                        ),
                                                    )}
                                                </div>
                                            </div>
                                        ),
                                )}
                            </motion.div>
                        ) : (
                            <motion.div
                                key="vitals"
                                variants={containerVariants}
                                initial="hidden"
                                animate="visible"
                                exit="hidden"
                                className="space-y-12"
                            >
                                {Object.entries(vitalsBuckets).map(
                                    ([bucket, vitals]: [string, any]) =>
                                        vitals.length > 0 && (
                                            <div key={bucket}>
                                                <div className="mb-6 flex items-center">
                                                    <span className="text-[10px] font-black tracking-[0.3em] text-gray-400 uppercase">
                                                        {bucket}
                                                    </span>
                                                    <div className="ml-6 h-px flex-1 bg-gray-200 dark:bg-neutral-700"></div>
                                                </div>

                                                <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                                                    {vitals.map((v: any) => (
                                                        <motion.div
                                                            key={v.id}
                                                            variants={
                                                                itemVariants
                                                            }
                                                            className="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
                                                        >
                                                            <div className="mb-6 flex items-center justify-between">
                                                                <div className="flex items-center text-[10px] font-black tracking-widest text-gray-900 uppercase dark:text-neutral-100">
                                                                    <Clock className="mr-2 h-3.5 w-3.5 text-blue-500" />
                                                                    {format(
                                                                        parseISO(
                                                                            v.recorded_at,
                                                                        ),
                                                                        'hh:mm a',
                                                                    )}
                                                                </div>
                                                                {v.is_abnormal && (
                                                                    <span className="rounded-lg border border-red-200 bg-red-100 px-2 py-0.5 text-[9px] font-black tracking-widest text-red-600 uppercase dark:border-red-800 dark:bg-red-900/40 dark:text-red-400">
                                                                        {t('abnormal')}
                                                                    </span>
                                                                )}
                                                            </div>

                                                            <div className="grid grid-cols-2 gap-x-2 gap-y-4">
                                                                <div className="flex flex-col">
                                                                    <span className="text-[9px] font-black tracking-tighter text-gray-400 uppercase">
                                                                        {t('temperature')}
                                                                    </span>
                                                                    <span
                                                                        className={`text-lg font-black ${v.is_abnormal && Number(v.temperature) > 38 ? 'text-red-500' : 'text-gray-900 dark:text-neutral-100'}`}
                                                                    >
                                                                        {
                                                                            v.temperature
                                                                        }
                                                                        °C
                                                                    </span>
                                                                </div>
                                                                <div className="flex flex-col">
                                                                    <span className="text-[9px] font-black tracking-tighter text-gray-400 uppercase">
                                                                        {t('bloodPressure')}
                                                                    </span>
                                                                    <span className="text-lg font-black text-gray-900 dark:text-neutral-100">
                                                                        {
                                                                            v.systolic_bp
                                                                        }
                                                                        /
                                                                        {
                                                                            v.diastolic_bp
                                                                        }
                                                                    </span>
                                                                </div>
                                                                <div className="flex flex-col">
                                                                    <span className="text-[9px] font-black tracking-tighter text-gray-400 uppercase">
                                                                        {t('heartRate')}
                                                                    </span>
                                                                    <span className="text-lg font-black text-gray-900 dark:text-neutral-100">
                                                                        {
                                                                            v.heart_rate
                                                                        }{' '}
                                                                        <span className="text-[10px] font-medium text-gray-400 uppercase">
                                                                            {t('bpm')}
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div className="flex flex-col">
                                                                    <span className="text-[9px] font-black tracking-tighter text-gray-400 uppercase">
                                                                        {t('oxygenSpo2')}
                                                                    </span>
                                                                    <span className="text-lg font-black text-gray-900 dark:text-neutral-100">
                                                                        {
                                                                            v.oxygen_saturation
                                                                        }
                                                                        %
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div className="mt-6 flex items-center justify-between border-t border-gray-50 pt-4 dark:border-neutral-700">
                                                                <span className="text-[9px] font-bold tracking-widest text-gray-400 uppercase">
                                                                    {t('nurse')}:{' '}
                                                                    {v
                                                                        .recorded_by
                                                                        ?.name ||
                                                                        t('staff')}
                                                                </span>
                                                                <Activity
                                                                    className={`h-4 w-4 ${v.is_abnormal ? 'animate-pulse text-red-500' : 'text-blue-500'}`}
                                                                />
                                                            </div>
                                                        </motion.div>
                                                    ))}
                                                </div>
                                            </div>
                                        ),
                                )}
                            </motion.div>
                        )}
                    </AnimatePresence>
                </div>
            </div>
        </div>
    );
};

// PERSISTENT LAYOUT - Exact same
PatientProfilePage.layout = (page: React.ReactNode) => (
    <AppLayout
        children={page}
        headerTitle={t('patientProfile')}
        headerSubtitle={t('patientMedicalHistoryVitals')}
    />
);

export default PatientProfilePage;
