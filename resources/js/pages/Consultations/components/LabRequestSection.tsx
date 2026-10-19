import { BeakerIcon, PlusIcon, TrashIcon } from '@heroicons/react/24/outline';
import { useState } from 'react';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    Field,
    FieldSet,
    Input,
    SectionCard,
    Select,
} from '@/Components/ui';
import { URGENCY_TONE } from './helpers';
import type { LabRequestFormItem, LabTestDefinition, Translate } from './types';

interface LabTestPickerProps {
    labTests: LabTestDefinition[];
    onAdd: (testId: number) => void;
    t: Translate;
}

/**
 * LabTestPicker — catalogs a lab test onto the request list.
 */
export function LabTestPicker({ labTests, onAdd, t }: LabTestPickerProps) {
    const [selected, setSelected] = useState('');

    return (
        <SectionCard
            icon={BeakerIcon}
            title={t('addLabTest')}
            description={t('selectLabTest')}
        >
            <div className="flex flex-col gap-2 sm:flex-row">
                <div className="min-w-0 flex-1">
                    <Select
                        aria-label={t('selectLabTest')}
                        value={selected}
                        onChange={(event) => setSelected(event.target.value)}
                    >
                        <option value="">{t('selectLabTest')}</option>
                        {labTests.map((test) => (
                            <option key={test.id} value={test.id}>
                                {test.test_name} ({test.code})
                            </option>
                        ))}
                    </Select>
                </div>
                <Button
                    variant="secondary"
                    icon={PlusIcon}
                    className="shrink-0"
                    onClick={() => {
                        if (!selected) {
                            return;
                        }

                        onAdd(Number(selected));
                        setSelected('');
                    }}
                >
                    {t('addTest')}
                </Button>
            </div>
        </SectionCard>
    );
}

interface LabRequestRowProps {
    item: LabRequestFormItem;
    index: number;
    onChange: (field: keyof LabRequestFormItem, value: string) => void;
    onRemove: () => void;
    t: Translate;
}

/**
 * LabRequestRow — one requested test with its urgency and clinical indication.
 */
export function LabRequestRow({
    item,
    index,
    onChange,
    onRemove,
    t,
}: LabRequestRowProps) {
    return (
        <Card className="p-4">
            <div className="mb-3 flex items-start justify-between gap-3">
                <div className="flex min-w-0 flex-wrap items-center gap-2">
                    <span className="text-[13px] font-semibold text-ink">
                        {item.test_name}
                    </span>
                    <Badge tone="muted" variant="outline">
                        <span className="font-mono normal-case">
                            {item.code}
                        </span>
                    </Badge>
                    <Badge tone={URGENCY_TONE[item.urgency] ?? 'neutral'} dot>
                        {item.urgency}
                    </Badge>
                </div>
                <Button
                    variant="ghost"
                    size="icon"
                    icon={TrashIcon}
                    aria-label={t('remove')}
                    onClick={onRemove}
                    className="shrink-0 text-ink-subtle hover:text-critical-ink"
                />
            </div>

            <FieldSet columns={2}>
                <Field label={t('urgency')} htmlFor={`urgency-${index}`}>
                    <Select
                        id={`urgency-${index}`}
                        value={item.urgency}
                        onChange={(event) =>
                            onChange('urgency', event.target.value)
                        }
                    >
                        <option value="routine">{t('routine')}</option>
                        <option value="urgent">{t('urgent')}</option>
                        <option value="stat">{t('stat')}</option>
                    </Select>
                </Field>
                <Field label={t('reasonForTest')} htmlFor={`reason-${index}`}>
                    <Input
                        id={`reason-${index}`}
                        value={item.reason_for_test}
                        onChange={(event) =>
                            onChange('reason_for_test', event.target.value)
                        }
                        placeholder={t('clinicalIndication')}
                    />
                </Field>
            </FieldSet>
        </Card>
    );
}

/**
 * EmptyLabRequests — empty state for the labs tab.
 */
export function EmptyLabRequests({ t }: { t: Translate }) {
    return (
        <EmptyState
            panel={false}
            icon={BeakerIcon}
            title={t('noLabTestsAddedYet')}
            description={t('addLabTest')}
        />
    );
}
