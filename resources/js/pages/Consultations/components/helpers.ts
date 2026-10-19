import type { Allergy, ReviewOfSystem } from './types';

/**
 * Pure clinical helpers shared by the consultation sections. No JSX lives here
 * so both the page and its components can import them freely.
 */

export const calculateBMI = (
    weightKg: string,
    heightCm: string,
): number | null => {
    const weight = parseFloat(weightKg);
    const height = parseFloat(heightCm);

    if (!weight || !height || height === 0) {
        return null;
    }

    return parseFloat((weight / (height / 100) ** 2).toFixed(1));
};

export type BMITone = 'positive' | 'warning' | 'critical' | 'neutral';

export interface BMICategory {
    label: string;
    tone: BMITone;
}

export const getBMICategory = (bmi: number): BMICategory => {
    if (bmi < 18.5) {
        return { label: 'Underweight', tone: 'warning' };
    }

    if (bmi < 25) {
        return { label: 'Normal', tone: 'positive' };
    }

    if (bmi < 30) {
        return { label: 'Overweight', tone: 'warning' };
    }

    if (bmi < 35) {
        return { label: 'Obese Class I', tone: 'warning' };
    }

    if (bmi < 40) {
        return { label: 'Obese Class II', tone: 'critical' };
    }

    return { label: 'Obese Class III', tone: 'critical' };
};

export const checkDrugAllergyInteraction = (
    drugName: string,
    allergies: Allergy[],
): Allergy | null => {
    const drugLower = drugName.toLowerCase();

    return (
        allergies.find(
            (allergy) =>
                allergy.status === 'active' &&
                allergy.type === 'drug' &&
                allergy.allergen.toLowerCase().includes(drugLower),
        ) || null
    );
};

export const REVIEW_OF_SYSTEMS: ReviewOfSystem[] = [
    'Constitutional',
    'Eyes',
    'ENT',
    'Cardiovascular',
    'Respiratory',
    'Gastrointestinal',
    'Genitourinary',
    'Musculoskeletal',
    'Skin',
    'Neurological',
    'Psychiatric',
    'Endocrine',
    'Hematologic/Lymphatic',
    'Allergic/Immunologic',
];

export const URGENCY_TONE: Record<string, 'neutral' | 'warning' | 'critical'> =
    {
        routine: 'neutral',
        urgent: 'warning',
        stat: 'critical',
    };
