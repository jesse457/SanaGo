import {
    BeakerIcon,
    ExclamationTriangleIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import { useMemo, useRef, useState } from 'react';
import {
    Alert,
    Badge,
    Button,
    Card,
    EmptyState,
    Field,
    Input,
    SectionCard,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import { checkDrugAllergyInteraction } from './helpers';
import type {
    Allergy,
    Medication,
    PrescriptionFormItem,
    Translate,
} from './types';

interface DrugAllergyAlertProps {
    conflicts: {
        prescription: PrescriptionFormItem;
        allergy: Allergy | null;
    }[];
    t: Translate;
}

/**
 * DrugAllergyAlert — the clinical safety banner for the prescription tab.
 * Critical tone, never a red panel background.
 */
export function DrugAllergyAlert({ conflicts, t }: DrugAllergyAlertProps) {
    if (conflicts.length === 0) {
        return null;
    }

    return (
        <Alert
            tone="critical"
            title={t('Drug-Allergy Interaction Warning')}
            action={
                <Badge tone="critical" variant="solid">
                    {conflicts.length}
                </Badge>
            }
        >
            <ul className="list-inside list-disc space-y-0.5">
                {conflicts.map((conflict, index) => (
                    <li key={index}>
                        <span className="font-semibold text-ink">
                            {conflict.prescription.drug_name}
                        </span>{' '}
                        — {t('Patient allergic to')}{' '}
                        {conflict.allergy?.allergen} (
                        {conflict.allergy?.severity})
                    </li>
                ))}
            </ul>
        </Alert>
    );
}

interface MedicationPickerProps {
    medications: Medication[];
    onAdd: (input: string, matched: Medication | null) => void;
    t: Translate;
}

/**
 * MedicationPicker — type-ahead over the formulary with an explicit add step.
 */
export function MedicationPicker({
    medications,
    onAdd,
    t,
}: MedicationPickerProps) {
    const [input, setInput] = useState('');
    const [matched, setMatched] = useState<Medication | null>(null);
    const [open, setOpen] = useState(false);
    const blurTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

    const suggestions = useMemo(() => {
        if (input.trim().length < 2) {
            return [];
        }

        const query = input.toLowerCase();

        return medications
            .filter((medication) =>
                medication.name.toLowerCase().includes(query),
            )
            .slice(0, 8);
    }, [input, medications]);

    const handleAdd = () => {
        onAdd(input, matched);
        setInput('');
        setMatched(null);
        setOpen(false);
    };

    return (
        <SectionCard
            icon={BeakerIcon}
            title={t('addMedicationToPrescription')}
            description={t('typeDrugName')}
        >
            <div className="flex flex-col gap-2 sm:flex-row">
                <div className="relative min-w-0 flex-1">
                    <Input
                        value={input}
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
                        placeholder={t('typeDrugName')}
                        autoComplete="off"
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
                                    <Badge tone="positive" className="shrink-0">
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
    );
}

const inlineInputClass = 'h-9 px-2 text-[12.5px]';

/**
 * MedicationLine — one editable prescription line. Shared by the desktop table
 * and the mobile card stack.
 */
export function MedicationFields({
    item,
    onChange,
    t,
}: {
    item: PrescriptionFormItem;
    onChange: (
        field: keyof PrescriptionFormItem,
        value: string | number,
    ) => void;
    t: Translate;
}) {
    return (
        <>
            <Field label={t('dosage')} htmlFor={`dosage-${item.drug_name}`}>
                <Input
                    id={`dosage-${item.drug_name}`}
                    value={item.dosage}
                    onChange={(event) => onChange('dosage', event.target.value)}
                    placeholder={t('egOneTab')}
                    className={inlineInputClass}
                />
            </Field>
            <Field
                label={t('frequency')}
                htmlFor={`frequency-${item.drug_name}`}
            >
                <Input
                    id={`frequency-${item.drug_name}`}
                    value={item.frequency}
                    onChange={(event) =>
                        onChange('frequency', event.target.value)
                    }
                    placeholder={t('egThreeTimesDay')}
                    className={inlineInputClass}
                />
            </Field>
            <Field label={t('duration')} htmlFor={`duration-${item.drug_name}`}>
                <Input
                    id={`duration-${item.drug_name}`}
                    value={item.duration}
                    onChange={(event) =>
                        onChange('duration', event.target.value)
                    }
                    placeholder="7d"
                    className={inlineInputClass}
                />
            </Field>
            <Field label={t('qty')} htmlFor={`qty-${item.drug_name}`}>
                <Input
                    id={`qty-${item.drug_name}`}
                    type="number"
                    min={1}
                    value={item.quantity}
                    onChange={(event) =>
                        onChange(
                            'quantity',
                            parseInt(event.target.value, 10) || 1,
                        )
                    }
                    className={`${inlineInputClass} tabular-nums`}
                />
            </Field>
            <Field
                label={t('instructions')}
                htmlFor={`instructions-${item.drug_name}`}
                className="sm:col-span-2 lg:col-span-1"
            >
                <Input
                    id={`instructions-${item.drug_name}`}
                    value={item.instructions}
                    onChange={(event) =>
                        onChange('instructions', event.target.value)
                    }
                    placeholder={t('afterMeals')}
                    className={inlineInputClass}
                />
            </Field>
        </>
    );
}

interface MedicationTableProps {
    items: PrescriptionFormItem[];
    allergies: Allergy[];
    onUpdate: (
        index: number,
        field: keyof PrescriptionFormItem,
        value: string | number,
    ) => void;
    onRemove: (index: number) => void;
    t: Translate;
}

/**
 * MedicationTable — desktop prescription lines.
 */
export function MedicationTable({
    items,
    allergies,
    onUpdate,
    onRemove,
    t,
}: MedicationTableProps) {
    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHeaderCell>{t('drugName')}</TableHeaderCell>
                    <TableHeaderCell>{t('dosage')}</TableHeaderCell>
                    <TableHeaderCell>{t('frequency')}</TableHeaderCell>
                    <TableHeaderCell>{t('duration')}</TableHeaderCell>
                    <TableHeaderCell numeric>{t('qty')}</TableHeaderCell>
                    <TableHeaderCell>{t('instructions')}</TableHeaderCell>
                    <TableHeaderCell>
                        <span className="sr-only">{t('actions')}</span>
                    </TableHeaderCell>
                </TableRow>
            </TableHeader>
            <TableBody>
                {items.map((item, index) => {
                    const conflicted = Boolean(
                        checkDrugAllergyInteraction(item.drug_name, allergies),
                    );

                    return (
                        <TableRow
                            key={`${item.drug_name}-${index}`}
                            className={conflicted ? 'bg-critical-soft/60' : ''}
                        >
                            <TableCell>
                                <span className="flex items-center gap-1.5 font-semibold text-ink">
                                    {item.drug_name}
                                    {conflicted && (
                                        <ExclamationTriangleIcon className="h-3.5 w-3.5 text-critical-ink" />
                                    )}
                                </span>
                                {!item.medication_id && (
                                    <Badge tone="warning" className="mt-1">
                                        {t('external')}
                                    </Badge>
                                )}
                            </TableCell>
                            <TableCell>
                                <Input
                                    aria-label={t('dosage')}
                                    value={item.dosage}
                                    onChange={(event) =>
                                        onUpdate(
                                            index,
                                            'dosage',
                                            event.target.value,
                                        )
                                    }
                                    className={inlineInputClass}
                                />
                            </TableCell>
                            <TableCell>
                                <Input
                                    aria-label={t('frequency')}
                                    value={item.frequency}
                                    onChange={(event) =>
                                        onUpdate(
                                            index,
                                            'frequency',
                                            event.target.value,
                                        )
                                    }
                                    className={inlineInputClass}
                                />
                            </TableCell>
                            <TableCell>
                                <Input
                                    aria-label={t('duration')}
                                    value={item.duration}
                                    onChange={(event) =>
                                        onUpdate(
                                            index,
                                            'duration',
                                            event.target.value,
                                        )
                                    }
                                    className={inlineInputClass}
                                />
                            </TableCell>
                            <TableCell numeric>
                                <Input
                                    aria-label={t('qty')}
                                    type="number"
                                    min={1}
                                    value={item.quantity}
                                    onChange={(event) =>
                                        onUpdate(
                                            index,
                                            'quantity',
                                            parseInt(event.target.value, 10) ||
                                                1,
                                        )
                                    }
                                    className={`${inlineInputClass} tabular-nums`}
                                />
                            </TableCell>
                            <TableCell>
                                <Input
                                    aria-label={t('instructions')}
                                    value={item.instructions}
                                    onChange={(event) =>
                                        onUpdate(
                                            index,
                                            'instructions',
                                            event.target.value,
                                        )
                                    }
                                    className={inlineInputClass}
                                />
                            </TableCell>
                            <TableCell>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    icon={TrashIcon}
                                    aria-label={t('actions')}
                                    onClick={() => onRemove(index)}
                                    className="text-ink-subtle hover:text-critical-ink"
                                />
                            </TableCell>
                        </TableRow>
                    );
                })}
            </TableBody>
        </Table>
    );
}

interface MedicationCardsProps {
    items: PrescriptionFormItem[];
    allergies: Allergy[];
    onUpdate: (
        index: number,
        field: keyof PrescriptionFormItem,
        value: string | number,
    ) => void;
    onRemove: (index: number) => void;
    t: Translate;
}

/**
 * MedicationCards — the same lines, stacked for narrow viewports.
 */
export function MedicationCards({
    items,
    allergies,
    onUpdate,
    onRemove,
    t,
}: MedicationCardsProps) {
    return (
        <div className="space-y-3">
            {items.map((item, index) => {
                const conflicted = Boolean(
                    checkDrugAllergyInteraction(item.drug_name, allergies),
                );

                return (
                    <Card key={`${item.drug_name}-${index}`} className="p-4">
                        <div className="mb-3 flex items-start justify-between gap-3">
                            <div className="min-w-0">
                                <span className="flex items-center gap-1.5 font-semibold text-ink">
                                    {item.drug_name}
                                    {conflicted && (
                                        <ExclamationTriangleIcon className="h-3.5 w-3.5 text-critical-ink" />
                                    )}
                                </span>
                                {!item.medication_id && (
                                    <Badge tone="warning" className="mt-1">
                                        {t('external')}
                                    </Badge>
                                )}
                            </div>
                            <Button
                                variant="ghost"
                                size="icon"
                                icon={TrashIcon}
                                aria-label={t('actions')}
                                onClick={() => onRemove(index)}
                                className="shrink-0 text-ink-subtle hover:text-critical-ink"
                            />
                        </div>
                        <div className="grid grid-cols-2 gap-3">
                            <MedicationFields
                                item={item}
                                t={t}
                                onChange={(field, value) =>
                                    onUpdate(index, field, value)
                                }
                            />
                        </div>
                    </Card>
                );
            })}
        </div>
    );
}

export function EmptyPrescription({ t }: { t: Translate }) {
    return (
        <EmptyState
            panel={false}
            icon={BeakerIcon}
            title={t('noMedicationsAddedYet')}
            description={t('addMedicationToPrescription')}
        />
    );
}
