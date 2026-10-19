import { BeakerIcon, PlusIcon } from '@heroicons/react/24/outline';
import { useState } from 'react';
import { Button, EmptyState, SectionCard, Select } from '@/Components/ui';
import { LabRequestRow } from './LabRequestSection';
import type { LabRequestFormItem, LabTestDefinition, Translate } from './types';

interface LabRequestPanelProps {
    labTests: LabTestDefinition[];
    items: LabRequestFormItem[];
    onAdd: (testId: number) => void;
    onUpdate: (
        index: number,
        field: keyof LabRequestFormItem,
        value: string,
    ) => void;
    onRemove: (index: number) => void;
    t: Translate;
}

/**
 * LabRequestPanel — the labs tab: catalog a test onto the request list, then set
 * its urgency and clinical indication.
 */
export function LabRequestPanel({
    labTests,
    items,
    onAdd,
    onUpdate,
    onRemove,
    t,
}: LabRequestPanelProps) {
    const [selected, setSelected] = useState('');

    return (
        <>
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
                            onChange={(event) =>
                                setSelected(event.target.value)
                            }
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

            {items.length > 0 ? (
                <div className="space-y-3">
                    {items.map((item, index) => (
                        <LabRequestRow
                            key={`${item.test_definition_id}-${index}`}
                            item={item}
                            index={index}
                            onChange={(field, value) =>
                                onUpdate(index, field, value)
                            }
                            onRemove={() => onRemove(index)}
                            t={t}
                        />
                    ))}
                </div>
            ) : (
                <EmptyState
                    icon={BeakerIcon}
                    title={t('noLabTestsAddedYet')}
                    description={t('addLabTest')}
                />
            )}
        </>
    );
}
