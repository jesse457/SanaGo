import { useForm, usePage } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import { toast } from 'sonner';
import { useTranslation } from '@/hooks/useTranslation';
import {
    calculateBMI,
    checkDrugAllergyInteraction,
    getBMICategory,
} from './helpers';
import type {
    Allergy,
    ConsultationForm,
    LabRequestFormItem,
    Medication,
    Patient,
    PrescriptionFormItem,
    ReviewOfSystem,
} from './types';

interface UseConsultationFormArgs {
    patients: Patient[];
    medications: Medication[];
    labTests: { id: number; test_name: string; code: string }[];
    patientAllergies: Allergy[];
}

/**
 * useConsultationForm — every piece of state and every handler the consultation
 * workspace needs, kept out of the page so the page file is composition only.
 *
 * The submitted payload, the field names and the validation order are unchanged
 * from the original inline implementation.
 */
export function useConsultationForm({
    medications,
    labTests,
    patientAllergies,
}: UseConsultationFormArgs) {
    const { t } = useTranslation();
    const { flash } = usePage<{ flash?: { success?: string } }>().props;

    const [showSuccess, setShowSuccess] = useState(!!flash?.success);
    const [activeTab, setActiveTab] = useState('clinical');

    // Success feedback auto-dismisses; the layout owns the toast host.
    useEffect(() => {
        if (!showSuccess) {
            return;
        }

        const timer = setTimeout(() => setShowSuccess(false), 4000);

        return () => clearTimeout(timer);
    }, [showSuccess]);
    const [patientQuery, setPatientQuery] = useState('');
    const [selectedPatient, setSelectedPatient] = useState<Patient | null>(
        null,
    );
    const [mobileDrawerOpen, setMobileDrawerOpen] = useState(false);

    const { data, setData, post, processing, errors, reset } =
        useForm<ConsultationForm>({
            patient_id: '',
            visit_type: '',
            consultation_date: new Date().toISOString().slice(0, 16),
            chief_complaint: '',
            history_of_present_illness: '',
            review_of_systems: [],
            smoking_status: '',
            alcohol_use: '',
            occupation: '',
            living_situation: '',
            temperature: '',
            systolic_bp: '',
            diastolic_bp: '',
            heart_rate: '',
            oxygen_saturation: '',
            respiratory_rate: '',
            pain_score: '',
            weight_kg: '',
            height_cm: '',
            physical_exam: '',
            diagnosis: '',
            differential_diagnoses: '',
            severity_assessment: '',
            treatment_plan: '',
            allergies: patientAllergies || [],
            new_allergies: [],
            allergy_notes: '',
            follow_up_date: '',
            follow_up_instructions: '',
            notes: '',
            patient_education: '',
            return_precautions: '',
            prescription_items: [],
            lab_requests: [],
            attachments: [],
            consultation_duration: undefined,
            supervising_provider: '',
        });

    // BMI is derived from the vitals the clinician just entered.
    const bmi = useMemo(
        () => calculateBMI(data.weight_kg, data.height_cm),
        [data.weight_kg, data.height_cm],
    );
    const bmiCategory = bmi ? getBMICategory(bmi) : null;

    // Any active drug allergy that collides with a prescribed drug name.
    const prescriptionConflicts = useMemo(() => {
        if (!data.prescription_items.length || !data.allergies.length) {
            return [];
        }

        return data.prescription_items
            .map((prescription) => ({
                prescription,
                allergy: checkDrugAllergyInteraction(
                    prescription.drug_name,
                    data.allergies,
                ),
            }))
            .filter((conflict) => conflict.allergy !== null);
    }, [data.prescription_items, data.allergies]);

    const selectPatient = (patient: Patient) => {
        setSelectedPatient(patient);
        setData('patient_id', patient.id);
        setPatientQuery('');
        setMobileDrawerOpen(false);

        if (patient.allergies) {
            setData('allergies', patientAllergies || []);
        }
    };

    const clearPatient = () => {
        setSelectedPatient(null);
        setData('patient_id', '');
        setData('allergies', []);
    };

    const addPrescription = (input: string, matched: Medication | null) => {
        if (!input.trim()) {
            return;
        }

        const exactMatch = medications.find(
            (medication) =>
                medication.name.toLowerCase() === input.trim().toLowerCase(),
        );
        const medicationId = exactMatch ? exactMatch.id : (matched?.id ?? null);
        const medicationName = exactMatch
            ? exactMatch.name
            : (matched?.name ?? input.trim());

        const allergyConflict = checkDrugAllergyInteraction(
            medicationName,
            data.allergies,
        );

        if (allergyConflict) {
            const proceed = window.confirm(
                `${t('allergyWarning')}: ${medicationName} ${t('mayCauseReaction')} (${allergyConflict.allergen}). ${t('continueAnyway')}?`,
            );

            if (!proceed) {
                return;
            }
        }

        setData('prescription_items', [
            ...data.prescription_items,
            {
                medication_id: medicationId,
                drug_name: medicationName,
                dosage: '',
                frequency: '',
                duration: '',
                quantity: 1,
                instructions: '',
            },
        ]);
    };

    const removeMedication = (index: number) => {
        const items = [...data.prescription_items];
        items.splice(index, 1);
        setData('prescription_items', items);
    };

    const updateMedication = (
        index: number,
        field: keyof PrescriptionFormItem,
        value: string | number,
    ) => {
        const items = [...data.prescription_items];
        items[index] = { ...items[index], [field]: value };
        setData('prescription_items', items);
    };

    const addLabTest = (testId: number) => {
        const test = labTests.find((definition) => definition.id === testId);

        if (
            test &&
            !data.lab_requests.some(
                (item) => item.test_definition_id === test.id,
            )
        ) {
            setData('lab_requests', [
                ...data.lab_requests,
                {
                    test_definition_id: test.id,
                    test_name: test.test_name,
                    code: test.code,
                    urgency: 'routine' as const,
                    reason_for_test: '',
                },
            ]);
        }
    };

    const removeLabTest = (index: number) => {
        const items = [...data.lab_requests];
        items.splice(index, 1);
        setData('lab_requests', items);
    };

    const updateLabTest = (
        index: number,
        field: keyof LabRequestFormItem,
        value: string,
    ) => {
        const items = [...data.lab_requests];
        items[index] = { ...items[index], [field]: value };
        setData('lab_requests', items);
    };

    const addAttachments = (files: File[]) => {
        setData('attachments', [...data.attachments, ...files]);
    };

    const removeAttachment = (index: number) => {
        const items = [...data.attachments];
        items.splice(index, 1);
        setData('attachments', items);
    };

    const toggleReviewOfSystem = (system: ReviewOfSystem) => {
        const current = data.review_of_systems;
        setData(
            'review_of_systems',
            current.includes(system)
                ? current.filter((item) => item !== system)
                : [...current, system],
        );
    };

    const submit = (event: React.FormEvent) => {
        event.preventDefault();

        if (!data.chief_complaint.trim()) {
            toast.error(t('chiefComplaintRequired'));

            return;
        }

        if (!data.diagnosis.trim()) {
            toast.error(t('diagnosisRequired'));

            return;
        }

        post(route('consultations.store'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                reset();
                setSelectedPatient(null);
                setActiveTab('clinical');
                setShowSuccess(true);
            },
        });
    };

    return {
        t,
        showSuccess,
        setShowSuccess,
        flashSuccess: flash?.success,
        data,
        setData,
        processing,
        errors,
        activeTab,
        setActiveTab,
        patientQuery,
        setPatientQuery,
        selectedPatient,
        mobileDrawerOpen,
        setMobileDrawerOpen,
        bmi,
        bmiCategory,
        prescriptionConflicts,
        selectPatient,
        clearPatient,
        addPrescription,
        removeMedication,
        updateMedication,
        addLabTest,
        removeLabTest,
        updateLabTest,
        addAttachments,
        removeAttachment,
        toggleReviewOfSystem,
        submit,
    };
}
