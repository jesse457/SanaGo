import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Button, Dialog, Field, FieldSet, Input } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

export interface BedType {
    id: number;
    name?: string | null;
    capacity?: number | string | null;
}

interface BedTypeFormDialogProps {
    open: boolean;
    /** `null` opens the dialog in create mode. */
    bedType: BedType | null;
    onClose: () => void;
}

/**
 * BedTypeFormDialog — the create/edit form for a bed type definition. Reused by
 * both the "add type" and "edit" actions.
 */
export function BedTypeFormDialog({
    open,
    bedType,
    onClose,
}: BedTypeFormDialogProps) {
    const { t } = useTranslation();

    const { data, setData, post, put, processing, errors, reset, clearErrors } =
        useForm({
            name: bedType?.name || '',
            capacity:
                bedType?.capacity === null || bedType?.capacity === undefined
                    ? ''
                    : String(bedType.capacity),
        });

    const close = () => {
        onClose();
        clearErrors();
        reset();
    };

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        const options = { onSuccess: () => close(), preserveScroll: true };

        if (bedType) {
            put(route('bed-types.update', bedType.id), options);
        } else {
            post(route('bed-types.store'), options);
        }
    };

    return (
        <Dialog
            open={open}
            onClose={close}
            title={
                bedType
                    ? t('page.bed_type.edit_type')
                    : t('page.bed_type.add_type')
            }
            description={t('page.bed_type.subtitle')}
            size="md"
            footer={
                <>
                    <Button
                        type="button"
                        variant="secondary"
                        onClick={close}
                        disabled={processing}
                    >
                        {t('common.cancel')}
                    </Button>
                    <Button
                        type="submit"
                        form="bed-type-form"
                        loading={processing}
                    >
                        {processing
                            ? t('page.bed_type.saving')
                            : t('common.save_changes')}
                    </Button>
                </>
            }
        >
            <form id="bed-type-form" onSubmit={handleSubmit}>
                <FieldSet columns={1}>
                    <Field
                        label={t('page.bed_type.bed_type_name')}
                        htmlFor="bed-type-name"
                        required
                        error={errors.name}
                    >
                        <Input
                            id="bed-type-name"
                            value={data.name}
                            onChange={(event) =>
                                setData('name', event.target.value)
                            }
                            placeholder="e.g. ICU, General, Private"
                            invalid={Boolean(errors.name)}
                            autoFocus
                        />
                    </Field>

                    <Field
                        label={t('page.bed_type.patient_capacity')}
                        htmlFor="bed-type-capacity"
                        required
                        error={errors.capacity}
                        hint="How many patients a bed of this type is designed for."
                    >
                        <Input
                            id="bed-type-capacity"
                            type="number"
                            min="1"
                            inputMode="numeric"
                            value={data.capacity}
                            onChange={(event) =>
                                setData('capacity', event.target.value)
                            }
                            invalid={Boolean(errors.capacity)}
                        />
                    </Field>
                </FieldSet>
            </form>
        </Dialog>
    );
}
