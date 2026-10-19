import {
    ArrowPathIcon,
    CheckIcon,
    Cog8ToothIcon,
    EnvelopeIcon,
    FingerPrintIcon,
    GlobeAltIcon,
    LockClosedIcon,
    PhotoIcon,
    ServerStackIcon,
    ShieldCheckIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import {
    Alert,
    Button,
    Field,
    Input,
    PageHeader,
    SectionCard,
    Select,
    Switch,
    Tabs,
} from '@/Components/ui';
import type { TabItem } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

type SettingsTab =
    | 'general'
    | 'localization'
    | 'security'
    | 'integrations'
    | 'backups';

export default function SystemSettings() {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState<SettingsTab>('general');

    const tabs: TabItem[] = [
        {
            value: 'general',
            label: t('page.settings.tab_general'),
            icon: Cog8ToothIcon,
        },
        {
            value: 'localization',
            label: t('page.settings.tab_localization'),
            icon: GlobeAltIcon,
        },
        {
            value: 'security',
            label: t('page.settings.tab_security'),
            icon: ShieldCheckIcon,
        },
        {
            value: 'integrations',
            label: t('page.settings.tab_integrations'),
            icon: EnvelopeIcon,
        },
        {
            value: 'backups',
            label: t('page.settings.tab_backups'),
            icon: ServerStackIcon,
        },
    ];

    const { data, setData, processing } = useForm({
        hospital_name: 'St. Mary’s General Hospital',
        timezone: 'UTC +0',
        currency: 'USD',
        smtp_host: 'smtp.hospital.com',
        backup_schedule: 'daily',
        two_factor: true,
        session_timeout: '30',
    });

    return (
        <AppLayout
            headerTitle={t('page.settings.title')}
            headerSubtitle={t('page.settings.subtitle')}
        >
            <Head title={t('page.settings.head_title')} />

            <div className="mx-auto max-w-7xl space-y-5 pb-12">
                <PageHeader
                    title={t('page.settings.system_settings')}
                    description={t('page.settings.subtitle')}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/' },
                        { label: t('page.settings.system_settings') },
                    ]}
                />

                <Tabs
                    items={tabs}
                    value={activeTab}
                    onChange={(value) => setActiveTab(value as SettingsTab)}
                />

                {activeTab === 'general' && (
                    <SectionCard
                        title={t('page.settings.general_profile')}
                        icon={Cog8ToothIcon}
                    >
                        <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <Field
                                label={t('page.settings.hospital_legal_name')}
                                htmlFor="hospital_name"
                            >
                                <Input
                                    id="hospital_name"
                                    name="hospital_name"
                                    value={data.hospital_name}
                                    onChange={(event) =>
                                        setData(
                                            'hospital_name',
                                            event.target.value,
                                        )
                                    }
                                />
                            </Field>

                            <Field
                                label={t('page.settings.hospital_logo')}
                                htmlFor="hospital_logo"
                                labelAction={
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        className="h-6 px-1.5 text-[11px]"
                                    >
                                        {t('page.settings.change_logo')}
                                    </Button>
                                }
                            >
                                <div className="flex h-10 items-center gap-3 rounded-md border border-dashed border-line-strong bg-surface-muted px-3">
                                    <PhotoIcon className="h-4 w-4 text-ink-subtle" />
                                    <span className="text-[12.5px] text-ink-subtle">
                                        {t('page.settings.hospital_logo')}
                                    </span>
                                </div>
                            </Field>
                        </div>
                    </SectionCard>
                )}

                {activeTab === 'security' && (
                    <SectionCard
                        title={t('page.settings.security_protocols')}
                        icon={ShieldCheckIcon}
                        contentClassName="space-y-3"
                    >
                        <div className="flex flex-col gap-3 rounded-lg border border-line bg-surface-muted px-4 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                            <div className="flex items-start gap-3">
                                <FingerPrintIcon className="mt-0.5 h-5 w-5 shrink-0 text-accent" />
                                <div>
                                    <p className="text-[13px] font-semibold text-ink">
                                        {t('page.settings.two_factor_title')}
                                    </p>
                                    <p className="mt-0.5 text-[12px] leading-relaxed text-ink-muted">
                                        {t('page.settings.two_factor_desc')}
                                    </p>
                                </div>
                            </div>
                            <Switch
                                checked={data.two_factor}
                                onChange={(event) =>
                                    setData('two_factor', event.target.checked)
                                }
                            />
                        </div>

                        <div className="flex flex-col gap-3 rounded-lg border border-line bg-surface-muted px-4 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                            <div className="flex items-start gap-3">
                                <LockClosedIcon className="mt-0.5 h-5 w-5 shrink-0 text-accent" />
                                <div>
                                    <p className="text-[13px] font-semibold text-ink">
                                        {t(
                                            'page.settings.auto_session_timeout',
                                        )}
                                    </p>
                                    <p className="mt-0.5 text-[12px] leading-relaxed text-ink-muted">
                                        {t(
                                            'page.settings.auto_session_timeout_desc',
                                        )}
                                    </p>
                                </div>
                            </div>
                            <Select
                                name="session_timeout"
                                className="sm:w-40"
                                value={data.session_timeout}
                                onChange={(event) =>
                                    setData(
                                        'session_timeout',
                                        event.target.value,
                                    )
                                }
                            >
                                <option value="15">
                                    {t('page.settings.minutes_15')}
                                </option>
                                <option value="30">
                                    {t('page.settings.minutes_30')}
                                </option>
                                <option value="60">
                                    {t('page.settings.minutes_60')}
                                </option>
                            </Select>
                        </div>
                    </SectionCard>
                )}

                {activeTab === 'localization' && (
                    <SectionCard
                        title={t('page.settings.regional_billing')}
                        icon={GlobeAltIcon}
                    >
                        <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <Field
                                label={t('page.settings.primary_currency')}
                                htmlFor="currency"
                            >
                                <Select
                                    id="currency"
                                    name="currency"
                                    value={data.currency}
                                    onChange={() => {}}
                                >
                                    <option value="USD">
                                        {t('page.settings.currency_usd')}
                                    </option>
                                    <option value="GHS">
                                        {t('page.settings.currency_ghs')}
                                    </option>
                                    <option value="EUR">
                                        {t('page.settings.currency_eur')}
                                    </option>
                                </Select>
                            </Field>

                            <Field
                                label={t('page.settings.system_timezone')}
                                htmlFor="timezone"
                            >
                                <Select
                                    id="timezone"
                                    name="timezone"
                                    value={data.timezone}
                                    onChange={() => {}}
                                >
                                    <option value="UTC +0">
                                        {t('page.settings.timezone_gmt')}
                                    </option>
                                    <option value="UTC +1">
                                        {t('page.settings.timezone_cet')}
                                    </option>
                                </Select>
                            </Field>
                        </div>
                    </SectionCard>
                )}

                {activeTab === 'integrations' && (
                    <SectionCard
                        title={t('page.settings.tab_integrations')}
                        icon={EnvelopeIcon}
                    >
                        <Field
                            label={t('page.settings.smtp_host')}
                            htmlFor="smtp_host"
                        >
                            <Input
                                id="smtp_host"
                                name="smtp_host"
                                value={data.smtp_host}
                                onChange={(event) =>
                                    setData('smtp_host', event.target.value)
                                }
                            />
                        </Field>
                    </SectionCard>
                )}

                {activeTab === 'backups' && (
                    <SectionCard
                        title={t('page.settings.infrastructure_maintenance')}
                        icon={ServerStackIcon}
                    >
                        <div className="space-y-5">
                            <Alert tone="warning">
                                {t('page.settings.backup_notification')}
                            </Alert>

                            <div className="grid grid-cols-1 items-end gap-5 md:grid-cols-2">
                                <Field
                                    label={t('page.settings.backup_frequency')}
                                    htmlFor="backup_schedule"
                                >
                                    <Select
                                        id="backup_schedule"
                                        name="backup_schedule"
                                        value={data.backup_schedule}
                                        onChange={(event) =>
                                            setData(
                                                'backup_schedule',
                                                event.target.value,
                                            )
                                        }
                                    >
                                        <option value="hourly">
                                            {t('page.settings.every_hour')}
                                        </option>
                                        <option value="daily">
                                            {t(
                                                'page.settings.daily_at_midnight',
                                            )}
                                        </option>
                                        <option value="weekly">
                                            {t('page.settings.every_sunday')}
                                        </option>
                                    </Select>
                                </Field>
                                <Button
                                    variant="secondary"
                                    icon={ArrowPathIcon}
                                    fullWidth
                                >
                                    {t('page.settings.trigger_manual_backup')}
                                </Button>
                            </div>
                        </div>
                    </SectionCard>
                )}

                <div className="sticky bottom-4 z-10 flex flex-wrap items-center justify-end gap-2 rounded-xl border border-line bg-surface px-4 py-3 shadow-raised">
                    <Button variant="ghost">
                        {t('common.restore_defaults')}
                    </Button>
                    <Button
                        icon={CheckIcon}
                        loading={processing}
                        className="min-w-36"
                    >
                        {t('common.save_changes')}
                    </Button>
                </div>
            </div>
        </AppLayout>
    );
}
