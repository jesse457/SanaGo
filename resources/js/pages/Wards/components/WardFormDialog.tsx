import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Button,
    Dialog,
    Field,
    FieldSet,
    Input,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

export interface Ward {
    id: number;
    name?: string | null;
    code?: string | null;
    floor?: string | null;
    capacity?: number | null;
    description?: string | null;
    beds_count?: number | null;
}

interface WardFormDialogProps {
    open: boolean;
    /** `null` opens the dialog in create mode. */
    ward: Ward | null;
    onClose: () => void;
}

/**
 * WardFormDialog — the single ward create/edit form. Owns its own `useForm`
 * state so the page only has to decide which ward is being edited.
 */
export function WardFormDialog({ open, ward, onClose }: WardFormDialogProps) {
    const { t } = useTranslation();
    const isEditing = Boolean(ward);

    // The page remounts this dialog per target (`key`), so initial values are
    // safe to read once here instead of syncing them from an effect.
    const { data, setData, post, put, processing, errors, reset, clearErrors } =
        useForm({
            name: ward?.name || '',
            code: ward?.code || '',
            floor: ward?.floor || '',
            capacity:
                ward?.capacity === null || ward?.capacity === undefined
                    ? ''
                    : String(ward.capacity),
            description: ward?.description || '',
        });

    const close = () => {
        onClose();
        clearErrors();
        reset();
    };

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();

        const options = { onSuccess: () => close(), preserveScroll: true };

        if (ward) {
            put(route('wards.update', ward.id), options);
        } else {
            post(route('wards.store'), options);
        }
    };

    return (
        <Dialog
            open={open}
            onClose={close}
            title={
                isEditing ? t('page.wards.edit_ward') : t('page.wards.add_ward')
            }
            description={t('page.wards.subtitle')}
            size="lg"
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
                        form="ward-form"
                        variant="primary"
                        loading={processing}
                    >
                        {processing
                            ? t('page.wards.saving')
                            : t('common.save_changes')}
                    </Button>
                </>
            }
        >
            <form id="ward-form" onSubmit={handleSubmit}>
                <FieldSet columns={2}>
                    <Field
                        label={t('page.wards.ward_name')}
                        htmlFor="ward-name"
                        required
                        error={errors.name}
                        className="sm:col-span-2"
                    >
                        <Input
                            id="ward-name"
                            value={data.name}
                            onChange={(event) =>
                                setData('name', event.target.value)
                            }
                            placeholder="e.g. Maternity Ward"
                            invalid={Boolean(errors.name)}
                            autoFocus
                        />
                    </Field>

                    <Field
                        label={t('page.wards.ward_code')}
                        htmlFor="ward-code"
                        error={errors.code}
                        hint="Short identifier shown on bed tiles."
                    >
                        <Input
                            id="ward-code"
                            value={data.code}
                            onChange={(event) =>
                                setData('code', event.target.value)
                            }
                            placeholder="e.g. W-MAT-01"
                            invalid={Boolean(errors.code)}
                        />
                    </Field>

                    <Field
                        label={t('page.wards.capacity_beds')}
                        htmlFor="ward-capacity"
                        error={errors.capacity}
                        hint="Total beds this ward can hold."
                    >
                        <Input
                            id="ward-capacity"
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

                    <Field
                        label={t('page.wards.floor_location')}
                        htmlFor="ward-floor"
                        error={errors.floor}
                        className="sm:col-span-2"
                    >
                        <Input
                            id="ward-floor"
                            value={data.floor}
                            onChange={(event) =>
                                setData('floor', event.target.value)
                            }
                            placeholder="e.g. 2nd Floor, West Wing"
                            invalid={Boolean(errors.floor)}
                        />
                    </Field>

                    <Field
                        label={t('common.description')}
                        htmlFor="ward-description"
                        optional
                        error={errors.description}
                        className="sm:col-span-2"
                    >
                        <Textarea
                            id="ward-description"
                            rows={3}
                            value={data.description}
                            onChange={(event) =>
                                setData('description', event.target.value)
                            }
                            placeholder="Brief description…"
                            invalid={Boolean(errors.description)}
                        />
                    </Field>
                </FieldSet>
            </form>
        </Dialog>
    );
}
