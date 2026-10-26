import { ArrowRightIcon } from '@heroicons/react/24/outline';
import { useId, useRef, useState } from 'react';
import type { AppScreenKey } from '@/Components/marketing/AppWindow';
import { AppWindow } from '@/Components/marketing/AppWindow';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

interface TourStop {
    key: AppScreenKey;
    label: string;
    line: string;
}

/**
 * WorkspaceTour — the product, walked through in the order a patient moves.
 *
 * This is the page's central claim made visible: the frames on the right are
 * assembled from the workspace's own UI kit (tables, badges, stat cards, the
 * same money formatting), and the list on the left is a real tablist, so the
 * whole showcase is navigable from the keyboard.
 *
 * The index on the left is deliberately vertical rather than a row of chips:
 * the six stops are a sequence, and a sequence reads top to bottom.
 */
export function WorkspaceTour({ className }: { className?: string }) {
    const { t } = useTranslation();
    const id = useId();
    const tabRefs = useRef<Record<string, HTMLButtonElement | null>>({});

    const stops: TourStop[] = [
        {
            key: 'patients',
            label: t('mk.tour.patients.label', 'Patient directory'),
            line: t(
                'mk.tour.patients.line',
                'One file opened at the front desk and never re-typed: guardian, blood group, allergies, insurer and every visit behind a single search box.',
            ),
        },
        {
            key: 'consultation',
            label: t('mk.tour.consultation.label', 'Consultation'),
            line: t(
                'mk.tour.consultation.line',
                'Vitals, diagnosis, prescription and the follow-up date in one sitting, with the allergy already on screen before the drug is chosen.',
            ),
        },
        {
            key: 'laboratory',
            label: t('mk.tour.laboratory.label', 'Laboratory'),
            line: t(
                'mk.tour.laboratory.line',
                'Requests arrive already attached to the patient. Technicians work a list and enter results against the test definition, not as free text.',
            ),
        },
        {
            key: 'pharmacy',
            label: t('mk.tour.pharmacy.label', 'Pharmacy'),
            line: t(
                'mk.tour.pharmacy.line',
                'Dispense from the signed prescription, batch by batch, while stock, expiries and reorder levels move with the same transaction.',
            ),
        },
        {
            key: 'wards',
            label: t('mk.tour.wards.label', 'Wards and beds'),
            line: t(
                'mk.tour.wards.line',
                'Occupancy is live, so the ward knows which bed is free before the patient reaches the door, and the discharge list is written down.',
            ),
        },
        {
            key: 'finance',
            label: t('mk.tour.finance.label', 'Invoices'),
            line: t(
                'mk.tour.finance.line',
                'One invoice per episode with partial payments, the balance carried forward and the insurer claim keeping its supporting lines.',
            ),
        },
        {
            key: 'caisse',
            label: t('mk.tour.caisse.label', 'Caisse'),
            line: t(
                'mk.tour.caisse.line',
                'Open a shift, take the payments in cash or mobile money, close against a counted drawer and keep that shift its own ledger.',
            ),
        },
    ];

    const [active, setActive] = useState<AppScreenKey>(stops[0].key);
    const current = stops.find((stop) => stop.key === active) ?? stops[0];

    const focusStop = (key: AppScreenKey) => {
        setActive(key);
        tabRefs.current[key]?.focus();
    };

    const handleKeyDown = (event: React.KeyboardEvent, index: number) => {
        const keys = [
            'ArrowDown',
            'ArrowUp',
            'ArrowRight',
            'ArrowLeft',
            'Home',
            'End',
        ];

        if (!keys.includes(event.key)) {
            return;
        }

        event.preventDefault();

        let next = index;

        if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
            next = (index + 1) % stops.length;
        } else if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
            next = (index - 1 + stops.length) % stops.length;
        } else if (event.key === 'Home') {
            next = 0;
        } else if (event.key === 'End') {
            next = stops.length - 1;
        }

        focusStop(stops[next].key);
    };

    return (
        <div
            className={cn(
                'grid items-start gap-8 lg:grid-cols-12 lg:gap-10',
                className,
            )}
        >
            <div
                role="tablist"
                aria-orientation="vertical"
                aria-label={t('mk.tour.tablist', 'A tour of the workspace')}
                className="lg:col-span-4"
            >
                {stops.map((stop, index) => {
                    const isActive = stop.key === current.key;

                    return (
                        <button
                            key={stop.key}
                            ref={(node) => {
                                tabRefs.current[stop.key] = node;
                            }}
                            type="button"
                            role="tab"
                            id={`${id}-tab-${stop.key}`}
                            aria-selected={isActive}
                            aria-controls={`${id}-panel`}
                            tabIndex={isActive ? 0 : -1}
                            onClick={() => setActive(stop.key)}
                            onKeyDown={(event) => handleKeyDown(event, index)}
                            className={cn(
                                'relative block w-full cursor-pointer border-t border-line px-1 py-3.5 text-left transition-colors duration-200 ease-premium last:border-b focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none',
                                isActive
                                    ? 'bg-surface-muted'
                                    : 'hover:bg-surface-hover',
                            )}
                        >
                            <span
                                aria-hidden
                                className={cn(
                                    'absolute top-0 bottom-0 left-0 w-0.5 origin-top rounded-full bg-accent transition-transform duration-300 ease-premium',
                                    isActive ? 'scale-y-100' : 'scale-y-0',
                                )}
                            />
                            <span className="flex items-baseline justify-between gap-3 pl-3">
                                <span
                                    className={cn(
                                        'flex items-baseline gap-2.5 text-[13.5px]',
                                        isActive
                                            ? 'font-semibold text-ink'
                                            : 'font-medium text-ink-muted',
                                    )}
                                >
                                    <span
                                        data-numeric
                                        className="font-mono text-[9px] tracking-[0.14em] text-ink-subtle tabular-nums"
                                    >
                                        {String(index + 1).padStart(2, '0')}
                                    </span>
                                    {stop.label}
                                </span>
                                <ArrowRightIcon
                                    className={cn(
                                        'mt-1 h-3 w-3 shrink-0 transition-[opacity,transform] duration-200 ease-premium',
                                        isActive
                                            ? 'text-accent'
                                            : 'text-ink-subtle opacity-0',
                                    )}
                                />
                            </span>
                            {isActive && (
                                <span className="mt-1.5 block max-w-[42ch] pl-3 text-[12.5px] leading-relaxed text-ink-muted">
                                    {stop.line}
                                </span>
                            )}
                        </button>
                    );
                })}
            </div>

            <div
                role="tabpanel"
                id={`${id}-panel`}
                aria-labelledby={`${id}-tab-${current.key}`}
                className="lg:col-span-8"
            >
                <AppWindow key={current.key} screen={current.key} />
                <p className="mt-3 font-mono text-[9.5px] tracking-[0.14em] text-ink-subtle uppercase">
                    {t(
                        'mk.tour.caption',
                        'Drawn from the working UI kit · same tables, badges and totals as the live hospital',
                    )}
                </p>
            </div>
        </div>
    );
}
