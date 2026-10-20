import {
    BanknotesIcon,
    CheckCircleIcon,
    ClockIcon,
    Cog6ToothIcon,
    GlobeAltIcon,
    InformationCircleIcon,
    LanguageIcon,
    LifebuoyIcon,
    MegaphoneIcon,
    SignalIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Alert,
    Badge,
    Button,
    Checkbox,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SectionCard,
    Select,
    Textarea,
} from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import {
    CURRENCY_OPTIONS,
    formatCurrency,
} from '@/pages/Platform/components/format';
import type { PlatformSettings } from '@/pages/Platform/components/types';

interface PlatformSettingsProps {
    settings: PlatformSettings;
}

interface SettingsForm {
    platform_name: string;
    platform_tagline: string;
    currency: string;
    currency_symbol: string;
    default_locale: string;
    supported_locales: string[];
    trial_days: string;
    grace_days: string;
    support_email: string;
    support_phone: string;
    realtime_enabled: boolean;
    email_notifications_enabled: boolean;
    maintenance_mode: boolean;
    maintenance_message: string;
}

/**
 * Platform/Settings — the platform's own identity and defaults.
 *
 * The currency here is the single source of truth for every billing figure in
 * the console and the tenant app, which is why it is called out in the helper
 * text rather than buried in a field.
 */
export default function PlatformSettingsPage({
    settings,
}: PlatformSettingsProps) {
    const { data, setData, put, processing, errors, isDirty, reset } =
        useForm<SettingsForm>({
            platform_name: settings?.platform_name ?? '',
            platform_tagline: settings?.platform_tagline ?? '',
            currency: settings?.currency ?? 'XAF',
            currency_symbol: settings?.currency_symbol ?? 'FCFA',
            default_locale: settings?.default_locale ?? 'en',
            supported_locales: settings?.supported_locales ?? ['en', 'fr'],
            trial_days: String(settings?.trial_days ?? 14),
            grace_days: String(settings?.grace_days ?? 3),
            support_email: settings?.support_email ?? '',
            support_phone: settings?.support_phone ?? '',
            realtime_enabled: Boolean(settings?.realtime_enabled),
            email_notifications_enabled: Boolean(
                settings?.email_notifications_enabled,
            ),
            maintenance_mode: Boolean(settings?.maintenance_mode),
            maintenance_message: settings?.maintenance_message ?? '',
        });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        put('/platform/settings', { preserveScroll: true });
    };

    const toggleLocale = (locale: string, checked: boolean) => {
        const current = new Set(data.supported_locales);

        if (checked) {
            current.add(locale);
        } else {
            current.delete(locale);
        }

        setData('supported_locales', Array.from(current).sort());
    };

    return (
        <SuperAdminLayout
            headerTitle="Platform settings"
            headerSubtitle="Identity, currency and defaults"
        >
            <Head title="Platform settings" />
            <div className="mx-auto max-w-4xl space-y-5 pb-12">
                <PageHeader
                    title="Platform settings"
                    description="The platform's identity, its billing currency and the defaults applied to every new hospital."
                    icon={Cog6ToothIcon}
                    breadcrumbs={[{ label: 'Platform' }, { label: 'Settings' }]}
                />

                <form onSubmit={submit} className="space-y-5">
                    {/* ── Identity ── */}
                    <SectionCard
                        title="Identity"
                        description="Shown in the console, on invoices and in transactional email."
                        icon={GlobeAltIcon}
                    >
                        <FieldSet columns={2}>
                            <Field
                                label="Platform name"
                                htmlFor="platform_name"
                                required
                                error={errors.platform_name}
                                className="sm:col-span-2"
                            >
                                <Input
                                    id="platform_name"
                                    name="platform_name"
                                    value={data.platform_name}
                                    placeholder="SanaGo"
                                    onChange={(event) =>
                                        setData(
                                            'platform_name',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.platform_name)}
                                    maxLength={120}
                                />
                            </Field>

                            <Field
                                label="Tagline"
                                htmlFor="platform_tagline"
                                error={errors.platform_tagline}
                                hint="One line, used under the name."
                                className="sm:col-span-2"
                            >
                                <Input
                                    id="platform_tagline"
                                    name="platform_tagline"
                                    value={data.platform_tagline}
                                    placeholder="Hospital management for Cameroon"
                                    onChange={(event) =>
                                        setData(
                                            'platform_tagline',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.platform_tagline)}
                                    maxLength={160}
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    {/* ── Currency ── */}
                    <SectionCard
                        title="Currency"
                        description="Drives every billing display across the platform."
                        icon={BanknotesIcon}
                    >
                        <div className="mb-4">
                            <Alert tone="info" title="This is the billing unit">
                                Prices on plans, subscriptions, invoices and
                                every usage figure are rendered in this
                                currency. Changing it does not convert existing
                                amounts — it only changes how they are labelled,
                                so convert the plan catalogue first.
                            </Alert>
                        </div>

                        <FieldSet columns={3}>
                            <Field
                                label="Currency code"
                                htmlFor="currency"
                                required
                                error={errors.currency}
                                hint="ISO 4217, three letters."
                            >
                                <Select
                                    id="currency"
                                    name="currency"
                                    value={data.currency}
                                    onChange={(event) =>
                                        setData('currency', event.target.value)
                                    }
                                    invalid={Boolean(errors.currency)}
                                >
                                    {CURRENCY_OPTIONS.map((option) => (
                                        <option
                                            key={option.value}
                                            value={option.value}
                                        >
                                            {option.label}
                                        </option>
                                    ))}
                                </Select>
                            </Field>

                            <Field
                                label="Currency symbol"
                                htmlFor="currency_symbol"
                                required
                                error={errors.currency_symbol}
                                hint="Trailing label, e.g. FCFA."
                            >
                                <Input
                                    id="currency_symbol"
                                    name="currency_symbol"
                                    value={data.currency_symbol}
                                    placeholder="FCFA"
                                    onChange={(event) =>
                                        setData(
                                            'currency_symbol',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.currency_symbol)}
                                    maxLength={8}
                                />
                            </Field>

                            <div className="flex items-end">
                                <div className="w-full rounded-lg border border-line bg-surface-muted px-3.5 py-2.5">
                                    <p className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                        Preview
                                    </p>
                                    <p className="mt-0.5 text-[14px] font-semibold text-ink tabular-nums">
                                        {formatCurrency(
                                            1500000,
                                            data.currency,
                                            data.currency_symbol || undefined,
                                        )}
                                    </p>
                                </div>
                            </div>
                        </FieldSet>
                    </SectionCard>

                    {/* ── Locales ── */}
                    <SectionCard
                        title="Languages"
                        description="Which locales the interface offers, and its default."
                        icon={LanguageIcon}
                    >
                        <FieldSet columns={2}>
                            <Field
                                label="Default locale"
                                htmlFor="default_locale"
                                required
                                error={errors.default_locale}
                                hint="Used when a user has not chosen one."
                            >
                                <Select
                                    id="default_locale"
                                    name="default_locale"
                                    value={data.default_locale}
                                    onChange={(event) =>
                                        setData(
                                            'default_locale',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.default_locale)}
                                >
                                    <option value="en">English</option>
                                    <option value="fr">Français</option>
                                </Select>
                            </Field>

                            <Field
                                label="Supported locales"
                                error={
                                    errors['supported_locales'] ??
                                    errors['supported_locales.0']
                                }
                                hint="At least one must be enabled."
                            >
                                <div className="flex flex-col gap-2 pt-1">
                                    {[
                                        { value: 'en', label: 'English' },
                                        { value: 'fr', label: 'Français' },
                                    ].map((locale) => (
                                        <Checkbox
                                            key={locale.value}
                                            id={`locale-${locale.value}`}
                                            checked={data.supported_locales.includes(
                                                locale.value,
                                            )}
                                            onChange={(event) =>
                                                toggleLocale(
                                                    locale.value,
                                                    event.target.checked,
                                                )
                                            }
                                            label={locale.label}
                                        />
                                    ))}
                                </div>
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    {/* ── Defaults ── */}
                    <SectionCard
                        title="Subscription defaults"
                        description="Applied to new hospitals unless a plan overrides them."
                        icon={ClockIcon}
                    >
                        <FieldSet columns={2}>
                            <Field
                                label="Trial days"
                                htmlFor="trial_days"
                                required
                                error={errors.trial_days}
                                hint="Default trial length for a new hospital."
                            >
                                <Input
                                    id="trial_days"
                                    name="trial_days"
                                    type="number"
                                    min="0"
                                    max="365"
                                    value={data.trial_days}
                                    onChange={(event) =>
                                        setData(
                                            'trial_days',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.trial_days)}
                                    className="tabular-nums"
                                />
                            </Field>

                            <Field
                                label="Grace days"
                                htmlFor="grace_days"
                                required
                                error={errors.grace_days}
                                hint="Days of access after a period ends before lockout."
                            >
                                <Input
                                    id="grace_days"
                                    name="grace_days"
                                    type="number"
                                    min="0"
                                    max="90"
                                    value={data.grace_days}
                                    onChange={(event) =>
                                        setData(
                                            'grace_days',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.grace_days)}
                                    className="tabular-nums"
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    {/* ── Support ── */}
                    <SectionCard
                        title="Support contacts"
                        description="Shown to hospitals that need help."
                        icon={LifebuoyIcon}
                    >
                        <FieldSet columns={2}>
                            <Field
                                label="Support email"
                                htmlFor="support_email"
                                required
                                error={errors.support_email}
                            >
                                <Input
                                    id="support_email"
                                    name="support_email"
                                    type="email"
                                    value={data.support_email}
                                    placeholder="support@sanago.cm"
                                    onChange={(event) =>
                                        setData(
                                            'support_email',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.support_email)}
                                    spellCheck={false}
                                />
                            </Field>

                            <Field
                                label="Support phone"
                                htmlFor="support_phone"
                                error={errors.support_phone}
                            >
                                <Input
                                    id="support_phone"
                                    name="support_phone"
                                    value={data.support_phone}
                                    placeholder="+237 6 00 00 00 00"
                                    onChange={(event) =>
                                        setData(
                                            'support_phone',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.support_phone)}
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    {/* ── Transports ── */}
                    <SectionCard
                        title="Realtime & notifications"
                        description="Transport switches for the whole platform."
                        icon={SignalIcon}
                    >
                        <div className="space-y-3">
                            <Checkbox
                                name="realtime_enabled"
                                checked={data.realtime_enabled}
                                onChange={(event) =>
                                    setData(
                                        'realtime_enabled',
                                        event.target.checked,
                                    )
                                }
                                label="Realtime updates"
                                description="Broadcasts live events to connected clients (WebSocket / Pusher)."
                            />
                            <Checkbox
                                name="email_notifications_enabled"
                                checked={data.email_notifications_enabled}
                                onChange={(event) =>
                                    setData(
                                        'email_notifications_enabled',
                                        event.target.checked,
                                    )
                                }
                                label="Email notifications"
                                description="Lifecycle and billing emails are delivered to hospitals."
                            />
                        </div>
                    </SectionCard>

                    {/* ── Maintenance ── */}
                    <SectionCard
                        title="Maintenance mode"
                        description="Announces planned downtime instead of failing silently."
                        icon={MegaphoneIcon}
                    >
                        <div className="space-y-4">
                            <Checkbox
                                name="maintenance_mode"
                                checked={data.maintenance_mode}
                                onChange={(event) =>
                                    setData(
                                        'maintenance_mode',
                                        event.target.checked,
                                    )
                                }
                                label="Maintenance mode is on"
                                description="Hospitals see the message below instead of the app."
                            />

                            <Field
                                label="Maintenance message"
                                htmlFor="maintenance_message"
                                error={errors.maintenance_message}
                                hint="Leave empty for the default copy."
                            >
                                <Textarea
                                    id="maintenance_message"
                                    name="maintenance_message"
                                    rows={3}
                                    maxLength={500}
                                    value={data.maintenance_message}
                                    placeholder="We are upgrading the platform. Back at 14:00."
                                    onChange={(event) =>
                                        setData(
                                            'maintenance_message',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(
                                        errors.maintenance_message,
                                    )}
                                />
                            </Field>

                            {data.maintenance_mode && (
                                <Alert
                                    tone="warning"
                                    title="Hospitals will be locked out"
                                >
                                    Every tenant sees the maintenance message on
                                    save. Platform staff keep access.
                                </Alert>
                            )}
                        </div>
                    </SectionCard>

                    {Object.keys(errors).length > 0 && (
                        <Alert tone="critical" title="Could not save">
                            {Object.values(errors).join(' ')}
                        </Alert>
                    )}

                    <div className="flex flex-wrap items-center justify-between gap-2 border-t border-line pt-4">
                        <p className="flex items-center gap-1.5 text-[12px] text-ink-subtle">
                            <InformationCircleIcon className="h-3.5 w-3.5" />
                            Every change is written to the platform audit log.
                        </p>
                        <div className="flex items-center gap-2">
                            <Button
                                variant="ghost"
                                disabled={!isDirty || processing}
                                onClick={() => reset()}
                            >
                                Discard
                            </Button>
                            <Button
                                type="submit"
                                icon={CheckCircleIcon}
                                loading={processing}
                                disabled={!isDirty}
                            >
                                Save settings
                            </Button>
                        </div>
                    </div>
                </form>

                <div className="flex items-center gap-2 text-[11.5px] text-ink-subtle">
                    <Badge tone="muted" variant="outline">
                        PUT /platform/settings
                    </Badge>
                    <span>
                        Settings apply platform-wide immediately after saving.
                    </span>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
