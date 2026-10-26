import {
    BanknotesIcon,
    CheckIcon,
    PencilIcon,
    PlusIcon,
    TagIcon,
    TrashIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router, useForm } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import { useEffect, useMemo, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Badge,
    Button,
    EmptyState,
    PageHeader,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    swal,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { formatMoney } from '@/pages/Finance/components/format';

interface ServiceItem {
    id: number;
    name: string;
    code: string;
    category: string;
    module: string;
    default_price: number;
    is_active: boolean;
    requires_upfront_payment: boolean;
    description: string | null;
}

interface Props {
    services: {
        data: ServiceItem[];
        links: { url: string | null; label: string; active: boolean }[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
    };
    filters: { search: string; module: string };
    modules: { value: string; label: string }[];
}

const MODULE_TONES: Record<string, BadgeTone> = {
    consultation: 'accent',
    laboratory: 'positive',
    radiology: 'warning',
    pharmacy: 'positive',
    admission: 'muted',
    surgery: 'critical',
    other: 'neutral',
};

export default function ServicesIndex({ services, filters, modules }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(filters.search ?? '');
    const [module, setModule] = useState(filters.module || 'all');

    // The row being priced right now: one price editor at a time.
    const [editingId, setEditingId] = useState<number | null>(null);

    const applyFilters = useMemo(
        () =>
            debounce((nextModule: string, nextSearch: string) => {
                router.get(
                    route('finance.services.index'),
                    {
                        module: nextModule !== 'all' ? nextModule : undefined,
                        search: nextSearch || undefined,
                    },
                    {
                        preserveState: true,
                        preserveScroll: true,
                        replace: true,
                    },
                );
            }, 300),
        [],
    );

    useEffect(() => () => applyFilters.cancel(), [applyFilters]);

    const handleSearch = (value: string) => {
        setSearch(value);
        setEditingId(null);
        applyFilters(module, value);
    };

    const handleModule = (value: string) => {
        setModule(value);
        setEditingId(null);
        applyFilters(value, search);
    };

    const priceForm = useForm<{ default_price: string }>({
        default_price: '',
    });

    const openPriceEditor = (service: ServiceItem) => {
        setEditingId(service.id);
        priceForm.setData('default_price', String(service.default_price));
    };

    const closePriceEditor = () => {
        setEditingId(null);
        priceForm.reset();
        priceForm.clearErrors();
    };

    const savePrice = (service: ServiceItem) => {
        priceForm.put(route('finance.services.price', service.id), {
            preserveScroll: true,
            onSuccess: closePriceEditor,
        });
    };

    const tabs = [{ value: 'all', label: t('All modules') }, ...modules];
    const hasRows = services.data.length > 0;
    const moduleLabel = (value: string) =>
        modules.find((m) => m.value === value)?.label ?? value;

    const handleDelete = async (service: ServiceItem) => {
        const confirmed = await swal.confirm({
            tone: 'warning',
            danger: true,
            title: t(
                'finance.services.index.delete_service',
                'Delete this service?',
            ),
            message: t('finance.services.index.delete_confirm'),
            confirmLabel: t('common.delete', 'Delete'),
            cancelLabel: t('common.cancel', 'Cancel'),
        });

        if (!confirmed) {
            return;
        }

        router.delete(route('finance.services.destroy', service.id), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout
            headerTitle={t('nav.services_catalog')}
            headerSubtitle={t('finance.services.index.subtitle')}
        >
            <Head title={t('nav.services_catalog')} />

            <div className="space-y-5">
                <PageHeader
                    title={t('nav.services_catalog')}
                    description={t('finance.services.index.subtitle')}
                    breadcrumbs={[
                        { label: t('finance.services.index.finance') },
                        { label: t('nav.services_catalog') },
                    ]}
                    actions={
                        <Button
                            icon={PlusIcon}
                            href={route('finance.services.create')}
                        >
                            {t('nav.new_service')}
                        </Button>
                    }
                />

                <Toolbar>
                    <div className="min-w-0 flex-1 overflow-x-auto sm:flex-none">
                        <div className="flex items-center gap-1">
                            {tabs.map((tab) => {
                                const active = module === tab.value;

                                return (
                                    <Button
                                        key={tab.value}
                                        size="sm"
                                        variant={active ? 'secondary' : 'ghost'}
                                        aria-pressed={active}
                                        onClick={() => handleModule(tab.value)}
                                        className="whitespace-nowrap"
                                    >
                                        {tab.label}
                                    </Button>
                                );
                            })}
                        </div>
                    </div>
                    <ToolbarSpacer />
                    <ToolbarSearch
                        value={search}
                        onChange={handleSearch}
                        placeholder={t(
                            'finance.services.index.search_placeholder',
                        )}
                    />
                </Toolbar>

                {hasRows ? (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>
                                    {t('finance.services.index.service')}
                                </TableHeaderCell>
                                <TableHeaderCell>{t('Module')}</TableHeaderCell>
                                <TableHeaderCell>
                                    {t('finance.services.index.code')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('finance.services.index.price')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('finance.services.index.payment')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('finance.services.index.status')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('common.actions')}
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            {services.data.map((service) => (
                                <TableRow key={service.id}>
                                    <TableCell>
                                        <p className="text-[13px] font-medium text-ink">
                                            {service.name}
                                        </p>
                                        {service.description && (
                                            <p className="mt-0.5 line-clamp-1 max-w-xs text-[11.5px] text-ink-subtle">
                                                {service.description}
                                            </p>
                                        )}
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            tone={
                                                MODULE_TONES[service.module] ??
                                                'neutral'
                                            }
                                            size="sm"
                                        >
                                            {moduleLabel(service.module)}
                                        </Badge>
                                    </TableCell>
                                    <TableCell muted>
                                        <span className="font-mono text-[12px] tracking-tight text-ink-muted">
                                            {service.code}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        {editingId === service.id ? (
                                            <form
                                                onSubmit={(event) => {
                                                    event.preventDefault();
                                                    savePrice(service);
                                                }}
                                                className="flex items-center gap-1"
                                            >
                                                <input
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    value={
                                                        priceForm.data
                                                            .default_price
                                                    }
                                                    onChange={(event) =>
                                                        priceForm.setData(
                                                            'default_price',
                                                            event.target.value,
                                                        )
                                                    }
                                                    aria-label={t(
                                                        'finance.services.index.price',
                                                    )}
                                                    className="w-24 rounded-md border border-line-strong bg-surface px-2 py-1 text-right text-[12.5px] text-ink tabular-nums focus:border-accent focus:outline-none"
                                                    autoFocus
                                                />
                                                <Button
                                                    type="submit"
                                                    variant="ghost"
                                                    size="icon"
                                                    icon={CheckIcon}
                                                    aria-label={t('Save price')}
                                                    disabled={
                                                        priceForm.processing
                                                    }
                                                />
                                                <Button
                                                    type="button"
                                                    onClick={closePriceEditor}
                                                    variant="ghost"
                                                    size="icon"
                                                    aria-label={t(
                                                        'Cancel edit',
                                                    )}
                                                    icon={XMarkIcon}
                                                />
                                            </form>
                                        ) : (
                                            <div className="flex items-center justify-end gap-1.5">
                                                <span className="font-semibold text-ink tabular-nums">
                                                    {formatMoney(
                                                        service.default_price,
                                                    )}
                                                </span>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    icon={BanknotesIcon}
                                                    aria-label={t(
                                                        'Change price',
                                                    )}
                                                    onClick={() =>
                                                        openPriceEditor(service)
                                                    }
                                                />
                                            </div>
                                        )}
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            tone={
                                                service.requires_upfront_payment
                                                    ? 'warning'
                                                    : 'positive'
                                            }
                                            size="sm"
                                        >
                                            {service.requires_upfront_payment
                                                ? t(
                                                      'finance.services.index.payment_before',
                                                  )
                                                : t(
                                                      'finance.services.index.payment_after',
                                                  )}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            tone={
                                                service.is_active
                                                    ? 'positive'
                                                    : 'critical'
                                            }
                                            size="sm"
                                        >
                                            {service.is_active
                                                ? t(
                                                      'finance.services.index.status_active',
                                                  )
                                                : t(
                                                      'finance.services.index.status_inactive',
                                                  )}
                                        </Badge>
                                    </TableCell>
                                    <TableCell numeric>
                                        <div className="flex items-center justify-end gap-1">
                                            <Link
                                                href={route(
                                                    'finance.services.edit',
                                                    service.id,
                                                )}
                                                aria-label={t('common.edit')}
                                                className="inline-flex h-8 w-8 items-center justify-center rounded-md text-ink-subtle transition-colors duration-150 ease-premium hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                                            >
                                                <PencilIcon className="h-4 w-4" />
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                icon={TrashIcon}
                                                aria-label={t('common.delete')}
                                                onClick={() =>
                                                    handleDelete(service)
                                                }
                                            />
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                ) : (
                    <EmptyState
                        icon={TagIcon}
                        title={t('finance.services.index.no_services')}
                        description={t('finance.services.index.subtitle')}
                        action={{
                            label: t(
                                'finance.services.index.create_first_service',
                            ),
                            href: route('finance.services.create'),
                        }}
                    />
                )}

                {priceForm.errors.default_price && (
                    <p className="text-xs font-semibold text-critical-ink">
                        {priceForm.errors.default_price}
                    </p>
                )}

                <Pagination
                    links={services.links ?? []}
                    meta={{
                        from: services.from,
                        to: services.to,
                        total: services.total,
                    }}
                />
            </div>
        </AppLayout>
    );
}
