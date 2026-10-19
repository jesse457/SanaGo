import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Button,
    Dialog,
    Field,
    FieldSet,
    Input,
    Select,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import type { Bed } from '@/pages/Beds/components/BedTile';

interface Option {
    id: number;
    name?: string | null;
}

interface BedFormDialogProps {
    open: boolean;
    /** `null` opens the dialog in create mode. */
    bed: Bed | null;
    wards: Option[];
    bedTypes: Option[];
    onClose: () => void;
}

/** BedFormDialog — one form for both create and edit, seeded on mount. */
export function BedFormDialog({
    open,
    bed,
    wards,
    bedTypes,
    onClose,
}: BedFormDialogProps) {
    const { t } = useTranslation();

    const { data, setData, post, put, processing, errors, reset, clearErrors } =
        useForm({
            code: bed?.code || '',
            ward_id: bed?.ward_id?.toString() || '',
            bed_type_id: bed?.bed_type_id?.toString() || '',
            status: bed?.status || 'Available',
        });

    const close = () => {
        onClose();
        clearErrors();
        reset();
    };

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        const options = { onSuccess: () => close(), preserveScroll: true };

        if (bed) {
            put(route('beds.update', bed.id), options);
        } else {
            post(route('beds.store'), options);
        }
    };

    return (
        <Dialog
            open={open}
            onClose={close}
            title={bed ? t('page.beds.edit_bed') : t('page.beds.add_bed')}
            description={t('page.beds.subtitle')}
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
                    <Button type="submit" form="bed-form" loading={processing}>
                        {processing
                            ? t('page.beds.saving')
                            : t('common.save_changes')}
                    </Button>
                </>
            }
        >
            <form id="bed-form" onSubmit={handleSubmit}>
                <FieldSet columns={2}>
                    <Field
                        label={t('page.beds.bed_code')}
                        htmlFor="bed-code"
                        required
                        error={errors.code}
                        className="sm:col-span-2"
                    >
                        <Input
                            id="bed-code"
                            value={data.code}
                            onChange={(event) =>
                                setData('code', event.target.value)
                            }
                            placeholder="e.g. BED-101"
                            invalid={Boolean(errors.code)}
                            autoFocus
                        />
                    </Field>

                    <Field
                        label={t('page.beds.assign_ward')}
                        htmlFor="bed-ward"
                        error={errors.ward_id}
                    >
                        <Select
                            id="bed-ward"
                            value={data.ward_id}
                            onChange={(event) =>
                                setData('ward_id', event.target.value)
                            }
                            invalid={Boolean(errors.ward_id)}
                            placeholder={t('page.beds.select_ward')}
                        >
                            {wards.map((ward) => (
                                <option key={ward.id} value={ward.id}>
                                    {ward.name}
                                </option>
                            ))}
                        </Select>
                    </Field>

                    <Field
                        label={t('page.beds.bed_type')}
                        htmlFor="bed-type"
                        error={errors.bed_type_id}
                    >
                        <Select
                            id="bed-type"
                            value={data.bed_type_id}
                            onChange={(event) =>
                                setData('bed_type_id', event.target.value)
                            }
                            invalid={Boolean(errors.bed_type_id)}
                            placeholder={t('page.beds.select_type')}
                        >
                            {bedTypes.map((type) => (
                                <option key={type.id} value={type.id}>
                                    {type.name}
                                </option>
                            ))}
                        </Select>
                    </Field>

                    <Field
                        label={t('page.beds.status')}
                        htmlFor="bed-status"
                        error={errors.status}
                        className="sm:col-span-2"
                    >
                        <Select
                            id="bed-status"
                            value={data.status}
                            onChange={(event) =>
                                setData('status', event.target.value)
                            }
                            invalid={Boolean(errors.status)}
                        >
                            <option value="Available">
                                {t('page.beds.available')}
                            </option>
                            <option value="Occupied">
                                {t('page.beds.occupied')}
                            </option>
                            <option value="Maintenance">
                                {t('page.beds.maintenance')}
                            </option>
                        </Select>
                    </Field>
                </FieldSet>
            </form>
        </Dialog>
    );
}

interface BedTransferDialogProps {
    open: boolean;
    bed: Bed | null;
    wards: Option[];
    onClose: () => void;
}

/** BedTransferDialog — moves a bed to another ward. */
export function BedTransferDialog({
    open,
    bed,
    wards,
    onClose,
}: BedTransferDialogProps) {
    const { t } = useTranslation();

    const { data, setData, post, processing, errors, reset } = useForm({
        ward_id: bed?.ward_id?.toString() || '',
    });

    const close = () => {
        onClose();
        reset();
    };

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();

        if (!bed) {
            return;
        }

        post(route('beds.transfer', bed.id), {
            onSuccess: () => close(),
            preserveScroll: true,
        });
    };

    return (
        <Dialog
            open={open}
            onClose={close}
            title={t('page.beds.transfer_bed')}
            description={
                bed
                    ? `${t('page.beds.bed_label')} ${bed.code} · ${t(
                          'page.beds.current_ward',
                      )}: ${bed.ward?.name || t('page.beds.none')}`
                    : undefined
            }
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
                        form="bed-transfer-form"
                        loading={processing}
                    >
                        {processing
                            ? t('page.beds.transferring')
                            : t('page.beds.confirm_transfer')}
                    </Button>
                </>
            }
        >
            <form id="bed-transfer-form" onSubmit={handleSubmit}>
                <FieldSet columns={1}>
                    <Field
                        label={t('page.beds.transfer_to_ward')}
                        htmlFor="transfer-ward"
                        required
                        error={errors.ward_id}
                    >
                        <Select
                            id="transfer-ward"
                            value={data.ward_id}
                            onChange={(event) =>
                                setData('ward_id', event.target.value)
                            }
                            invalid={Boolean(errors.ward_id)}
                            required
                            placeholder={t('page.beds.select_new_ward')}
                        >
                            {wards.map((ward) => (
                                <option
                                    key={ward.id}
                                    value={ward.id}
                                    disabled={ward.id === bed?.ward_id}
                                >
                                    {ward.name}
                                    {ward.id === bed?.ward_id
                                        ? ` (${t('page.beds.current')})`
                                        : ''}
                                </option>
                            ))}
                        </Select>
                    </Field>
                </FieldSet>
            </form>
        </Dialog>
    );
}
