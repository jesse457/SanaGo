import { ExclamationTriangleIcon } from '@heroicons/react/24/outline';
import {
    Button,
    Dialog,
    Field,
    FieldSet,
    Input,
    Select,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import type {
    BedTypeRecord,
    UtilitiesEntityType,
    WardRecord,
} from '@/pages/Settings/components/types';

export type UtilitiesModalMode = 'add' | 'edit' | 'delete' | null;

export interface UtilitiesFormData {
    entity_type: UtilitiesEntityType | '';
    name: string;
    code: string;
    floor: string;
    capacity: number | string;
    description: string;
    ward_id: string;
    bed_type_id: string;
    status: string;
}

type FormErrors = Partial<Record<keyof UtilitiesFormData, string>>;

interface UtilitiesFormDialogProps {
    open: boolean;
    mode: UtilitiesModalMode;
    entity: UtilitiesEntityType;
    data: UtilitiesFormData;
    errors: FormErrors;
    processing: boolean;
    wards: WardRecord[];
    bedTypes: BedTypeRecord[];
    selectedLabel?: string;
    onChange: <K extends keyof UtilitiesFormData>(
        key: K,
        value: UtilitiesFormData[K],
    ) => void;
    onSubmit: (event: React.FormEvent) => void;
    onClose: () => void;
}

/**
 * UtilitiesFormDialog — the add / edit / delete dialog for wards, beds and bed
 * types. Behaviour is unchanged from the original page: same `useForm` payload,
 * same route calls, only the markup is now composed from the kit.
 */
export function UtilitiesFormDialog({
    open,
    mode,
    entity,
    data,
    errors,
    processing,
    wards,
    bedTypes,
    selectedLabel,
    onChange,
    onSubmit,
    onClose,
}: UtilitiesFormDialogProps) {
    const { t } = useTranslation();

    return (
        <Dialog
            open={open}
            onClose={onClose}
            size="lg"
            title={
                mode
                    ? `${mode.charAt(0).toUpperCase() + mode.slice(1)} ${entity}`
                    : ''
            }
            footer={
                <>
                    <Button variant="secondary" onClick={onClose}>
                        {t('common.cancel')}
                    </Button>
                    <Button
                        type="submit"
                        form="utilities-form"
                        variant={mode === 'delete' ? 'danger' : 'primary'}
                        loading={processing}
                    >
                        {processing
                            ? t('common.processing')
                            : mode === 'delete'
                              ? t('page.utilities.delete_permanently')
                              : t('common.save_changes')}
                    </Button>
                </>
            }
        >
            <form id="utilities-form" onSubmit={onSubmit} className="space-y-5">
                {mode === 'delete' ? (
                    <div className="py-2 text-center">
                        <span className="mx-auto mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-critical-soft text-critical-ink">
                            <ExclamationTriangleIcon className="h-5 w-5" />
                        </span>
                        <p className="text-[15px] font-semibold tracking-tight text-ink">
                            {t('page.utilities.confirm_deletion')}
                        </p>
                        <p className="mt-1 text-[13px] leading-relaxed text-ink-muted">
                            {t('page.utilities.confirm_delete_message', {
                                name: selectedLabel ?? '',
                            })}
                        </p>
                    </div>
                ) : (
                    <>
                        {entity === 'ward' && (
                            <FieldSet columns={2}>
                                <Field
                                    label={t('page.utilities.ward_name')}
                                    htmlFor="name"
                                    error={errors.name}
                                    className="sm:col-span-2"
                                >
                                    <Input
                                        id="name"
                                        name="name"
                                        value={data.name}
                                        placeholder="e.g. Maternity Ward"
                                        invalid={Boolean(errors.name)}
                                        onChange={(event) =>
                                            onChange('name', event.target.value)
                                        }
                                    />
                                </Field>
                                <Field
                                    label={t('page.utilities.ward_code')}
                                    htmlFor="code"
                                    error={errors.code}
                                >
                                    <Input
                                        id="code"
                                        name="code"
                                        value={data.code}
                                        placeholder="e.g. W-MAT-01"
                                        invalid={Boolean(errors.code)}
                                        onChange={(event) =>
                                            onChange('code', event.target.value)
                                        }
                                    />
                                </Field>
                                <Field
                                    label={t('page.utilities.capacity_beds')}
                                    htmlFor="capacity"
                                    error={errors.capacity}
                                >
                                    <Input
                                        id="capacity"
                                        name="capacity"
                                        type="number"
                                        min={1}
                                        value={data.capacity}
                                        invalid={Boolean(errors.capacity)}
                                        onChange={(event) =>
                                            onChange(
                                                'capacity',
                                                event.target.value
                                                    ? Number(event.target.value)
                                                    : '',
                                            )
                                        }
                                    />
                                </Field>
                                <Field
                                    label={t('page.utilities.floor_location')}
                                    htmlFor="floor"
                                    error={errors.floor}
                                    className="sm:col-span-2"
                                >
                                    <Input
                                        id="floor"
                                        name="floor"
                                        value={data.floor}
                                        placeholder="e.g. 2nd Floor, West Wing"
                                        invalid={Boolean(errors.floor)}
                                        onChange={(event) =>
                                            onChange(
                                                'floor',
                                                event.target.value,
                                            )
                                        }
                                    />
                                </Field>
                                <Field
                                    label={t('common.description')}
                                    htmlFor="description"
                                    error={errors.description}
                                    className="sm:col-span-2"
                                >
                                    <Textarea
                                        id="description"
                                        name="description"
                                        rows={3}
                                        value={data.description}
                                        placeholder="Brief description of the ward..."
                                        invalid={Boolean(errors.description)}
                                        onChange={(event) =>
                                            onChange(
                                                'description',
                                                event.target.value,
                                            )
                                        }
                                    />
                                </Field>
                            </FieldSet>
                        )}

                        {entity === 'bed' && (
                            <div className="space-y-4">
                                <Field
                                    label={t('page.utilities.bed_code')}
                                    htmlFor="code"
                                    error={errors.code}
                                >
                                    <Input
                                        id="code"
                                        name="code"
                                        value={data.code}
                                        placeholder="e.g. BED-101"
                                        invalid={Boolean(errors.code)}
                                        onChange={(event) =>
                                            onChange('code', event.target.value)
                                        }
                                    />
                                </Field>
                                <FieldSet columns={2}>
                                    <Field
                                        label={t('page.utilities.assign_ward')}
                                        htmlFor="ward_id"
                                        error={errors.ward_id}
                                    >
                                        <Select
                                            id="ward_id"
                                            name="ward_id"
                                            value={data.ward_id}
                                            invalid={Boolean(errors.ward_id)}
                                            onChange={(event) =>
                                                onChange(
                                                    'ward_id',
                                                    event.target.value,
                                                )
                                            }
                                        >
                                            <option value="">
                                                {t(
                                                    'page.utilities.select_ward',
                                                )}
                                            </option>
                                            {wards.map((ward) => (
                                                <option
                                                    key={ward.id}
                                                    value={ward.id}
                                                >
                                                    {ward.name}
                                                </option>
                                            ))}
                                        </Select>
                                    </Field>
                                    <Field
                                        label={t('page.utilities.bed_type')}
                                        htmlFor="bed_type_id"
                                        error={errors.bed_type_id}
                                    >
                                        <Select
                                            id="bed_type_id"
                                            name="bed_type_id"
                                            value={data.bed_type_id}
                                            invalid={Boolean(
                                                errors.bed_type_id,
                                            )}
                                            onChange={(event) =>
                                                onChange(
                                                    'bed_type_id',
                                                    event.target.value,
                                                )
                                            }
                                        >
                                            <option value="">
                                                {t(
                                                    'page.utilities.select_type',
                                                )}
                                            </option>
                                            {bedTypes.map((bedType) => (
                                                <option
                                                    key={bedType.id}
                                                    value={bedType.id}
                                                >
                                                    {bedType.name}
                                                </option>
                                            ))}
                                        </Select>
                                    </Field>
                                </FieldSet>
                                <Field
                                    label={t('page.utilities.current_status')}
                                    htmlFor="status"
                                    error={errors.status}
                                >
                                    <Select
                                        id="status"
                                        name="status"
                                        value={data.status}
                                        invalid={Boolean(errors.status)}
                                        onChange={(event) =>
                                            onChange(
                                                'status',
                                                event.target.value,
                                            )
                                        }
                                    >
                                        <option value="Available">
                                            {t('page.utilities.available')}
                                        </option>
                                        <option value="Occupied">
                                            {t('page.utilities.occupied')}
                                        </option>
                                        <option value="Maintenance">
                                            {t('page.utilities.maintenance')}
                                        </option>
                                    </Select>
                                </Field>
                            </div>
                        )}

                        {entity === 'type' && (
                            <div className="space-y-4">
                                <Field
                                    label={t('page.utilities.bed_type_name')}
                                    htmlFor="name"
                                    error={errors.name}
                                >
                                    <Input
                                        id="name"
                                        name="name"
                                        value={data.name}
                                        placeholder="e.g. ICU Bed, Standard"
                                        invalid={Boolean(errors.name)}
                                        onChange={(event) =>
                                            onChange('name', event.target.value)
                                        }
                                    />
                                </Field>
                                <Field
                                    label={t('page.utilities.patient_capacity')}
                                    htmlFor="capacity"
                                    error={errors.capacity}
                                >
                                    <Input
                                        id="capacity"
                                        name="capacity"
                                        type="number"
                                        min={1}
                                        value={data.capacity}
                                        invalid={Boolean(errors.capacity)}
                                        onChange={(event) =>
                                            onChange(
                                                'capacity',
                                                event.target.value
                                                    ? Number(event.target.value)
                                                    : '',
                                            )
                                        }
                                    />
                                </Field>
                            </div>
                        )}
                    </>
                )}
            </form>
        </Dialog>
    );
}
