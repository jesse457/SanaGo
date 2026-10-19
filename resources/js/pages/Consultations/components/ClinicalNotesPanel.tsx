import type { ReactNode } from 'react';

interface ClinicalNotesPanelProps {
    visitInformation: ReactNode;
    socialHistory: ReactNode;
    vitals: ReactNode;
    complaints: ReactNode;
    history: ReactNode;
    reviewOfSystems: ReactNode;
    examAndPlan: ReactNode;
    followUp: ReactNode;
}

/**
 * ClinicalNotesPanel — the reading order of the clinical tab. The individual
 * sections own their own markup; this just fixes the sequence and spacing so the
 * page file stays a thin composition.
 */
export function ClinicalNotesPanel({
    visitInformation,
    socialHistory,
    vitals,
    complaints,
    history,
    reviewOfSystems,
    examAndPlan,
    followUp,
}: ClinicalNotesPanelProps) {
    return (
        <>
            {visitInformation}
            {socialHistory}
            {vitals}
            {complaints}
            {history}
            {reviewOfSystems}
            {examAndPlan}
            {followUp}
        </>
    );
}
