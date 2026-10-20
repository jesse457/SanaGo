import {
    CheckIcon,
    DocumentTextIcon,
    PlusIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useMemo, useState } from 'react';
import {
    Alert,
    Button,
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
    Field,
    FieldSet,
    Input,
    PageHeader,
    Select,
    SectionCard,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { InvoiceLineRow, InvoiceTotalsPanel } from '@/pages/Finance/components';
import type { InvoiceLineDraft } from '@/pages/Finance/components';

interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
}

interface Props {
    patients: Patient[];
}

const BLANK_ITEM: InvoiceLineDraft = {
    item_type: 'consultation',
    description: '',
    quantity: 1,
    unit_price: 0,
};

export default function InvoiceCreate({ patients }: Props) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } = useForm({
        patient_id: '',
        invoice_date: new Date().toISOString().slice(0, 10),
        due_date: '',
        notes: '',
        items: [{ ...BLANK_ITEM }] as InvoiceLineDraft[],
    });

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const addItem = () => {
        setData('items', [...data.items, { ...BLANK_ITEM }]);
    };

    const removeItem = (index: number) => {
        setData(
            'items',
            data.items.filter((_, position) => position !== index),
        );
    };

    const updateItem = (
        index: number,
        field: keyof InvoiceLineDraft,
        value: string | number,
    ) => {
        setData(
            'items',
            data.items.map((item, position) =>
                position === index ? { ...item, [field]: value } : item,
            ),
        );
    };

    const grandTotal = useMemo(
        () =>
            data.items.reduce(
                (sum, item) =>
                    sum +
                    (Number(item.quantity) || 0) *
                        (Number(item.unit_price) || 0),
                0,
            ),
        [data.items],
    );

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        setErrorMessage(null);
        post(route('finance.invoices.store'), {
            onSuccess: () => {
                setSuccessMessage(t('invoiceCreatedSuccessfully'));
                reset();
                setData('invoice_date', new Date().toISOString().slice(0, 10));
                setData('items', [{ ...BLANK_ITEM }]);
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('finance.invoices.create.title')}
            headerSubtitle={t('finance.invoices.create.subtitle')}
        >
            <Head title={t('finance.invoices.create.head_title')} />

            <div className="space-y-5">
                <PageHeader
                    title={t('finance.invoices.create.title')}
                    description={t('finance.invoices.create.subtitle')}
                    breadcrumbs={[
                        {
                            label: t('finance.invoices.create.finance'),
                            href: route('finance.invoices.index'),
                        },
                        { label: t('finance.invoices.create.create_invoice') },
                    ]}
                />

                {successMessage && (
                    <Alert tone="positive" title={t('common.success')}>
                        {successMessage}
                    </Alert>
                )}

                {(errorMessage || Object.keys(errors).length > 0) && (
                    <Alert tone="critical" title={t('common.error')}>
                        {errorMessage || t('common.someFieldsRequireAttention')}
                    </Alert>
                )}

                <form onSubmit={handleSubmit} className="space-y-5">
                    <SectionCard
                        title={t('finance.invoices.create.invoice_details')}
                        icon={DocumentTextIcon}
                    >
                        <FieldSet columns={2}>
                            <Field
                                label={t('finance.invoices.create.patient')}
                                htmlFor="patient_id"
                                required
                                error={errors.patient_id}
                            >
                                <Select
                                    id="patient_id"
                                    value={data.patient_id}
                                    invalid={Boolean(errors.patient_id)}
                                    onChange={(event) =>
                                        setData(
                                            'patient_id',
                                            event.target.value,
                                        )
                                    }
                                >
                                    <option value="">
                                        {t(
                                            'finance.invoices.create.select_patient',
                                        )}
                                    </option>
                                    {patients.map((patient) => (
                                        <option
                                            key={patient.id}
                                            value={String(patient.id)}
                                        >
                                            {patient.first_name}{' '}
                                            {patient.last_name} (
                                            {patient.patient_uid})
                                        </option>
                                    ))}
                                </Select>
                            </Field>

                            <Field
                                label={t(
                                    'finance.invoices.create.invoice_date',
                                )}
                                htmlFor="invoice_date"
                                required
                                error={errors.invoice_date}
                            >
                                <Input
                                    id="invoice_date"
                                    type="date"
                                    value={data.invoice_date}
                                    invalid={Boolean(errors.invoice_date)}
                                    onChange={(event) =>
                                        setData(
                                            'invoice_date',
                                            event.target.value,
                                        )
                                    }
                                />
                            </Field>

                            <Field
                                label={t('finance.invoices.create.due_date')}
                                htmlFor="due_date"
                                optional
                                error={errors.due_date}
                            >
                                <Input
                                    id="due_date"
                                    type="date"
                                    value={data.due_date}
                                    invalid={Boolean(errors.due_date)}
                                    onChange={(event) =>
                                        setData('due_date', event.target.value)
                                    }
                                />
                            </Field>

                            <Field
                                label={t('finance.invoices.create.notes')}
                                htmlFor="notes"
                                optional
                                error={errors.notes}
                            >
                                <Input
                                    id="notes"
                                    value={data.notes}
                                    invalid={Boolean(errors.notes)}
                                    placeholder={t(
                                        'finance.invoices.create.notes_placeholder',
                                    )}
                                    onChange={(event) =>
                                        setData('notes', event.target.value)
                                    }
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    <div className="grid grid-cols-1 gap-5 lg:grid-cols-3">
                        <Card className="lg:col-span-2">
                            <CardHeader
                                actions={
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        icon={PlusIcon}
                                        onClick={addItem}
                                    >
                                        {t(
                                            'finance.invoices.create.add_blank_item',
                                        )}
                                    </Button>
                                }
                            >
                                <CardTitle>
                                    {t('finance.invoices.create.line_items')}
                                </CardTitle>
                            </CardHeader>

                            <CardContent className="space-y-3">
                                {errors.items && (
                                    <Alert tone="critical">
                                        {errors.items}
                                    </Alert>
                                )}

                                {data.items.map((item, index) => (
                                    <InvoiceLineRow
                                        key={index}
                                        item={item}
                                        index={index}
                                        canRemove={data.items.length > 1}
                                        onChange={updateItem}
                                        onRemove={removeItem}
                                        descriptionPlaceholder={t(
                                            'finance.invoices.create.description_placeholder',
                                        )}
                                        labels={{
                                            type: t(
                                                'finance.invoices.create.type',
                                            ),
                                            description: t(
                                                'finance.invoices.create.description',
                                            ),
                                            quantity: t(
                                                'finance.invoices.create.qty',
                                            ),
                                            unitPrice: t(
                                                'finance.invoices.create.unit_price',
                                            ),
                                            remove: t('common.delete'),
                                        }}
                                    />
                                ))}
                            </CardContent>
                        </Card>

                        <InvoiceTotalsPanel
                            title={t('finance.invoices.create.grand_total')}
                            lines={data.items.map((item, index) => ({
                                label:
                                    item.description ||
                                    `${item.item_type.replace(/_/g, ' ')} #${index + 1}`,
                                value:
                                    (Number(item.quantity) || 0) *
                                    (Number(item.unit_price) || 0),
                            }))}
                            total={grandTotal}
                            totalLabel={t(
                                'finance.invoices.create.grand_total',
                            )}
                            footer={
                                <CardFooter>
                                    <Button
                                        variant="secondary"
                                        href={route('finance.invoices.index')}
                                    >
                                        {t('common.cancel')}
                                    </Button>
                                    <Button
                                        type="submit"
                                        icon={CheckIcon}
                                        loading={processing}
                                    >
                                        {processing
                                            ? t('common.creating')
                                            : t(
                                                  'finance.invoices.create.create_invoice_button',
                                              )}
                                    </Button>
                                </CardFooter>
                            }
                        />
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
