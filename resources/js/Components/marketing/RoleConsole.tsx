import {
    BanknotesIcon,
    BeakerIcon,
    CameraIcon,
    HeartIcon,
    CubeIcon,
    ShieldCheckIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { useId, useRef } from 'react';
import type { ComponentType, SVGProps } from 'react';
import type { AppScreenKey } from '@/Components/marketing/AppWindow';
import { AppWindow } from '@/Components/marketing/AppWindow';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

interface RolePanel {
    key: string;
    label: string;
    icon: ComponentType<SVGProps<SVGSVGElement>>;
    scope: string;
    actions: string[];
    /** The screen that desk lands on after signing in. */
    screen: AppScreenKey;
}

/**
 * RoleConsole — the workspace, per desk.
 *
 * Seven consoles, each showing the screen that role actually opens on. Tabs
 * are a real tablist: arrow keys move between them, and the panel is labelled
 * by the selected tab. Nothing is hidden behind a modal.
 */
export function RoleConsole({
    activeRole,
    onRoleChange,
    className,
}: {
    activeRole: string;
    onRoleChange: (role: string) => void;
    className?: string;
}) {
    const { t } = useTranslation();
    const id = useId();
    const tabRefs = useRef<Record<string, HTMLButtonElement | null>>({});

    const roles: RolePanel[] = [
        {
            key: 'admin',
            label: t('mk.roles.admin.label', 'Administration'),
            icon: ShieldCheckIcon,
            scope: t(
                'mk.roles.admin.scope',
                'Users, roles and the whole ledger',
            ),
            actions: [
                t(
                    'mk.roles.admin.a1',
                    'Create a role and decide exactly which desks reach which record',
                ),
                t(
                    'mk.roles.admin.a2',
                    'Read the audit trail: who opened, edited or exported a file',
                ),
                t(
                    'mk.roles.admin.a3',
                    'Watch revenue, stock value and occupancy on one board',
                ),
            ],
            screen: 'dashboard',
        },
        {
            key: 'doctor',
            label: t('mk.roles.doctor.label', 'Doctor'),
            icon: UserIcon,
            scope: t(
                'mk.roles.doctor.scope',
                "Today's list, the record, the prescription pad",
            ),
            actions: [
                t(
                    'mk.roles.doctor.a1',
                    'Open any patient with their full history already loaded',
                ),
                t(
                    'mk.roles.doctor.a2',
                    'Prescribe against real stock, with allergy warnings inline',
                ),
                t(
                    'mk.roles.doctor.a3',
                    'Order labs and imaging without leaving the consultation',
                ),
            ],
            screen: 'consultation',
        },
        {
            key: 'nurse',
            label: t('mk.roles.nurse.label', 'Nursing'),
            icon: HeartIcon,
            scope: t(
                'mk.roles.nurse.scope',
                'Ward rounds, vitals and care reports',
            ),
            actions: [
                t(
                    'mk.roles.nurse.a1',
                    'Record vitals in seconds and flag the abnormal ones',
                ),
                t(
                    'mk.roles.nurse.a2',
                    'Chart a bed for the night and hand over the shift in writing',
                ),
                t(
                    'mk.roles.nurse.a3',
                    'See which prescriptions are still waiting at the pharmacy',
                ),
            ],
            screen: 'wards',
        },
        {
            key: 'lab',
            label: t('mk.roles.lab.label', 'Laboratory'),
            icon: BeakerIcon,
            scope: t('mk.roles.lab.scope', 'Requests, specimens, results'),
            actions: [
                t(
                    'mk.roles.lab.a1',
                    'A worklist of every open request, oldest first',
                ),
                t(
                    'mk.roles.lab.a2',
                    'Enter results against the test definition, not free text',
                ),
                t(
                    'mk.roles.lab.a3',
                    'Validate and release — the doctor is notified immediately',
                ),
            ],
            screen: 'laboratory',
        },
        {
            key: 'pharmacy',
            label: t('mk.roles.pharmacy.label', 'Pharmacy'),
            icon: CubeIcon,
            scope: t('mk.roles.pharmacy.scope', 'Dispensing, batches, expiry'),
            actions: [
                t(
                    'mk.roles.pharmacy.a1',
                    'Dispense from the prescription, batch by batch',
                ),
                t(
                    'mk.roles.pharmacy.a2',
                    'Watch expiries and low stock before they become a shortage',
                ),
                t(
                    'mk.roles.pharmacy.a3',
                    'Bill the medicines into the same invoice as the consultation',
                ),
            ],
            screen: 'pharmacy',
        },
        {
            key: 'radiology',
            label: t('mk.roles.radiology.label', 'Imaging'),
            icon: CameraIcon,
            scope: t('mk.roles.radiology.scope', 'Orders, studies, reports'),
            actions: [
                t(
                    'mk.roles.radiology.a1',
                    'Queue X-ray, ultrasound, CT and MRI orders by modality',
                ),
                t(
                    'mk.roles.radiology.a2',
                    'Write findings and a conclusion, then sign the study off',
                ),
                t(
                    'mk.roles.radiology.a3',
                    'Keep every report in one searchable archive',
                ),
            ],
            screen: 'radiology',
        },
        {
            key: 'cashier',
            label: t('mk.roles.cashier.label', 'Caisse'),
            icon: BanknotesIcon,
            scope: t('mk.roles.cashier.scope', 'Payments, shifts, claims'),
            actions: [
                t(
                    'mk.roles.cashier.a1',
                    'Take a payment against the invoice, in cash or by transfer',
                ),
                t(
                    'mk.roles.cashier.a2',
                    'Open and close a shift with a counted drawer',
                ),
                t(
                    'mk.roles.cashier.a3',
                    'Prepare the insurer claim with its supporting lines',
                ),
            ],
            screen: 'caisse',
        },
    ];

    const current = roles.find((role) => role.key === activeRole) ?? roles[0];

    const handleKeyDown = (event: React.KeyboardEvent, index: number) => {
        const keys = ['ArrowRight', 'ArrowLeft', 'Home', 'End'];

        if (!keys.includes(event.key)) {
            return;
        }

        event.preventDefault();

        let next = index;

        if (event.key === 'ArrowRight') {
            next = (index + 1) % roles.length;
        } else if (event.key === 'ArrowLeft') {
            next = (index - 1 + roles.length) % roles.length;
        } else if (event.key === 'Home') {
            next = 0;
        } else if (event.key === 'End') {
            next = roles.length - 1;
        }

        onRoleChange(roles[next].key);
        tabRefs.current[roles[next].key]?.focus();
    };

    return (
        <div className={cn('flex flex-col gap-8', className)}>
            <div
                role="tablist"
                aria-label={t('mk.roles.tablist', 'Hospital roles')}
                className="-mx-5 flex gap-1 overflow-x-auto px-5 pb-1 sm:mx-0 sm:flex-wrap sm:px-0"
            >
                {roles.map((role, index) => {
                    const isActive = role.key === current.key;
                    const Icon = role.icon;

                    return (
                        <button
                            key={role.key}
                            ref={(node) => {
                                tabRefs.current[role.key] = node;
                            }}
                            type="button"
                            role="tab"
                            id={`${id}-tab-${role.key}`}
                            aria-selected={isActive}
                            aria-controls={`${id}-panel`}
                            tabIndex={isActive ? 0 : -1}
                            onClick={() => onRoleChange(role.key)}
                            onKeyDown={(event) => handleKeyDown(event, index)}
                            className={cn(
                                'inline-flex shrink-0 cursor-pointer items-center gap-2 rounded-md border px-3 py-2 text-[12.5px] font-medium transition-all duration-200 ease-premium',
                                'focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:ring-offset-2 focus-visible:ring-offset-canvas focus-visible:outline-none active:scale-[0.98]',
                                isActive
                                    ? 'border-line-strong bg-surface text-ink shadow-surface'
                                    : 'border-transparent text-ink-muted hover:bg-surface-hover hover:text-ink',
                            )}
                        >
                            <Icon
                                className={cn(
                                    'h-[15px] w-[15px]',
                                    isActive
                                        ? 'text-accent-ink'
                                        : 'text-ink-subtle',
                                )}
                            />
                            {role.label}
                        </button>
                    );
                })}
            </div>

            <div
                role="tabpanel"
                id={`${id}-panel`}
                aria-labelledby={`${id}-tab-${current.key}`}
                className="grid items-start gap-8 lg:grid-cols-12 lg:gap-12"
            >
                <div className="lg:col-span-6">
                    <p className="font-mono text-[10px] tracking-[0.18em] text-ink-subtle uppercase">
                        {t('mk.roles.scope_label', 'Sees')}
                    </p>
                    <p className="mt-2 font-editorial text-[clamp(1.35rem,2.2vw,1.75rem)] leading-[1.2] text-ink">
                        {current.scope}
                    </p>
                    <ul className="mt-6 space-y-0">
                        {current.actions.map((action) => (
                            <li
                                key={action}
                                className="flex gap-3 border-t border-line py-3.5 text-[13.5px] leading-relaxed text-ink-muted"
                            >
                                <span
                                    aria-hidden
                                    className="mt-[7px] h-1 w-1 shrink-0 rounded-full bg-accent"
                                />
                                {action}
                            </li>
                        ))}
                    </ul>
                </div>

                <div className="lg:col-span-6">
                    <div className="relative">
                        <div
                            aria-hidden
                            className="absolute -inset-x-3 -inset-y-3 rounded-2xl bg-surface-muted"
                        />
                        <AppWindow
                            key={current.key}
                            screen={current.screen}
                            density="compact"
                            className="relative"
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}
