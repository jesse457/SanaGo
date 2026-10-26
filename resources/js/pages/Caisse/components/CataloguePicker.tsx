import {
    BeakerIcon,
    CheckIcon,
    MagnifyingGlassIcon,
    PlusIcon,
} from '@heroicons/react/24/outline';
import { useMemo, useState } from 'react';
import { Badge, Button, EmptyState, Input } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import type { CatalogueItem, CounterModule } from './types';

const MODULE_TONES: Record<string, BadgeTone> = {
    consultation: 'neutral',
    laboratory: 'accent',
    radiology: 'warning',
    pharmacy: 'positive',
    admission: 'muted',
    surgery: 'critical',
    other: 'muted',
};

function money(value: number): string {
    return `${Math.round(value).toLocaleString('fr-FR')} FCFA`;
}

/** Case- and accent-insensitive so "hemogramme" finds "Hémogramme". */
function normalise(value: string): string {
    return value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

interface CataloguePickerProps {
    items: CatalogueItem[];
    modules: CounterModule[];
    /** Keys already in the basket, so a line cannot be added twice. */
    picked: string[];
    disabled?: boolean;
    onPick: (item: CatalogueItem) => void;
}

/**
 * CataloguePicker — everything the till may sell, in one search box.
 *
 * Drugs are read from the pharmacy stock, lab tests from the laboratory
 * catalogue, bed days from the patient's running stay and the rest from the
 * price list. The cashier searches, not scrolls: a hospital price list has
 * hundreds of rows.
 */
export function CataloguePicker({
    items,
    modules,
    picked,
    disabled = false,
    onPick,
}: CataloguePickerProps) {
    const { t } = useTranslation();
    const [query, setQuery] = useState('');
    const [module, setModule] = useState('all');

    const visible = useMemo(() => {
        const needle = normalise(query.trim());

        return items.filter((item) => {
            if (module !== 'all' && item.module !== module) {
                return false;
            }

            if (needle === '') {
                return true;
            }

            return (
                normalise(item.name).includes(needle) ||
                normalise(item.code ?? '').includes(needle)
            );
        });
    }, [items, module, query]);

    const tabs = [
        { value: 'all', label: t('page.caisse.all_modules', 'All') },
        ...modules,
    ];

    return (
        <div className="space-y-3">
            <Input
                id="catalogue-search"
                icon={MagnifyingGlassIcon}
                value={query}
                onChange={(event) => setQuery(event.target.value)}
                placeholder={t(
                    'page.caisse.search_catalogue',
                    'Search a drug, a test, a bed or a service...',
                )}
                autoComplete="off"
                disabled={disabled}
            />

            <div className="flex flex-wrap items-center gap-1">
                {tabs.map((tab) => (
                    <Button
                        key={tab.value}
                        type="button"
                        size="sm"
                        variant={module === tab.value ? 'secondary' : 'ghost'}
                        aria-pressed={module === tab.value}
                        onClick={() => setModule(tab.value)}
                        disabled={disabled}
                    >
                        {tab.label}
                    </Button>
                ))}
            </div>

            {visible.length === 0 ? (
                <EmptyState
                    panel={false}
                    icon={BeakerIcon}
                    title={t(
                        'page.caisse.nothing_found',
                        'Nothing matches that search',
                    )}
                    description={t(
                        'page.caisse.nothing_found_desc',
                        'Only what the price list, the drug stock and the laboratory catalogue hold can be sold here.',
                    )}
                    className="py-8"
                />
            ) : (
                <ul className="max-h-72 divide-y divide-line overflow-y-auto rounded-lg border border-line">
                    {visible.map((item) => {
                        const alreadyPicked = picked.includes(item.key);
                        const outOfStock =
                            item.stock !== null &&
                            item.stock !== undefined &&
                            item.stock <= 0;

                        return (
                            <li
                                key={item.key}
                                className="flex items-center gap-3 px-3.5 py-2.5"
                            >
                                <div className="min-w-0 flex-1">
                                    <p className="truncate text-[12.5px] font-medium text-ink">
                                        {item.name}
                                    </p>
                                    <span className="mt-0.5 flex flex-wrap items-center gap-2">
                                        <Badge
                                            tone={
                                                MODULE_TONES[item.module] ??
                                                'muted'
                                            }
                                            size="sm"
                                        >
                                            {item.module}
                                        </Badge>
                                        {item.code && (
                                            <span className="font-mono text-[10.5px] text-ink-subtle">
                                                {item.code}
                                            </span>
                                        )}
                                        {item.stock !== null &&
                                            item.stock !== undefined && (
                                                <span
                                                    className={
                                                        outOfStock
                                                            ? 'text-[10.5px] font-medium text-critical-ink'
                                                            : 'text-[10.5px] text-ink-subtle'
                                                    }
                                                >
                                                    {outOfStock
                                                        ? t(
                                                              'page.caisse.out_of_stock',
                                                              'out of stock',
                                                          )
                                                        : `${item.stock.toLocaleString('fr-FR')} ${t('page.caisse.in_stock', 'in stock')}`}
                                                </span>
                                            )}
                                        {item.unit && (
                                            <span className="text-[10.5px] text-ink-subtle">
                                                {item.unit}
                                            </span>
                                        )}
                                    </span>
                                </div>

                                <span className="shrink-0 text-right text-[12.5px] font-semibold text-ink tabular-nums">
                                    {money(item.price)}
                                </span>

                                <Button
                                    type="button"
                                    size="sm"
                                    variant={
                                        alreadyPicked ? 'ghost' : 'secondary'
                                    }
                                    icon={alreadyPicked ? CheckIcon : PlusIcon}
                                    disabled={
                                        disabled || alreadyPicked || outOfStock
                                    }
                                    onClick={() => onPick(item)}
                                    aria-label={t('page.caisse.add', 'Add')}
                                >
                                    {alreadyPicked
                                        ? t('page.caisse.added', 'Added')
                                        : t('page.caisse.add', 'Add')}
                                </Button>
                            </li>
                        );
                    })}
                </ul>
            )}
        </div>
    );
}
