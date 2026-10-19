import { BeakerIcon, PlusIcon } from '@heroicons/react/24/outline';
import { useMemo, useRef, useState } from 'react';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    Input,
    SectionCard,
} from '@/Components/ui';
import {
    DrugAllergyAlert,
    MedicationCards,
    MedicationTable,
} from './PrescriptionSection';
import type {
    Allergy,
    Medication,
    PrescriptionFormItem,
    Translate,
} from './types';

interface PrescriptionPanelProps {
    medications: Medication[];
    items: PrescriptionFormItem[];
    allergies: Allergy[];
    conflicts: {
        prescription: PrescriptionFormItem;
        allergy: Allergy | null;
    }[];
    onAdd: (input: string, matched: Medication | null) => void;
    onUpdate: (
        index: number,
        field: keyof PrescriptionFormItem,
        value: string | number,
    ) => void;
    onRemove: (index: number) => void;
    t: Translate;
}

/**
 * PrescriptionPanel — the whole prescribing tab: the safety banner, the
 * formulary type-ahead, and the same lines rendered as a table on wide screens
 * and stacked cards on narrow ones.
 */
export function PrescriptionPanel({
    medications,
    items,
    allergies,
    conflicts,
    onAdd,
    onUpdate,
    onRemove,
    t,
}: PrescriptionPanelProps) {
    const [input, setInput] = useState('');
    const [matched, setMatched] = useState<Medication | null>(null);
    const [open, setOpen] = useState(false);
    const blurTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

    const suggestions = useMemo(() => {
        if (input.length < 2) {
            return [];
        }

        return medications.filter((medication) =>
            medication.name.toLowerCase().includes(input.toLowerCase()),
        );
    }, [input, medications]);

    const handleAdd = () => {
        onAdd(input, matched);
        setInput('');
        setMatched(null);
        setOpen(false);
    };

    return (
        <>
            <DrugAllergyAlert conflicts={conflicts} t={t} />

            <SectionCard
                icon={BeakerIcon}
                title={t('addMedicationToPrescription')}
                description={t('typeDrugName')}
            >
                <div className="flex flex-col gap-2 sm:flex-row">
                    <div className="relative min-w-0 flex-1">
                        <Input
                            value={input}
                            placeholder={t('typeDrugName')}
                            autoComplete="off"
                            onChange={(event) => {
                                setInput(event.target.value);
                                setMatched(null);
                                setOpen(true);
                            }}
                            onFocus={() => setOpen(true)}
                            onBlur={() => {
                                blurTimer.current = setTimeout(
                                    () => setOpen(false),
                                    160,
                                );
                            }}
                            onKeyDown={(event) => {
                                if (event.key === 'Enter') {
                                    event.preventDefault();
                                    handleAdd();
                                }
                            }}
                        />

                        {open && suggestions.length > 0 && !matched && (
                            <div className="absolute inset-x-0 top-full z-30 mt-1.5 max-h-48 overflow-y-auto rounded-lg border border-line bg-surface p-1 shadow-float">
                                {suggestions.map((medication) => (
                                    <Button
                                        key={medication.id}
                                        variant="ghost"
                                        onClick={() => {
                                            if (blurTimer.current) {
                                                clearTimeout(blurTimer.current);
                                            }

                                            setInput(medication.name);
                                            setMatched(medication);
                                            setOpen(false);
                                        }}
                                        className="h-auto w-full justify-between gap-3 px-2.5 py-2"
                                    >
                                        <span className="truncate text-[13px] font-medium text-ink">
                                            {medication.name}
                                        </span>
                                        <Badge
                                            tone="positive"
                                            className="shrink-0"
                                        >
                                            {t('systemMatch')}
                                        </Badge>
                                    </Button>
                                ))}
                            </div>
                        )}
                    </div>

                    <Button
                        variant="secondary"
                        icon={PlusIcon}
                        onClick={handleAdd}
                        className="shrink-0"
                    >
                        {t('addMedication')}
                    </Button>
                </div>
            </SectionCard>

            {items.length > 0 ? (
                <>
                    <div className="hidden lg:block">
                        <MedicationTable
                            items={items}
                            allergies={allergies}
                            onUpdate={onUpdate}
                            onRemove={onRemove}
                            t={t}
                        />
                    </div>
                    <div className="lg:hidden">
                        <MedicationCards
                            items={items}
                            allergies={allergies}
                            onUpdate={onUpdate}
                            onRemove={onRemove}
                            t={t}
                        />
                    </div>
                </>
            ) : (
                <Card>
                    <EmptyState
                        panel={false}
                        icon={BeakerIcon}
                        title={t('noMedicationsAddedYet')}
                        description={t('addMedicationToPrescription')}
                    />
                </Card>
            )}
        </>
    );
}
