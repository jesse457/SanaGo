import { PlusIcon } from '@heroicons/react/24/outline';
import { useState } from 'react';
import { Alert, Button, Field, Input, Select } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import type { CatalogueService, ChargeCategory, ChargeDraft } from './types';

/**
 * The heading of a charge, in the cashier's words.
 *
 * Keys are literal so the translation test can see them; the value sent to the
 * server is the enum case the API validates against.
 */
const CATEGORY_LABELS: Record<string, { key: string; label: string }> = {
    admission: {
        key: 'page.caisse.category_admission',
        label: 'Admission fee',
    },
    bed: { key: 'page.caisse.category_bed', label: 'Bed / night' },
    consultation: {
        key: 'page.caisse.category_consultation',
        label: 'Consultation',
    },
    procedure: { key: 'page.caisse.category_procedure', label: 'Procedure' },
    nursing: { key: 'page.caisse.category_nursing', label: 'Nursing care' },
    observation: {
        key: 'page.caisse.category_observation',
        label: 'Observation',
    },
    surgery: { key: 'page.caisse.category_surgery', label: 'Surgery' },
    emergency: { key: 'page.caisse.category_emergency', label: 'Emergency' },
    laboratory: { key: 'page.caisse.category_laboratory', label: 'Laboratory' },
    radiology: { key: 'page.caisse.category_radiology', label: 'Radiology' },
    pharmacy: { key: 'page.caisse.category_pharmacy', label: 'Pharmacy' },
    other: { key: 'page.caisse.category_other', label: 'Other' },
};

const emptyDraft = (category: string): ChargeDraft => ({
    category,
    description: '',
    quantity: '1',
    unit_price: '',
});

interface ChargeComposerProps {
    categories: ChargeCategory[];
    services: CatalogueService[];
    busy: boolean;
    error?: string | null;
    onAddCharge: (line: ChargeDraft) => void;
    onAddService: (serviceId: number) => void;
}

/**
 * ChargeComposer — write a charge the price list does not know about.
 *
 * Nothing is pre-decided: the cashier names the line, picks the heading, says
 * how many and at what price. A bill for a stay is built one consumed item at a
 * time, then settled in one payment.
 */
export function ChargeComposer({
    categories,
    services,
    busy,
    error,
    onAddCharge,
    onAddService,
}: ChargeComposerProps) {
    const { t } = useTranslation();
    const [draft, setDraft] = useState<ChargeDraft>(() =>
        emptyDraft(categories[0]?.value ?? 'other'),
    );
    const [serviceToAdd, setServiceToAdd] = useState('');

    const set = <K extends keyof ChargeDraft>(
        field: K,
        value: ChargeDraft[K],
    ) => setDraft((current) => ({ ...current, [field]: value }));

    const quantity = Number(draft.quantity || 0);
    const unitPrice = Number(draft.unit_price || 0);
    const lineTotal = Math.max(0, quantity * unitPrice);
    const canAdd = draft.description.trim().length > 0 && quantity >= 1;

    const submit = () => {
        if (!canAdd) {
            return;
        }

        onAddCharge({
            ...draft,
            description: draft.description.trim(),
            unit_price: String(unitPrice),
        });
        setDraft(emptyDraft(draft.category));
    };

    return (
        <div className="space-y-4">
            <div className="grid gap-3 sm:grid-cols-2">
                <Field
                    className="sm:col-span-2"
                    label={t('page.caisse.charge_description', 'What was used')}
                    htmlFor="charge-description"
                    required
                    hint={t(
                        'page.caisse.charge_description_hint',
                        'Free text — an admission fee, oxygen, a dressing, anything consumed during the stay.',
                    )}
                >
                    <Input
                        id="charge-description"
                        value={draft.description}
                        onChange={(event) =>
                            set('description', event.target.value)
                        }
                        placeholder={t(
                            'page.caisse.charge_description_placeholder',
                            'e.g. Admission fee — general ward',
                        )}
                        onKeyDown={(event) => {
                            if (event.key === 'Enter') {
                                event.preventDefault();
                                submit();
                            }
                        }}
                    />
                </Field>

                <Field
                    label={t('page.caisse.charge_category', 'Heading')}
                    htmlFor="charge-category"
                    required
                >
                    <Select
                        id="charge-category"
                        value={draft.category}
                        onChange={(event) =>
                            set('category', event.target.value)
                        }
                    >
                        {categories.map((category) => {
                            const label = CATEGORY_LABELS[category.value];

                            return (
                                <option
                                    key={category.value}
                                    value={category.value}
                                >
                                    {label === undefined
                                        ? category.value
                                        : t(label.key, label.label)}
                                </option>
                            );
                        })}
                    </Select>
                </Field>

                <div className="grid grid-cols-2 gap-3">
                    <Field
                        label={t('page.caisse.charge_quantity', 'Quantity')}
                        htmlFor="charge-quantity"
                        required
                    >
                        <Input
                            id="charge-quantity"
                            type="number"
                            min={1}
                            step={1}
                            value={draft.quantity}
                            onChange={(event) =>
                                set('quantity', event.target.value)
                            }
                            className="tabular-nums"
                        />
                    </Field>

                    <Field
                        label={t('page.caisse.charge_price', 'Unit price')}
                        htmlFor="charge-price"
                        required
                    >
                        <Input
                            id="charge-price"
                            type="number"
                            min={0}
                            step={1}
                            value={draft.unit_price}
                            onChange={(event) =>
                                set('unit_price', event.target.value)
                            }
                            placeholder="0"
                            className="tabular-nums"
                        />
                    </Field>
                </div>
            </div>

            {error && <Alert tone="critical">{error}</Alert>}

            <div className="flex flex-wrap items-center justify-between gap-2">
                <p className="text-[12px] text-ink-muted tabular-nums">
                    {t('page.caisse.charge_line_total', 'Line total')}{' '}
                    <span className="font-semibold text-ink">
                        {lineTotal.toLocaleString()} FCFA
                    </span>
                </p>
                <Button
                    type="button"
                    variant="secondary"
                    icon={PlusIcon}
                    loading={busy}
                    disabled={!canAdd || busy}
                    onClick={submit}
                >
                    {t('page.caisse.add_charge', 'Add to bill')}
                </Button>
            </div>

            {services.length > 0 && (
                <div className="flex flex-col gap-2 border-t border-line pt-3 sm:flex-row sm:items-end">
                    <Field
                        className="flex-1"
                        label={t(
                            'page.caisse.price_list',
                            'Or pick a priced service',
                        )}
                        htmlFor="add-service"
                    >
                        <Select
                            id="add-service"
                            value={serviceToAdd}
                            onChange={(event) =>
                                setServiceToAdd(event.target.value)
                            }
                        >
                            <option value="">
                                {t(
                                    'page.caisse.pick_service',
                                    'Pick a service from the price list...',
                                )}
                            </option>
                            {services.map((service) => (
                                <option key={service.id} value={service.id}>
                                    {service.name} —{' '}
                                    {service.price.toLocaleString()} FCFA
                                </option>
                            ))}
                        </Select>
                    </Field>
                    <Button
                        type="button"
                        variant="ghost"
                        icon={PlusIcon}
                        disabled={serviceToAdd === ''}
                        onClick={() => {
                            onAddService(Number(serviceToAdd));
                            setServiceToAdd('');
                        }}
                    >
                        {t('page.caisse.add_to_payment', 'Add to payment')}
                    </Button>
                </div>
            )}
        </div>
    );
}
