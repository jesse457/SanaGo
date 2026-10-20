import { BanknotesIcon, ArrowLeftIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import { useMemo } from 'react';
import type { FormEvent } from 'react';
import {
    Alert,
    Badge,
    Button,
    Input,
    PageHeader,
    SectionCard,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface PriceRow {
    id: number;
    name: string;
    code: string;
    category: string;
    default_price: number;
    price: number;
    is_override: boolean;
}

interface Props {
    doctor: {
        id: number;
        name: string;
        roles: string[];
    };
    prices: PriceRow[];
    consultationService: { id: number; name: string; code: string } | null;
}

function money(value: number): string {
    return `${Math.round(value).toLocaleString('fr-FR')} FCFA`;
}

const CATEGORY_LABELS: Record<string, string> = {
    consultation: 'Consultation',
    procedure: 'Procedure',
    nursing: 'Nursing care',
    emergency: 'Emergency',
    imaging: 'Imaging',
    radiology: 'Radiology',
    lab: 'Laboratory',
    medication: 'Medication',
    bed: 'Bed',
    other: 'Other',
};

/**
 * A doctor's own price list.
 *
 * The catalogue price is the reference; anything different is this doctor's
 * override and is what the counter bills for their consultations and
 * procedures. Clearing a value back to the catalogue price removes the
 * override.
 */
export default function UserPricing({
    doctor,
    prices,
    consultationService,
}: Props) {
    const { t } = useTranslation();

    const initial = useMemo(
        () =>
            Object.fromEntries(
                prices.map((row) => [String(row.id), String(row.price)]),
            ) as Record<string, string>,
        [prices],
    );

    const { data, setData, put, processing, errors } = useForm({
        prices: initial,
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();

        put(route('users.pricing.update', doctor.id), { preserveScroll: true });
    };

    const overrideCount = prices.filter(
        (row) => data.prices[String(row.id)] !== String(row.default_price),
    ).length;

    return (
        <AppLayout
            headerTitle={t('page.doctor_pricing.title', 'Doctor prices')}
            headerSubtitle={doctor.name}
        >
            <Head
                title={`${doctor.name} · ${t('page.doctor_pricing.title', 'Doctor prices')}`}
            />

            <div className="flex flex-col gap-6 pb-12">
                <PageHeader
                    eyebrow={t('page.doctor_pricing.eyebrow', 'Tariffs')}
                    title={doctor.name}
                    description={t(
                        'page.doctor_pricing.description',
                        'What this doctor charges. Leave a value at the catalogue price to use the hospital default.',
                    )}
                    breadcrumbs={[
                        { label: t('page.doctor_pricing.users', 'Users') },
                        { label: doctor.name },
                    ]}
                    meta={
                        <>
                            {doctor.roles.map((role) => (
                                <Badge key={role} tone="accent">
                                    {role}
                                </Badge>
                            ))}
                            <Badge
                                tone={overrideCount > 0 ? 'positive' : 'muted'}
                            >
                                {overrideCount}{' '}
                                {t(
                                    'page.doctor_pricing.overrides',
                                    'custom prices',
                                )}
                            </Badge>
                        </>
                    }
                    actions={
                        <Button
                            variant="secondary"
                            icon={ArrowLeftIcon}
                            href="/users"
                        >
                            {t('page.doctor_pricing.back', 'Back to users')}
                        </Button>
                    }
                />

                {consultationService && (
                    <Alert tone="info">
                        {t(
                            'page.doctor_pricing.consultation_note',
                            'Appointments are billed as consultations: the price set for',
                        )}{' '}
                        <strong>{consultationService.name}</strong>{' '}
                        {t(
                            'page.doctor_pricing.consultation_note_end',
                            'is what the counter collects when a patient books by phone.',
                        )}
                    </Alert>
                )}

                <form onSubmit={submit}>
                    <SectionCard
                        title={t(
                            'page.doctor_pricing.price_list',
                            'Price list',
                        )}
                        description={t(
                            'page.doctor_pricing.price_list_desc',
                            'Applies to this doctor only. Laboratory, beds and drugs follow the catalogue.',
                        )}
                        icon={BanknotesIcon}
                        actions={
                            <Button
                                type="submit"
                                loading={processing}
                                disabled={processing}
                            >
                                {t('page.doctor_pricing.save', 'Save prices')}
                            </Button>
                        }
                    >
                        <Table bare>
                            <TableHeader>
                                <TableRow>
                                    <TableHeaderCell>
                                        {t(
                                            'page.doctor_pricing.col_service',
                                            'Service',
                                        )}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t(
                                            'page.doctor_pricing.col_category',
                                            'Category',
                                        )}
                                    </TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        {t(
                                            'page.doctor_pricing.col_default',
                                            'Catalogue',
                                        )}
                                    </TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        {t(
                                            'page.doctor_pricing.col_price',
                                            'This doctor',
                                        )}
                                    </TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {prices.map((row) => {
                                    const value = data.prices[String(row.id)];
                                    const isOverride =
                                        value !== String(row.default_price);

                                    return (
                                        <TableRow key={row.id}>
                                            <TableCell>
                                                <div className="min-w-0">
                                                    <p className="text-[13px] font-medium text-ink">
                                                        {row.name}
                                                    </p>
                                                    <p className="font-mono text-[10.5px] text-ink-subtle">
                                                        {row.code}
                                                    </p>
                                                </div>
                                            </TableCell>
                                            <TableCell muted>
                                                {CATEGORY_LABELS[
                                                    row.category
                                                ] ?? row.category}
                                            </TableCell>
                                            <TableCell
                                                numeric
                                                muted
                                                className="tabular-nums"
                                            >
                                                {money(row.default_price)}
                                            </TableCell>
                                            <TableCell numeric>
                                                <Input
                                                    type="number"
                                                    min="0"
                                                    step="1"
                                                    value={value}
                                                    onChange={(event) =>
                                                        setData('prices', {
                                                            ...data.prices,
                                                            [String(row.id)]:
                                                                event.target
                                                                    .value,
                                                        })
                                                    }
                                                    invalid={Boolean(
                                                        errors[
                                                            `prices.${row.id}` as keyof typeof errors
                                                        ],
                                                    )}
                                                    className="ml-auto w-32 text-right"
                                                    aria-label={`${row.name} price`}
                                                />
                                                {isOverride && (
                                                    <span className="mt-1 block text-[10.5px] font-medium text-positive-ink">
                                                        {t(
                                                            'page.doctor_pricing.custom',
                                                            'custom',
                                                        )}
                                                    </span>
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>

                        <div className="mt-4 flex flex-col gap-2 border-t border-line pt-4 sm:flex-row sm:items-center sm:justify-between">
                            <p className="text-[11.5px] text-ink-subtle">
                                {t(
                                    'page.doctor_pricing.hint',
                                    'Values equal to the catalogue price are stored as “use hospital default”.',
                                )}
                            </p>
                            <div className="flex gap-2">
                                <Button
                                    type="button"
                                    variant="ghost"
                                    href={`/users`}
                                >
                                    {t('page.doctor_pricing.cancel', 'Cancel')}
                                </Button>
                                <Button
                                    type="submit"
                                    loading={processing}
                                    disabled={processing}
                                >
                                    {t(
                                        'page.doctor_pricing.save',
                                        'Save prices',
                                    )}
                                </Button>
                            </div>
                        </div>
                    </SectionCard>
                </form>
            </div>
        </AppLayout>
    );
}
