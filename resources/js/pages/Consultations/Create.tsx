import {
    BeakerIcon,
    ClipboardDocumentListIcon,
    DocumentTextIcon,
    HeartIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import type React from 'react';
import { Alert, Button } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { AttachmentPanel } from './components/AttachmentPanel';
import { ClinicalNotesPanel } from './components/ClinicalNotesPanel';
import {
    ComplaintAndDiagnosisSection,
    ExamAndPlanSection,
    FollowUpSection,
    HistorySection,
    ReviewOfSystemsSection,
} from './components/ClinicalNotesSection';
import {
    ConsultationSubmitBar,
    ConsultationWorkspaceHeader,
} from './components/ConsultationWorkspaceHeader';
import { FormActionBar } from './components/FormActionBar';
import { LabRequestPanel } from './components/LabRequestPanel';
import { PatientDrawer } from './components/PatientDrawer';
import {
    ConsultationEmptyWorkspace,
    PatientSearchPanel,
} from './components/PatientSearchPanel';
import { PatientBanner, PatientSidebar } from './components/PatientSidebar';
import { PrescriptionPanel } from './components/PrescriptionPanel';
import { TabPanel } from './components/TabPanel';
import type { ConsultationPageProps } from './components/types';
import { useConsultationForm } from './components/useConsultationForm';
import {
    SocialHistorySection,
    VisitInformationSection,
} from './components/VisitInformationSection';
import { VitalsSection } from './components/VitalsSection';

export type {
    Allergy,
    ConsultationForm,
    ConsultationPageProps,
    LabRequestFormItem,
    Medication,
    Patient,
    PrescriptionFormItem,
    ReviewOfSystem,
    VisitType,
} from './components/types';

/**
 * Consultations/Create — the consultation workspace.
 *
 * Form state and handlers live in `useConsultationForm`; every visible block is
 * a component under `components/` composed from the shared UI kit.
 */
const ConsultationPage: React.FC<ConsultationPageProps> = ({
    patients = [],
    medications = [],
    labTests = [],
    patient_allergies = [],
}) => {
    const { t } = useTranslation();
    const consultation = useConsultationForm({
        patients,
        medications,
        labTests,
        patientAllergies: patient_allergies || [],
    });

    const { data, setData, errors, processing, selectedPatient } = consultation;

    const tabs = [
        {
            value: 'clinical',
            label: t('clinicalNotes'),
            icon: ClipboardDocumentListIcon,
        },
        { value: 'rx', label: t('prescriptionPad'), icon: BeakerIcon },
        { value: 'labs', label: t('labRequests'), icon: HeartIcon },
        { value: 'files', label: t('attachments'), icon: DocumentTextIcon },
    ];

    const patientRail = (onClose?: () => void) => (
        <>
            <PatientSearchPanel
                patients={patients}
                query={consultation.patientQuery}
                onQueryChange={consultation.setPatientQuery}
                onSelect={consultation.selectPatient}
                error={errors.patient_id}
                t={t}
            />
            <PatientSidebar
                patient={selectedPatient}
                onClearPatient={consultation.clearPatient}
                onClose={onClose}
                t={t}
            />
        </>
    );

    return (
        <AppLayout
            headerTitle={t('patientConsultation')}
            headerSubtitle={
                selectedPatient
                    ? `${selectedPatient.first_name} ${selectedPatient.last_name}`
                    : undefined
            }
        >
            <Head title={t('consultation')} />

            <form onSubmit={consultation.submit} className="space-y-4">
                {consultation.showSuccess && (
                    <Alert
                        tone="positive"
                        action={
                            <Button
                                variant="ghost"
                                size="icon"
                                icon={XMarkIcon}
                                aria-label={t('cancel')}
                                onClick={() =>
                                    consultation.setShowSuccess(false)
                                }
                            />
                        }
                    >
                        {consultation.flashSuccess || t('consultationSaved')}
                    </Alert>
                )}

                {errors.patient_id && (
                    <Alert tone="critical">{errors.patient_id}</Alert>
                )}

                <div className="flex flex-col gap-4 lg:flex-row">
                    {/* Desktop patient rail */}
                    <aside className="hidden w-72 shrink-0 flex-col overflow-hidden rounded-xl border border-line bg-surface lg:flex xl:w-80">
                        {patientRail()}
                    </aside>

                    <PatientDrawer
                        open={consultation.mobileDrawerOpen}
                        onClose={() => consultation.setMobileDrawerOpen(false)}
                        title={t('searchPatient')}
                        t={t}
                    >
                        {patientRail()}
                    </PatientDrawer>

                    <main className="min-w-0 flex-1">
                        {!selectedPatient ? (
                            <ConsultationEmptyWorkspace
                                onSearch={() =>
                                    consultation.setMobileDrawerOpen(true)
                                }
                                t={t}
                            />
                        ) : (
                            <div className="flex flex-col gap-4">
                                <PatientBanner
                                    patient={selectedPatient}
                                    onOpen={() =>
                                        consultation.setMobileDrawerOpen(true)
                                    }
                                    t={t}
                                />

                                <ConsultationWorkspaceHeader
                                    tabs={tabs}
                                    activeTab={consultation.activeTab}
                                    onTabChange={consultation.setActiveTab}
                                    conflictCount={
                                        consultation.prescriptionConflicts
                                            .length
                                    }
                                    processing={processing}
                                    t={t}
                                />

                                {consultation.activeTab === 'clinical' && (
                                    <TabPanel>
                                        <ClinicalNotesPanel
                                            visitInformation={
                                                <VisitInformationSection
                                                    visitType={data.visit_type}
                                                    consultationDate={
                                                        data.consultation_date
                                                    }
                                                    onVisitTypeChange={(
                                                        value,
                                                    ) =>
                                                        setData(
                                                            'visit_type',
                                                            value,
                                                        )
                                                    }
                                                    onConsultationDateChange={(
                                                        value,
                                                    ) =>
                                                        setData(
                                                            'consultation_date',
                                                            value,
                                                        )
                                                    }
                                                    t={t}
                                                />
                                            }
                                            socialHistory={
                                                <SocialHistorySection
                                                    smokingStatus={
                                                        data.smoking_status
                                                    }
                                                    alcoholUse={
                                                        data.alcohol_use
                                                    }
                                                    occupation={
                                                        data.occupation || ''
                                                    }
                                                    livingSituation={
                                                        data.living_situation ||
                                                        ''
                                                    }
                                                    setData={setData}
                                                    t={t}
                                                />
                                            }
                                            vitals={
                                                <VitalsSection
                                                    data={data}
                                                    setData={setData}
                                                    bmi={consultation.bmi}
                                                    bmiCategory={
                                                        consultation.bmiCategory
                                                    }
                                                    t={t}
                                                />
                                            }
                                            complaints={
                                                <ComplaintAndDiagnosisSection
                                                    chiefComplaint={
                                                        data.chief_complaint
                                                    }
                                                    diagnosis={data.diagnosis}
                                                    chiefComplaintError={
                                                        errors.chief_complaint
                                                    }
                                                    setData={setData}
                                                    t={t}
                                                />
                                            }
                                            history={
                                                <HistorySection
                                                    historyOfPresentIllness={
                                                        data.history_of_present_illness
                                                    }
                                                    setData={setData}
                                                    t={t}
                                                />
                                            }
                                            reviewOfSystems={
                                                <ReviewOfSystemsSection
                                                    selected={
                                                        data.review_of_systems
                                                    }
                                                    onToggle={
                                                        consultation.toggleReviewOfSystem
                                                    }
                                                    t={t}
                                                />
                                            }
                                            examAndPlan={
                                                <ExamAndPlanSection
                                                    physicalExam={
                                                        data.physical_exam
                                                    }
                                                    treatmentPlan={
                                                        data.treatment_plan
                                                    }
                                                    setData={setData}
                                                    t={t}
                                                />
                                            }
                                            followUp={
                                                <FollowUpSection
                                                    followUpDate={
                                                        data.follow_up_date
                                                    }
                                                    followUpInstructions={
                                                        data.follow_up_instructions
                                                    }
                                                    notes={data.notes}
                                                    setData={setData}
                                                    t={t}
                                                />
                                            }
                                        />
                                    </TabPanel>
                                )}

                                {consultation.activeTab === 'rx' && (
                                    <TabPanel className="max-w-5xl">
                                        <PrescriptionPanel
                                            medications={medications}
                                            items={data.prescription_items}
                                            allergies={data.allergies}
                                            conflicts={
                                                consultation.prescriptionConflicts
                                            }
                                            onAdd={consultation.addPrescription}
                                            onUpdate={
                                                consultation.updateMedication
                                            }
                                            onRemove={
                                                consultation.removeMedication
                                            }
                                            t={t}
                                        />
                                    </TabPanel>
                                )}

                                {consultation.activeTab === 'labs' && (
                                    <TabPanel>
                                        <LabRequestPanel
                                            labTests={labTests}
                                            items={data.lab_requests}
                                            onAdd={consultation.addLabTest}
                                            onUpdate={
                                                consultation.updateLabTest
                                            }
                                            onRemove={
                                                consultation.removeLabTest
                                            }
                                            t={t}
                                        />
                                    </TabPanel>
                                )}

                                {consultation.activeTab === 'files' && (
                                    <TabPanel className="max-w-3xl">
                                        <AttachmentPanel
                                            files={data.attachments}
                                            onAdd={consultation.addAttachments}
                                            onRemove={
                                                consultation.removeAttachment
                                            }
                                            t={t}
                                        />
                                    </TabPanel>
                                )}

                                <FormActionBar
                                    processing={processing}
                                    conflictCount={
                                        consultation.prescriptionConflicts
                                            .length
                                    }
                                    missingComplaint={
                                        !data.chief_complaint.trim()
                                    }
                                    missingDiagnosis={!data.diagnosis.trim()}
                                    t={t}
                                />

                                <ConsultationSubmitBar
                                    processing={processing}
                                    t={t}
                                />
                            </div>
                        )}
                    </main>
                </div>
            </form>
        </AppLayout>
    );
};

export default ConsultationPage;
