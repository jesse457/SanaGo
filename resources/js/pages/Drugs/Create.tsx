import {
    AdjustmentsHorizontalIcon,
    ArchiveBoxArrowDownIcon,
    CubeIcon,
    PlusIcon,
    RectangleStackIcon,
    TagIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import {
    Button,
    EmptyState,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SectionCard,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { CreateBatchCard } from './components/CreateBatchCard';
import type { CreateBatchValues } from './components/CreateBatchCard';
import { DrugImageUpload } from './components/DrugImageUpload';

interface Batch extends CreateBatchValues {
    id: string;
}

interface DrugForm {
    name: string;
    dosage_units: string;
    min_stock_level: string;
    description: string;
    batches: Batch[];
}

const emptyBatch = (): Batch => ({
    id: `batch-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
    batch_number: '',
    expiry_date: '',
    quantity: '',
    purchase_price: '',
    sale_price: '',
    manufactured_date: '',
    notes: '',
});

export default function CreateDrug() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } = useForm<
        DrugForm & { image: File | null }
    >({
        name: '',
        dosage_units: '',
        min_stock_level: '',
        description: '',
        batches: [],
        image: null as File | null,
    });

    const [imagePreview, setImagePreview] = useState<string | null>(null);

    const addBatch = () => {
        setData('batches', [...data.batches, emptyBatch()]);
    };

    const removeBatch = (index: number) => {
        const newBatches = data.batches.filter((_, i) => i !== index);
        setData('batches', newBatches);
    };

    const updateBatch = (
        index: number,
        field: keyof CreateBatchValues,
        value: string,
    ) => {
        const newBatches = [...data.batches];
        newBatches[index] = { ...newBatches[index], [field]: value };
        setData('batches', newBatches);
    };

    const batchError = (index: number, field: keyof CreateBatchValues) =>
        errors[`batches.${index}.${field}` as keyof typeof errors];

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post(route('drugs.store'), {
            forceFormData: true,
            onSuccess: () => {
                reset();
                setImagePreview(null);
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('addNewDrug')}
            headerSubtitle={t('createNewMedicationEntry')}
        >
            <Head title={t('addNewDrug')} />

            <div className="mx-auto max-w-5xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={CubeIcon}
                    eyebrow={t('manageDrugs')}
                    title={t('addNewDrug')}
                    description={t('createNewMedicationEntry')}
                    breadcrumbs={[
                        { label: t('manageDrugs'), href: route('drugs.index') },
                        { label: t('addNewDrug') },
                    ]}
                />

                <form onSubmit={submit} className="space-y-5">
                    <SectionCard title={t('medicationDetails')} icon={CubeIcon}>
                        <FieldSet columns={2}>
                            <Field
                                className="sm:col-span-2"
                                label={t('drugName')}
                                htmlFor="drug-name"
                                required
                                error={errors.name}
                            >
                                <Input
                                    id="drug-name"
                                    value={data.name}
                                    invalid={Boolean(errors.name)}
                                    onChange={(e) =>
                                        setData('name', e.target.value)
                                    }
                                    placeholder={t('egParacetamol')}
                                />
                            </Field>

                            <Field
                                label={t('dosageUnit')}
                                htmlFor="drug-dosage-unit"
                                required
                                error={errors.dosage_units}
                            >
                                <Input
                                    id="drug-dosage-unit"
                                    icon={AdjustmentsHorizontalIcon}
                                    value={data.dosage_units}
                                    invalid={Boolean(errors.dosage_units)}
                                    onChange={(e) =>
                                        setData('dosage_units', e.target.value)
                                    }
                                    placeholder={t('egMgTabletsMl')}
                                />
                            </Field>

                            <Field
                                label={t('minimumStockLevel')}
                                htmlFor="drug-min-stock"
                                required
                                error={errors.min_stock_level}
                            >
                                <Input
                                    id="drug-min-stock"
                                    type="number"
                                    min="0"
                                    icon={ArchiveBoxArrowDownIcon}
                                    value={data.min_stock_level}
                                    invalid={Boolean(errors.min_stock_level)}
                                    onChange={(e) =>
                                        setData(
                                            'min_stock_level',
                                            e.target.value,
                                        )
                                    }
                                    placeholder={t('egFifty')}
                                    className="tabular-nums"
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    <SectionCard
                        title={t('batchInformation')}
                        icon={RectangleStackIcon}
                        description={`${data.batches.length} ${t('batches')}`}
                        actions={
                            <Button
                                type="button"
                                variant="secondary"
                                size="sm"
                                icon={PlusIcon}
                                onClick={addBatch}
                            >
                                {t('addBatch')}
                            </Button>
                        }
                    >
                        {data.batches.length === 0 ? (
                            <EmptyState
                                panel={false}
                                icon={RectangleStackIcon}
                                title={t('noBatchesAdded')}
                                description={errors.batches}
                                className="py-8"
                            />
                        ) : (
                            <div className="space-y-4">
                                {data.batches.map((batch, index) => (
                                    <CreateBatchCard
                                        key={batch.id}
                                        index={index}
                                        values={batch}
                                        onChange={(field, value) =>
                                            updateBatch(index, field, value)
                                        }
                                        onRemove={() => removeBatch(index)}
                                        errorFor={(field) =>
                                            batchError(index, field)
                                        }
                                        hasErrors={Object.keys(errors).some(
                                            (k) =>
                                                k.startsWith(
                                                    `batches.${index}`,
                                                ),
                                        )}
                                    />
                                ))}
                            </div>
                        )}
                    </SectionCard>

                    <SectionCard
                        title={t('description')}
                        description={t('provideAdditionalDetails')}
                        icon={TagIcon}
                    >
                        <Field
                            label={t('description')}
                            htmlFor="drug-description"
                            optional
                            error={errors.description}
                        >
                            <Textarea
                                id="drug-description"
                                value={data.description}
                                invalid={Boolean(errors.description)}
                                onChange={(e) =>
                                    setData('description', e.target.value)
                                }
                                placeholder={t('provideAdditionalDetails')}
                                rows={3}
                            />
                        </Field>
                    </SectionCard>

                    <SectionCard
                        title={t('drugImage')}
                        description={t('optional')}
                        icon={TagIcon}
                    >
                        <DrugImageUpload
                            preview={imagePreview}
                            uploadLabel={t('uploadImage')}
                            previewAlt={t('preview')}
                            removeLabel={t('drugImage')}
                            onSelect={(file) => {
                                setData('image', file);
                                setImagePreview(URL.createObjectURL(file));
                            }}
                            onClear={() => {
                                setImagePreview(null);
                                setData('image', null);
                            }}
                        />
                    </SectionCard>

                    <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <Button variant="secondary" href={route('drugs.index')}>
                            {t('cancel')}
                        </Button>
                        <Button type="submit" loading={processing}>
                            {processing
                                ? `${t('creating')}...`
                                : t('createDrug')}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
