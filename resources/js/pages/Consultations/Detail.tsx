import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import {
    HomeIcon, ChevronRightIcon, CalendarDaysIcon, UserIcon, ClipboardDocumentListIcon,
    DocumentTextIcon, BeakerIcon, PaperClipIcon, ArrowLeftIcon,
} from '@heroicons/react/24/outline';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface PrescriptionItem {
    id: number;
    medication: { name: string };
    dosage: string;
    frequency: string;
    duration: string;
    quantity_prescribed: number;
}

interface Prescription {
    id: number;
    prescription_date: string;
    items: PrescriptionItem[];
}

interface LabResult {
    id: number;
    result_date: string;
    status: string;
    results_text: string;
    attachments?: { id: number; file_name: string }[];
}

interface Consultation {
    id: number;
    created_at: string;
    patient: { id: number; first_name: string; last_name: string };
    doctor: { name: string };
    diagnosis: string | null;
    treatment_plan: string | null;
    chief_complaint: string | null;
    physical_exam: string | null;
    notes: string | null;
    prescriptions?: Prescription[];
    lab_results?: LabResult[];
}

interface Props {
    consultation: Consultation;
}

export default function ConsultationDetail({ consultation }: Props) {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState<'prescriptions' | 'lab'>('prescriptions');
    const c = consultation;

    return (
        <AppLayout headerTitle={`${t('consultation')} #${c.id}`} headerSubtitle={c.patient ? `${c.patient.first_name} ${c.patient.last_name}` : t('patient')}>
            <Head title={`${t('consultation')} #${c.id}`} />

            <div className="max-w-5xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer transition-colors">
                            <HomeIcon className="w-4 h-4 mr-1" /> {t('home')}
                        </li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li><Link href="/consultations/create" className="hover:text-blue-600">{t('consultations')}</Link></li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-medium">#{c.id}</li>
                    </ol>
                </nav>

                {/* Back Link */}
                <Link href="/patients" className="inline-flex items-center text-sm text-gray-500 hover:text-blue-600 transition-colors">
                    <ArrowLeftIcon className="w-4 h-4 mr-1" /> {t('backToPatientList')}
                </Link>

                {/* Patient & Doctor Info */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6">
                        <h3 className="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <UserIcon className="w-4 h-4" /> {t('patientInfo')}
                        </h3>
                        {c.patient && (
                            <div className="space-y-2">
                                <p className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                                    <Link href={`/patients/${c.patient.id}`} className="hover:text-blue-600 transition-colors">
                                        {c.patient.first_name} {c.patient.last_name}
                                    </Link>
                                </p>
                                <p className="text-sm text-gray-500">{t('id')}: #{c.patient.id}</p>
                            </div>
                        )}
                    </div>
                    <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6">
                        <h3 className="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <CalendarDaysIcon className="w-4 h-4" /> {t('visitDetails')}
                        </h3>
                        <div className="space-y-2">
                            <p className="text-sm text-gray-500">{t('date')}: <span className="font-semibold text-gray-900 dark:text-neutral-100">{c.created_at}</span></p>
                            <p className="text-sm text-gray-500">{t('doctor')}: <span className="font-semibold text-gray-900 dark:text-neutral-100">{c.doctor?.name ?? t('unknown')}</span></p>
                        </div>
                    </div>
                </div>

                {/* Clinical Details */}
                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6">
                    <h3 className="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 flex items-center gap-2">
                        <DocumentTextIcon className="w-4 h-4" /> {t('clinicalNotes')}
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700 rounded-xl">
                            <p className="text-xs font-semibold text-gray-400 uppercase mb-1">{t('chiefComplaint')}</p>
                            <p className="text-sm text-gray-900 dark:text-neutral-100">{c.chief_complaint || t('na')}</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700 rounded-xl">
                            <p className="text-xs font-semibold text-gray-400 uppercase mb-1">{t('diagnosis')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{c.diagnosis || t('na')}</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700 rounded-xl">
                            <p className="text-xs font-semibold text-gray-400 uppercase mb-1">{t('physicalExam')}</p>
                            <p className="text-sm text-gray-900 dark:text-neutral-100">{c.physical_exam || t('na')}</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700 rounded-xl">
                            <p className="text-xs font-semibold text-gray-400 uppercase mb-1">{t('treatmentPlan')}</p>
                            <p className="text-sm text-gray-900 dark:text-neutral-100">{c.treatment_plan || t('na')}</p>
                        </div>
                    </div>
                    {c.notes && (
                        <div className="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-100 dark:border-yellow-800">
                            <p className="text-xs font-semibold text-yellow-600 uppercase mb-1">{t('notes')}</p>
                            <p className="text-sm text-gray-900 dark:text-neutral-100">{c.notes}</p>
                        </div>
                    )}
                </div>

                {/* Tabs: Prescriptions / Lab Results */}
                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 overflow-hidden">
                    <div className="flex border-b border-gray-100 dark:border-neutral-700">
                        <button onClick={() => setActiveTab('prescriptions')}
                            className={`flex-1 py-3 px-4 text-sm font-semibold transition-colors ${activeTab === 'prescriptions' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 hover:text-gray-700'}`}>
                            <ClipboardDocumentListIcon className="w-4 h-4 inline mr-2" /> {t('prescriptions')}
                        </button>
                        <button onClick={() => setActiveTab('lab')}
                            className={`flex-1 py-3 px-4 text-sm font-semibold transition-colors ${activeTab === 'lab' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 hover:text-gray-700'}`}>
                            <BeakerIcon className="w-4 h-4 inline mr-2" /> {t('labResults')}
                        </button>
                    </div>

                    <div className="p-6">
                        {activeTab === 'prescriptions' && (
                            <div className="space-y-4">
                                {c.prescriptions?.length ? c.prescriptions.map(rx => (
                                    <div key={rx.id} className="p-4 bg-gray-50 dark:bg-neutral-700 rounded-xl">
                                        <p className="text-xs text-gray-400 mb-2">{t('prescribed')}: {rx.prescription_date}</p>
                                        {rx.items?.map(item => (
                                            <div key={item.id} className="flex items-center justify-between py-2 border-b border-gray-100 dark:border-neutral-600 last:border-0">
                                                <div>
                                                    <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100">{item.medication?.name}</p>
                                                    <p className="text-xs text-gray-500">{item.dosage} &middot; {item.frequency} &middot; {item.duration}</p>
                                                </div>
                                                <span className="text-xs font-bold text-blue-600">{t('qty')}: {item.quantity_prescribed}</span>
                                            </div>
                                        ))}
                                    </div>
                                )) : <p className="text-sm text-gray-400">{t('noPrescriptionsRecorded')}</p>}
                            </div>
                        )}

                        {activeTab === 'lab' && (
                            <div className="space-y-4">
                                {c.lab_results?.length ? c.lab_results.map(lab => (
                                    <div key={lab.id} className="p-4 bg-gray-50 dark:bg-neutral-700 rounded-xl">
                                        <div className="flex items-center justify-between mb-2">
                                            <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100">{t('result')} #{lab.id}</p>
                                            <span className={`px-2 py-0.5 rounded-full text-[10px] font-bold ${lab.status === 'Completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}`}>{lab.status}</span>
                                        </div>
                                        <p className="text-xs text-gray-400 mb-1">{lab.result_date}</p>
                                        <p className="text-sm text-gray-900 dark:text-neutral-100">{lab.results_text}</p>
                                        {lab.attachments?.length > 0 && (
                                            <div className="mt-2 flex gap-2">
                                                {lab.attachments.map(att => (
                                                    <span key={att.id} className="inline-flex items-center text-xs text-blue-600">
                                                        <PaperClipIcon className="w-3 h-3 mr-1" /> {att.file_name}
                                                    </span>
                                                ))}
                                            </div>
                                        )}
                                    </div>
                                )) : <p className="text-sm text-gray-400">{t('noLabResultsRecorded')}</p>}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
