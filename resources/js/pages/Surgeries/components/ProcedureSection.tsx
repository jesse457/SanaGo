import { CubeIcon, DocumentTextIcon } from '@heroicons/react/24/outline';
import { Field, Input, SectionCard, Textarea } from '@/Components/ui';
import type { SetSurgeryData, Translate } from './types';

interface ProcedureSectionProps {
    surgeryName: string;
    description: string;
    errors: Partial<Record<string, string>>;
    setData: SetSurgeryData;
    t: Translate;
}

/**
 * ProcedureSection — the procedure itself: what is being performed and the
 * operative description.
 */
export function ProcedureSection({
    surgeryName,
    description,
    errors,
    setData,
    t,
}: ProcedureSectionProps) {
    return (
        <SectionCard
            icon={CubeIcon}
            title={t('surgeries.create.section_procedure')}
        >
            <div className="grid grid-cols-1 gap-x-4 gap-y-5 md:grid-cols-2">
                <Field
                    label={t('surgeries.create.procedure_name')}
                    htmlFor="surgery_name"
                    required
                    error={errors.surgery_name}
                    className="md:col-span-2"
                >
                    <Input
                        id="surgery_name"
                        value={surgeryName}
                        icon={DocumentTextIcon}
                        invalid={Boolean(errors.surgery_name)}
                        required
                        placeholder={t(
                            'surgeries.create.procedure_name_placeholder',
                        )}
                        onChange={(event) =>
                            setData('surgery_name', event.target.value)
                        }
                    />
                </Field>

                <Field
                    label={t('surgeries.create.description')}
                    htmlFor="description"
                    error={errors.description}
                    className="md:col-span-2"
                >
                    <Textarea
                        id="description"
                        rows={2}
                        value={description}
                        invalid={Boolean(errors.description)}
                        placeholder={t(
                            'surgeries.create.description_placeholder',
                        )}
                        onChange={(event) =>
                            setData('description', event.target.value)
                        }
                    />
                </Field>
            </div>
        </SectionCard>
    );
}
