import {
    ArrowPathIcon,
    BanknotesIcon,
    CalculatorIcon,
    CheckCircleIcon,
    ClockIcon,
    MagnifyingGlassIcon,
    PlusIcon,
    ReceiptPercentIcon,
    UserPlusIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router, useForm } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import type { FormEvent } from 'react';
import {
    Alert,
    Badge,
    Button,
    Checkbox,
    Dialog,
    EmptyState,
    Field,
    Input,
    PageHeader,
    SectionCard,
    Select,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { ShiftHistoryCard } from '@/pages/Caisse/components';
import type { CashShift, CashPayment } from '@/types';

interface CashPointOption {
    id: number;
    name: string;
    code: string;
    module: string | null;
}

interface PatientOption {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid?: string | null;
    phone?: string | null;
    gender?: string | null;
}

/** One unpaid line on a patient's running bill. */
interface Charge {
    id: number;
    invoice_id: number;
    invoice_number?: string | null;
    module: string;
    description: string;
    quantity: number;
    unit_price: number;
    total_price: number;
}

interface CatalogueService {
    id: number;
    name: string;
    code?: string | null;
    category?: string | null;
    price: number;
    requires_upfront_payment: boolean;
}

interface Props {
    activeShift: (CashShift & { cash_point?: CashPointOption }) | null;
    todayPayments: CashPayment[];
    shiftHistory: CashShift[];
    cashPoints: CashPointOption[];
    defaultCashPointId: number;
    selectedPatient: PatientOption | null;
}

const PAYMENT_METHODS = [
    { value: 'cash', label: 'Cash (Espèces)' },
    { value: 'mobile_money', label: 'Mobile Money (Orange Money / MTN MoMo)' },
    { value: 'bank_transfer', label: 'Bank Transfer (Virement)' },
];

const MODULE_TONES: Record<string, BadgeTone> = {
    laboratory: 'accent',
    pharmacy: 'positive',
    radiology: 'warning',
    consultation: 'neutral',
    admission: 'muted',
    surgery: 'critical',
    emergency: 'critical',
    other: 'muted',
};

function money(value: number): string {
    return `${Math.round(value).toLocaleString('fr-FR')} FCFA`;
}

function rows<T>(data: unknown, key: string): T[] {
    if (Array.isArray(data)) {
        return data as T[];
    }

    const record = data as Record<string, unknown> | null;

    return (record?.[key] as T[]) ?? [];
}

/**
 * Cash desk.
 *
 * The counter flow the hospital actually runs: open a shift on a till, find or
 * register the patient, tick the services they are paying for, take the money
 * and print a receipt. Amounts always come from the bill or the priced
 * catalogue — the cashier never types an arbitrary amount.
 */
export default function CaisseIndex({
    activeShift,
    todayPayments,
    shiftHistory,
    cashPoints,
    defaultCashPointId,
    selectedPatient,
}: Props) {
    const { t } = useTranslation();

    // ── Shift ───────────────────────────────────────────────────────────────
    const {
        data: shiftData,
        setData: setShiftData,
        post: postShift,
        processing: shiftProcessing,
        errors: shiftErrors,
        reset: resetShift,
    } = useForm({
        cash_point_id: String(defaultCashPointId),
        opening_balance: '0',
    });

    const {
        data: closeData,
        setData: setCloseData,
        post: postClose,
        processing: closeProcessing,
        reset: resetClose,
    } = useForm({
        closing_balance: '',
    });

    const [showOpenModal, setShowOpenModal] = useState(false);
    const [showCloseModal, setShowCloseModal] = useState(false);

    // ── Counter: patient → charges → payment ────────────────────────────────
    const [patient, setPatient] = useState<PatientOption | null>(
        selectedPatient ?? null,
    );
    const [query, setQuery] = useState('');
    const [results, setResults] = useState<PatientOption[]>([]);
    const [searching, setSearching] = useState(false);
    // `null` means "not loaded yet" so the loading state needs no effect.
    const [charges, setCharges] = useState<Charge[] | null>(null);
    const [services, setServices] = useState<CatalogueService[]>([]);
    const [selectedCharges, setSelectedCharges] = useState<number[]>([]);
    const [pickedServices, setPickedServices] = useState<number[]>([]);
    const [serviceToAdd, setServiceToAdd] = useState('');
    const [amountTendered, setAmountTendered] = useState('');
    const [method, setMethod] = useState('cash');
    const [paying, setPaying] = useState(false);
    const [payError, setPayError] = useState<string | null>(null);

    // One counter attempt keeps its id across retries, so a double click or a
    // dropped response can never charge the patient twice.
    const clientUuid = useRef<string>(crypto.randomUUID());

    const searchPatients = useMemo(
        () =>
            debounce((value: string) => {
                if (value.trim().length < 2) {
                    setResults([]);
                    setSearching(false);

                    return;
                }

                setSearching(true);

                fetch(
                    `${route('caisse.patients')}?search=${encodeURIComponent(value)}`,
                    {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    },
                )
                    .then((response) => (response.ok ? response.json() : null))
                    .then((data) =>
                        setResults(rows<PatientOption>(data, 'patients')),
                    )
                    .catch(() => setResults([]))
                    .finally(() => setSearching(false));
            }, 300),
        [],
    );

    useEffect(() => {
        searchPatients(query);
    }, [query, searchPatients]);

    useEffect(() => () => searchPatients.cancel(), [searchPatients]);

    const loadCharges = useCallback((target: PatientOption) => {
        fetch(route('caisse.outstanding', target.id), {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
            .then((response) => (response.ok ? response.json() : null))
            .then((data) => {
                const loaded = rows<Charge>(data, 'charges');

                setCharges(loaded);
                setServices(rows<CatalogueService>(data, 'services'));
                // Everything the patient already owes is ticked by default.
                setSelectedCharges(loaded.map((charge) => charge.id));
            })
            .catch(() => {
                setCharges([]);
                setSelectedCharges([]);
            });
    }, []);

    useEffect(() => {
        if (patient !== null) {
            loadCharges(patient);
        }
    }, [patient, loadCharges]);

    const selectPatient = (next: PatientOption) => {
        clientUuid.current = crypto.randomUUID();
        setPatient(next);
        setCharges(null);
        setSelectedCharges([]);
        setQuery('');
        setResults([]);
        setPickedServices([]);
        setAmountTendered('');
        setPayError(null);
        loadCharges(next);
    };

    const clearPatient = () => {
        setPatient(null);
        setCharges(null);
        setSelectedCharges([]);
        setPickedServices([]);
        setAmountTendered('');
        setPayError(null);
    };

    const addedServices = useMemo(
        () => services.filter((service) => pickedServices.includes(service.id)),
        [services, pickedServices],
    );

    const chargesTotal = (charges ?? [])
        .filter((charge) => selectedCharges.includes(charge.id))
        .reduce((sum, charge) => sum + charge.total_price, 0);

    const servicesTotal = addedServices.reduce(
        (sum, service) => sum + service.price,
        0,
    );

    const total = chargesTotal + servicesTotal;
    const tendered = Number(amountTendered || 0);
    const change = Math.max(0, Math.round(tendered - total));

    const toggleCharge = (id: number, checked: boolean) => {
        setSelectedCharges((current) =>
            checked
                ? [...current, id]
                : current.filter((value) => value !== id),
        );
    };

    const addService = () => {
        const id = Number(serviceToAdd);

        if (!id || pickedServices.includes(id)) {
            return;
        }

        setPickedServices((current) => [...current, id]);
        setServiceToAdd('');
    };

    const removeService = (id: number) => {
        setPickedServices((current) => current.filter((value) => value !== id));
    };

    const handleOpenShift = (event: FormEvent) => {
        event.preventDefault();
        postShift(route('caisse.open'), {
            onSuccess: () => {
                setShowOpenModal(false);
                resetShift();
            },
        });
    };

    const handleCloseShift = (event: FormEvent) => {
        event.preventDefault();
        postClose(route('caisse.close'), {
            onSuccess: () => {
                setShowCloseModal(false);
                resetClose();
            },
        });
    };

    const handleSettle = (event: FormEvent) => {
        event.preventDefault();

        if (patient === null) {
            return;
        }

        setPayError(null);
        setPaying(true);

        router.post(
            route('caisse.settle'),
            {
                patient_id: patient.id,
                item_ids: selectedCharges,
                service_ids: pickedServices,
                payment_method: method,
                amount_tendered: tendered,
                client_uuid: clientUuid.current,
            },
            {
                preserveScroll: true,
                onError: (errors) => {
                    setPayError(
                        errors.amount_tendered ??
                            errors.item_ids ??
                            errors.service_ids ??
                            t(
                                'page.caisse.payment_failed',
                                'The payment could not be recorded.',
                            ),
                    );
                },
                onSuccess: () => {
                    clientUuid.current = crypto.randomUUID();
                },
                onFinish: () => setPaying(false),
            },
        );
    };

    const todayTotal = todayPayments.reduce(
        (sum, payment) => sum + Number(payment.amount_paid),
        0,
    );
    const shiftTotal =
        activeShift?.payments?.reduce(
            (sum, payment) => sum + Number(payment.amount_paid),
            0,
        ) ?? 0;

    const canPay =
        activeShift !== null &&
        patient !== null &&
        total > 0 &&
        tendered >= total &&
        !paying;

    return (
        <AppLayout
            headerTitle={t('page.caisse.title', 'Caisse — Cash register')}
            headerSubtitle={t(
                'page.caisse.subtitle',
                'Collect payments, issue receipts and reconcile the till.',
            )}
        >
            <Head title={t('page.caisse.title', 'Caisse')} />

            <div className="flex flex-col gap-6">
                <PageHeader
                    eyebrow={t('page.caisse.eyebrow', 'Cash desk')}
                    title={t('page.caisse.heading', 'Cash register')}
                    description={t(
                        'page.caisse.description',
                        'Find the patient, tick what they are paying for, take the money and print the receipt.',
                    )}
                    breadcrumbs={[{ label: t('page.caisse.title', 'Caisse') }]}
                    meta={
                        activeShift ? (
                            <span className="inline-flex items-center gap-2">
                                <Badge tone="positive" dot>
                                    {t('page.caisse.shift_open', 'Shift open')}
                                </Badge>
                                <span>
                                    {activeShift.cash_point?.name ??
                                        activeShift.payment_point}
                                </span>
                            </span>
                        ) : (
                            <Badge tone="critical" dot>
                                {t('page.caisse.shift_closed', 'No open shift')}
                            </Badge>
                        )
                    }
                />

                <StatGrid columns={4}>
                    <StatCard
                        icon={ClockIcon}
                        label={t('page.caisse.stat_shift', 'Shift status')}
                        value={
                            activeShift
                                ? t('page.caisse.open', 'Open')
                                : t('page.caisse.closed', 'Closed')
                        }
                        tone={activeShift ? 'positive' : 'critical'}
                    />
                    <StatCard
                        icon={BanknotesIcon}
                        label={t('page.caisse.stat_opening', 'Opening balance')}
                        value={money(Number(activeShift?.opening_balance ?? 0))}
                        tone="accent"
                    />
                    <StatCard
                        icon={ArrowPathIcon}
                        label={t('page.caisse.stat_collected', 'Collected')}
                        value={money(shiftTotal)}
                        tone="accent"
                    />
                    <StatCard
                        icon={CalculatorIcon}
                        label={t('page.caisse.stat_today', "Today's total")}
                        value={money(todayTotal)}
                        tone="warning"
                    />
                </StatGrid>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div className="space-y-6 lg:col-span-2">
                        <SectionCard
                            title={t(
                                'page.caisse.collect_title',
                                'Collect payment',
                            )}
                            description={t(
                                'page.caisse.collect_description',
                                'Amounts come from the bill and the price list, never typed by hand.',
                            )}
                            icon={ReceiptPercentIcon}
                            actions={
                                !activeShift ? (
                                    <Button
                                        icon={CheckCircleIcon}
                                        onClick={() => setShowOpenModal(true)}
                                    >
                                        {t(
                                            'page.caisse.open_shift',
                                            'Open shift',
                                        )}
                                    </Button>
                                ) : (
                                    <Button
                                        variant="danger"
                                        icon={XMarkIcon}
                                        onClick={() => setShowCloseModal(true)}
                                    >
                                        {t(
                                            'page.caisse.close_shift',
                                            'Close shift',
                                        )}
                                    </Button>
                                )
                            }
                        >
                            {activeShift === null ? (
                                <Alert tone="warning">
                                    {t(
                                        'page.caisse.open_shift_first',
                                        'Open a shift on a till before taking payments.',
                                    )}
                                </Alert>
                            ) : patient === null ? (
                                <div className="space-y-4">
                                    <Field
                                        label={t(
                                            'page.caisse.find_patient',
                                            'Find patient',
                                        )}
                                        htmlFor="patient-search"
                                        hint={t(
                                            'page.caisse.find_patient_hint',
                                            'Name, patient ID or phone number.',
                                        )}
                                    >
                                        <Input
                                            id="patient-search"
                                            icon={MagnifyingGlassIcon}
                                            value={query}
                                            onChange={(event) =>
                                                setQuery(event.target.value)
                                            }
                                            placeholder={t(
                                                'page.caisse.find_patient_placeholder',
                                                'Search a patient...',
                                            )}
                                            autoComplete="off"
                                        />
                                    </Field>

                                    {searching && (
                                        <p className="text-[12.5px] text-ink-subtle">
                                            {t(
                                                'page.caisse.searching',
                                                'Searching...',
                                            )}
                                        </p>
                                    )}

                                    {results.length > 0 && (
                                        <ul className="divide-y divide-line overflow-hidden rounded-lg border border-line">
                                            {results.map((option) => (
                                                <li key={option.id}>
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            selectPatient(
                                                                option,
                                                            )
                                                        }
                                                        className="flex w-full items-center justify-between gap-3 px-3.5 py-3 text-left transition-colors duration-150 hover:bg-surface-hover"
                                                    >
                                                        <span className="min-w-0">
                                                            <span className="block truncate text-[13px] font-medium text-ink">
                                                                {
                                                                    option.first_name
                                                                }{' '}
                                                                {
                                                                    option.last_name
                                                                }
                                                            </span>
                                                            <span className="block truncate font-mono text-[11px] text-ink-subtle">
                                                                {option.patient_uid ??
                                                                    '—'}
                                                                {option.phone
                                                                    ? ` · ${option.phone}`
                                                                    : ''}
                                                            </span>
                                                        </span>
                                                        <Badge tone="accent">
                                                            {t(
                                                                'page.caisse.select',
                                                                'Select',
                                                            )}
                                                        </Badge>
                                                    </button>
                                                </li>
                                            ))}
                                        </ul>
                                    )}

                                    <Link
                                        href={`${route('patients.create')}?redirect_to=caisse`}
                                        className="inline-flex items-center gap-2 text-[12.5px] font-medium text-accent-ink hover:underline"
                                    >
                                        <UserPlusIcon className="h-4 w-4" />
                                        {t(
                                            'page.caisse.register_patient',
                                            'Register a new patient',
                                        )}
                                    </Link>
                                </div>
                            ) : (
                                <form
                                    onSubmit={handleSettle}
                                    className="space-y-5"
                                >
                                    <div className="flex items-center justify-between gap-3 rounded-lg border border-line bg-surface-muted px-3.5 py-3">
                                        <div className="min-w-0">
                                            <p className="truncate text-[13px] font-semibold text-ink">
                                                {patient.first_name}{' '}
                                                {patient.last_name}
                                            </p>
                                            <p className="truncate font-mono text-[11px] text-ink-subtle">
                                                {patient.patient_uid ?? '—'}
                                                {patient.phone
                                                    ? ` · ${patient.phone}`
                                                    : ''}
                                            </p>
                                        </div>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            onClick={clearPatient}
                                        >
                                            {t(
                                                'page.caisse.change_patient',
                                                'Change patient',
                                            )}
                                        </Button>
                                    </div>

                                    <div>
                                        <p className="mb-2 text-[10.5px] font-semibold tracking-[0.13em] text-ink-subtle uppercase">
                                            {t(
                                                'page.caisse.outstanding',
                                                'To pay',
                                            )}
                                        </p>

                                        {charges === null ? (
                                            <p className="text-[12.5px] text-ink-subtle">
                                                {t(
                                                    'page.caisse.loading_charges',
                                                    'Loading the bill...',
                                                )}
                                            </p>
                                        ) : charges.length === 0 ? (
                                            <EmptyState
                                                panel={false}
                                                title={t(
                                                    'page.caisse.no_charges',
                                                    'Nothing on the bill',
                                                )}
                                                description={t(
                                                    'page.caisse.no_charges_desc',
                                                    'Add a service below, or send the patient to the doctor first.',
                                                )}
                                                className="py-8"
                                            />
                                        ) : (
                                            <ul className="divide-y divide-line overflow-hidden rounded-lg border border-line">
                                                {charges.map((charge) => (
                                                    <li
                                                        key={charge.id}
                                                        className="flex items-center gap-3 px-3.5 py-2.5"
                                                    >
                                                        <Checkbox
                                                            id={`charge-${charge.id}`}
                                                            checked={selectedCharges.includes(
                                                                charge.id,
                                                            )}
                                                            onChange={(event) =>
                                                                toggleCharge(
                                                                    charge.id,
                                                                    event.target
                                                                        .checked,
                                                                )
                                                            }
                                                        />
                                                        <label
                                                            htmlFor={`charge-${charge.id}`}
                                                            className="min-w-0 flex-1 cursor-pointer"
                                                        >
                                                            <span className="block truncate text-[12.5px] font-medium text-ink">
                                                                {
                                                                    charge.description
                                                                }
                                                            </span>
                                                            <span className="mt-0.5 flex items-center gap-2">
                                                                <Badge
                                                                    tone={
                                                                        MODULE_TONES[
                                                                            charge
                                                                                .module
                                                                        ] ??
                                                                        'muted'
                                                                    }
                                                                    size="sm"
                                                                >
                                                                    {
                                                                        charge.module
                                                                    }
                                                                </Badge>
                                                                {charge.invoice_number && (
                                                                    <span className="font-mono text-[10.5px] text-ink-subtle">
                                                                        {
                                                                            charge.invoice_number
                                                                        }
                                                                    </span>
                                                                )}
                                                            </span>
                                                        </label>
                                                        <span className="shrink-0 text-[12.5px] font-semibold text-ink tabular-nums">
                                                            {money(
                                                                charge.total_price,
                                                            )}
                                                        </span>
                                                    </li>
                                                ))}
                                            </ul>
                                        )}
                                    </div>

                                    <div>
                                        <p className="mb-2 text-[10.5px] font-semibold tracking-[0.13em] text-ink-subtle uppercase">
                                            {t(
                                                'page.caisse.add_service',
                                                'Add a service',
                                            )}
                                        </p>
                                        <div className="flex gap-2">
                                            <Select
                                                id="add-service"
                                                value={serviceToAdd}
                                                onChange={(event) =>
                                                    setServiceToAdd(
                                                        event.target.value,
                                                    )
                                                }
                                                className="flex-1"
                                            >
                                                <option value="">
                                                    {t(
                                                        'page.caisse.pick_service',
                                                        'Pick a service from the price list...',
                                                    )}
                                                </option>
                                                {services.map((service) => (
                                                    <option
                                                        key={service.id}
                                                        value={service.id}
                                                    >
                                                        {service.name} —{' '}
                                                        {money(service.price)}
                                                    </option>
                                                ))}
                                            </Select>
                                            <Button
                                                type="button"
                                                variant="secondary"
                                                icon={PlusIcon}
                                                onClick={addService}
                                                disabled={!serviceToAdd}
                                            >
                                                {t('page.caisse.add', 'Add')}
                                            </Button>
                                        </div>

                                        {addedServices.length > 0 && (
                                            <ul className="mt-2 space-y-1.5">
                                                {addedServices.map(
                                                    (service) => (
                                                        <li
                                                            key={service.id}
                                                            className="flex items-center justify-between gap-3 rounded-md bg-surface-muted px-3 py-2"
                                                        >
                                                            <span className="min-w-0 truncate text-[12.5px] text-ink">
                                                                {service.name}
                                                            </span>
                                                            <span className="flex shrink-0 items-center gap-2">
                                                                <span className="text-[12.5px] font-semibold text-ink tabular-nums">
                                                                    {money(
                                                                        service.price,
                                                                    )}
                                                                </span>
                                                                <button
                                                                    type="button"
                                                                    onClick={() =>
                                                                        removeService(
                                                                            service.id,
                                                                        )
                                                                    }
                                                                    className="rounded p-1 text-ink-subtle transition-colors hover:text-critical-ink"
                                                                    aria-label={t(
                                                                        'page.caisse.remove',
                                                                        'Remove',
                                                                    )}
                                                                >
                                                                    <XMarkIcon className="h-3.5 w-3.5" />
                                                                </button>
                                                            </span>
                                                        </li>
                                                    ),
                                                )}
                                            </ul>
                                        )}
                                    </div>

                                    <div className="grid gap-3 border-t border-line pt-4 sm:grid-cols-3">
                                        <Field
                                            label={t(
                                                'page.caisse.total',
                                                'Total to pay',
                                            )}
                                            htmlFor="counter-total"
                                        >
                                            <div
                                                id="counter-total"
                                                className="flex h-10 items-center text-[18px] font-semibold text-ink tabular-nums"
                                            >
                                                {money(total)}
                                            </div>
                                        </Field>

                                        <Field
                                            label={t(
                                                'page.caisse.tendered',
                                                'Amount received',
                                            )}
                                            htmlFor="amount_tendered"
                                            required
                                        >
                                            <Input
                                                id="amount_tendered"
                                                type="number"
                                                min={0}
                                                step={1}
                                                value={amountTendered}
                                                onChange={(event) =>
                                                    setAmountTendered(
                                                        event.target.value,
                                                    )
                                                }
                                                placeholder={String(total)}
                                            />
                                        </Field>

                                        <Field
                                            label={t(
                                                'page.caisse.method',
                                                'Method',
                                            )}
                                            htmlFor="payment_method"
                                            required
                                        >
                                            <Select
                                                id="payment_method"
                                                value={method}
                                                onChange={(event) =>
                                                    setMethod(
                                                        event.target.value,
                                                    )
                                                }
                                            >
                                                {PAYMENT_METHODS.map(
                                                    (option) => (
                                                        <option
                                                            key={option.value}
                                                            value={option.value}
                                                        >
                                                            {option.label}
                                                        </option>
                                                    ),
                                                )}
                                            </Select>
                                        </Field>
                                    </div>

                                    {tendered > 0 && (
                                        <div className="flex items-center justify-between rounded-lg bg-surface-inset px-3.5 py-2.5">
                                            <span className="text-[12px] text-ink-muted">
                                                {t(
                                                    'page.caisse.change',
                                                    'Change to hand back',
                                                )}
                                            </span>
                                            <span className="text-[15px] font-semibold text-ink tabular-nums">
                                                {money(change)}
                                            </span>
                                        </div>
                                    )}

                                    {payError && (
                                        <Alert tone="critical">
                                            {payError}
                                        </Alert>
                                    )}

                                    <div className="flex items-center justify-end gap-2 border-t border-line pt-4">
                                        <p className="mr-auto text-[11.5px] text-ink-subtle">
                                            {t(
                                                'page.caisse.receipt_note',
                                                'A receipt is issued automatically.',
                                            )}
                                        </p>
                                        <Button
                                            type="submit"
                                            icon={BanknotesIcon}
                                            loading={paying}
                                            disabled={!canPay}
                                        >
                                            {t(
                                                'page.caisse.pay',
                                                'Pay service',
                                            )}
                                        </Button>
                                    </div>
                                </form>
                            )}
                        </SectionCard>

                        {activeShift &&
                            activeShift.payments &&
                            activeShift.payments.length > 0 && (
                                <SectionCard
                                    title={t(
                                        'page.caisse.shift_payments',
                                        'This shift',
                                    )}
                                    icon={BanknotesIcon}
                                >
                                    <Table bare>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHeaderCell>
                                                    {t(
                                                        'page.caisse.col_patient',
                                                        'Patient',
                                                    )}
                                                </TableHeaderCell>
                                                <TableHeaderCell>
                                                    {t(
                                                        'page.caisse.col_invoice',
                                                        'Invoice',
                                                    )}
                                                </TableHeaderCell>
                                                <TableHeaderCell numeric>
                                                    {t(
                                                        'page.caisse.col_amount',
                                                        'Amount',
                                                    )}
                                                </TableHeaderCell>
                                                <TableHeaderCell numeric>
                                                    {t(
                                                        'page.caisse.col_method',
                                                        'Method',
                                                    )}
                                                </TableHeaderCell>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {activeShift.payments.map(
                                                (payment) => (
                                                    <TableRow key={payment.id}>
                                                        <TableCell muted>
                                                            {payment.patient
                                                                ? `${payment.patient.first_name} ${payment.patient.last_name}`
                                                                : '—'}
                                                        </TableCell>
                                                        <TableCell muted>
                                                            {payment.invoice
                                                                ?.invoice_number ??
                                                                '—'}
                                                        </TableCell>
                                                        <TableCell numeric>
                                                            {money(
                                                                Number(
                                                                    payment.amount_paid,
                                                                ),
                                                            )}
                                                        </TableCell>
                                                        <TableCell numeric>
                                                            <span className="text-[11px] font-medium tracking-[0.1em] text-ink-muted uppercase">
                                                                {payment.payment_method ===
                                                                'mobile_money'
                                                                    ? 'MoMo'
                                                                    : payment.payment_method}
                                                            </span>
                                                        </TableCell>
                                                    </TableRow>
                                                ),
                                            )}
                                        </TableBody>
                                    </Table>
                                </SectionCard>
                            )}
                    </div>

                    <SectionCard
                        title={t('page.caisse.shift_history', 'Shift history')}
                        icon={ClockIcon}
                    >
                        {shiftHistory.length === 0 ? (
                            <EmptyState
                                panel={false}
                                title={t(
                                    'page.caisse.no_shifts',
                                    'No previous shifts.',
                                )}
                                className="px-0 py-8"
                            />
                        ) : (
                            <div className="space-y-3">
                                {shiftHistory.map((shift) => (
                                    <ShiftHistoryCard
                                        key={shift.id}
                                        shift={shift}
                                        labels={{
                                            open: t('page.caisse.open', 'Open'),
                                            closed: t(
                                                'page.caisse.closed',
                                                'Closed',
                                            ),
                                            opening: t(
                                                'page.caisse.opening',
                                                'Opening',
                                            ),
                                            closing: t(
                                                'page.caisse.closing',
                                                'Closing',
                                            ),
                                            payments: t(
                                                'page.caisse.payments',
                                                'Payments',
                                            ),
                                        }}
                                    />
                                ))}
                            </div>
                        )}
                    </SectionCard>
                </div>
            </div>

            <Dialog
                open={showOpenModal}
                onClose={() => setShowOpenModal(false)}
                title={t('page.caisse.open_shift_title', 'Open cash shift')}
                size="md"
            >
                <form onSubmit={handleOpenShift} className="space-y-4">
                    <Field
                        label={t('page.caisse.till', 'Till (cash point)')}
                        htmlFor="cash_point_id"
                        required
                        error={shiftErrors.cash_point_id}
                    >
                        <Select
                            id="cash_point_id"
                            value={shiftData.cash_point_id}
                            onChange={(event) =>
                                setShiftData(
                                    'cash_point_id',
                                    event.target.value,
                                )
                            }
                            invalid={Boolean(shiftErrors.cash_point_id)}
                        >
                            {cashPoints.map((point) => (
                                <option key={point.id} value={point.id}>
                                    {point.name}
                                </option>
                            ))}
                        </Select>
                    </Field>

                    <Field
                        label={t(
                            'page.caisse.opening_balance',
                            'Opening balance (XAF)',
                        )}
                        htmlFor="opening_balance"
                        required
                        error={shiftErrors.opening_balance}
                    >
                        <Input
                            id="opening_balance"
                            type="number"
                            min="0"
                            step="1"
                            value={shiftData.opening_balance}
                            onChange={(event) =>
                                setShiftData(
                                    'opening_balance',
                                    event.target.value,
                                )
                            }
                            invalid={Boolean(shiftErrors.opening_balance)}
                        />
                    </Field>

                    <div className="flex justify-end gap-2 border-t border-line pt-4">
                        <Button
                            type="button"
                            variant="secondary"
                            onClick={() => setShowOpenModal(false)}
                        >
                            {t('page.caisse.cancel', 'Cancel')}
                        </Button>
                        <Button
                            type="submit"
                            loading={shiftProcessing}
                            disabled={shiftProcessing}
                        >
                            {t('page.caisse.open_shift', 'Open shift')}
                        </Button>
                    </div>
                </form>
            </Dialog>

            <Dialog
                open={showCloseModal}
                onClose={() => setShowCloseModal(false)}
                title={t('page.caisse.close_shift_title', 'Close cash shift')}
                size="md"
            >
                <form onSubmit={handleCloseShift} className="space-y-4">
                    <p className="text-[12.5px] text-ink-muted">
                        {t(
                            'page.caisse.close_shift_hint',
                            'Count the cash in the drawer. Leave the field empty to accept the expected total.',
                        )}
                    </p>

                    <Field
                        label={t(
                            'page.caisse.counted_balance',
                            'Counted balance (XAF)',
                        )}
                        htmlFor="closing_balance"
                    >
                        <Input
                            id="closing_balance"
                            type="number"
                            min="0"
                            step="1"
                            value={closeData.closing_balance}
                            onChange={(event) =>
                                setCloseData(
                                    'closing_balance',
                                    event.target.value,
                                )
                            }
                        />
                    </Field>

                    <div className="flex justify-end gap-2 border-t border-line pt-4">
                        <Button
                            type="button"
                            variant="secondary"
                            onClick={() => setShowCloseModal(false)}
                        >
                            {t('page.caisse.cancel', 'Cancel')}
                        </Button>
                        <Button
                            type="submit"
                            variant="danger"
                            loading={closeProcessing}
                            disabled={closeProcessing}
                        >
                            {t('page.caisse.close_shift', 'Close shift')}
                        </Button>
                    </div>
                </form>
            </Dialog>
        </AppLayout>
    );
}
