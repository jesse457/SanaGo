import {
    ShieldCheckIcon,
    GlobeAltIcon,
    ServerStackIcon,
    EnvelopeIcon,
    Cog8ToothIcon,
    LockClosedIcon,
    BellAlertIcon,
    PhotoIcon,
    ArrowPathIcon,
    CheckIcon,
    FingerPrintIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import React, { useState } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

export default function SystemSettings() {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState('general');

    const tabs = [
        {
            id: 'general',
            name: t('page.settings.tab_general'),
            icon: Cog8ToothIcon,
        },
        {
            id: 'localization',
            name: t('page.settings.tab_localization'),
            icon: GlobeAltIcon,
        },
        {
            id: 'security',
            name: t('page.settings.tab_security'),
            icon: ShieldCheckIcon,
        },
        {
            id: 'integrations',
            name: t('page.settings.tab_integrations'),
            icon: EnvelopeIcon,
        },
        {
            id: 'backups',
            name: t('page.settings.tab_backups'),
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

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: '/' },
                        { label: t('page.settings.system_settings') },
                    ]}
                />

                <div className="flex flex-col gap-8 lg:flex-row">
                    <aside className="w-full shrink-0 space-y-1 lg:w-64">
                        {tabs.map((tab) => (
                            <button
                                key={tab.id}
                                onClick={() => setActiveTab(tab.id)}
                                className={`flex w-full items-center gap-3 rounded-xl border px-4 py-3 text-sm font-bold transition-all ${
                                    activeTab === tab.id
                                        ? 'border-blue-600 bg-blue-600 text-white shadow-lg shadow-blue-100'
                                        : 'border-transparent bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-900 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-100'
                                }`}
                            >
                                <tab.icon className="h-5 w-5" />
                                {tab.name}
                            </button>
                        ))}
                    </aside>

                    <div className="flex min-h-[600px] flex-grow flex-col gap-6">
                        {activeTab === 'general' && (
                            <motion.div
                                initial={{ opacity: 0, x: 10 }}
                                animate={{ opacity: 1, x: 0 }}
                            >
                                <PageSection
                                    title={t('page.settings.general_profile')}
                                    icon={Cog8ToothIcon}
                                >
                                    <div className="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">
                                        <FormInput
                                            name="hospital_name"
                                            label={t(
                                                'page.settings.hospital_legal_name',
                                            )}
                                            value={data.hospital_name}
                                            onChange={(v) =>
                                                setData('hospital_name', v)
                                            }
                                        />
                                        <div className="space-y-1">
                                            <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                                {t(
                                                    'page.settings.hospital_logo',
                                                )}
                                            </label>
                                            <div className="mt-1.5 flex items-center gap-4">
                                                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 dark:bg-neutral-700">
                                                    <PhotoIcon className="h-6 w-6 text-gray-400 dark:text-neutral-500" />
                                                </div>
                                                <button className="text-xs font-bold text-blue-600 hover:underline">
                                                    {t(
                                                        'page.settings.change_logo',
                                                    )}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </PageSection>
                            </motion.div>
                        )}

                        {activeTab === 'security' && (
                            <motion.div
                                initial={{ opacity: 0, x: 10 }}
                                animate={{ opacity: 1, x: 0 }}
                            >
                                <PageSection
                                    title={t(
                                        'page.settings.security_protocols',
                                    )}
                                    icon={ShieldCheckIcon}
                                >
                                    <div className="space-y-4 p-6">
                                        <div className="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 p-4 dark:border-neutral-700 dark:bg-neutral-700">
                                            <div className="flex items-center gap-4">
                                                <FingerPrintIcon className="h-8 w-8 shrink-0 text-blue-600" />
                                                <div>
                                                    <p className="text-sm font-bold text-slate-900 dark:text-neutral-100">
                                                        {t(
                                                            'page.settings.two_factor_title',
                                                        )}
                                                    </p>
                                                    <p className="text-xs text-slate-500 dark:text-neutral-500">
                                                        {t(
                                                            'page.settings.two_factor_desc',
                                                        )}
                                                    </p>
                                                </div>
                                            </div>
                                            <input
                                                type="checkbox"
                                                checked={data.two_factor}
                                                onChange={(e) =>
                                                    setData(
                                                        'two_factor',
                                                        e.target.checked,
                                                    )
                                                }
                                                className="h-5 w-5 rounded border-gray-300 text-blue-600"
                                            />
                                        </div>

                                        <div className="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 p-4 dark:border-neutral-700 dark:bg-neutral-700">
                                            <div className="flex items-center gap-4">
                                                <LockClosedIcon className="h-8 w-8 shrink-0 text-blue-600" />
                                                <div>
                                                    <p className="text-sm font-bold text-slate-900 dark:text-neutral-100">
                                                        {t(
                                                            'page.settings.auto_session_timeout',
                                                        )}
                                                    </p>
                                                    <p className="text-xs text-slate-500 dark:text-neutral-500">
                                                        {t(
                                                            'page.settings.auto_session_timeout_desc',
                                                        )}
                                                    </p>
                                                </div>
                                            </div>
                                            <FormSelect
                                                name="session_timeout"
                                                value={data.session_timeout}
                                                onChange={(v) =>
                                                    setData(
                                                        'session_timeout',
                                                        v,
                                                    )
                                                }
                                                options={[
                                                    {
                                                        value: '15',
                                                        label: t(
                                                            'page.settings.minutes_15',
                                                        ),
                                                    },
                                                    {
                                                        value: '30',
                                                        label: t(
                                                            'page.settings.minutes_30',
                                                        ),
                                                    },
                                                    {
                                                        value: '60',
                                                        label: t(
                                                            'page.settings.minutes_60',
                                                        ),
                                                    },
                                                ]}
                                                className="w-40"
                                            />
                                        </div>
                                    </div>
                                </PageSection>
                            </motion.div>
                        )}

                        {activeTab === 'localization' && (
                            <motion.div
                                initial={{ opacity: 0, x: 10 }}
                                animate={{ opacity: 1, x: 0 }}
                            >
                                <PageSection
                                    title={t('page.settings.regional_billing')}
                                    icon={GlobeAltIcon}
                                >
                                    <div className="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">
                                        <FormSelect
                                            name="currency"
                                            label={t(
                                                'page.settings.primary_currency',
                                            )}
                                            value={data.currency}
                                            onChange={() => {}}
                                            options={[
                                                {
                                                    value: 'USD',
                                                    label: t(
                                                        'page.settings.currency_usd',
                                                    ),
                                                },
                                                {
                                                    value: 'GHS',
                                                    label: t(
                                                        'page.settings.currency_ghs',
                                                    ),
                                                },
                                                {
                                                    value: 'EUR',
                                                    label: t(
                                                        'page.settings.currency_eur',
                                                    ),
                                                },
                                            ]}
                                        />
                                        <FormSelect
                                            name="timezone"
                                            label={t(
                                                'page.settings.system_timezone',
                                            )}
                                            value={data.timezone}
                                            onChange={() => {}}
                                            options={[
                                                {
                                                    value: 'UTC +0',
                                                    label: t(
                                                        'page.settings.timezone_gmt',
                                                    ),
                                                },
                                                {
                                                    value: 'UTC +1',
                                                    label: t(
                                                        'page.settings.timezone_cet',
                                                    ),
                                                },
                                            ]}
                                        />
                                    </div>
                                </PageSection>
                            </motion.div>
                        )}

                        {activeTab === 'backups' && (
                            <motion.div
                                initial={{ opacity: 0, x: 10 }}
                                animate={{ opacity: 1, x: 0 }}
                            >
                                <PageSection
                                    title={t(
                                        'page.settings.infrastructure_maintenance',
                                    )}
                                    icon={ServerStackIcon}
                                >
                                    <div className="space-y-6 p-6">
                                        <div className="flex gap-3 rounded-xl border border-amber-100 bg-amber-50 p-4 dark:border-amber-900/30 dark:bg-amber-900/10">
                                            <BellAlertIcon className="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" />
                                            <p className="text-xs leading-relaxed font-medium text-amber-800 dark:text-amber-200">
                                                {t(
                                                    'page.settings.backup_notification',
                                                )}
                                            </p>
                                        </div>

                                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                            <FormSelect
                                                name="backup_schedule"
                                                label={t(
                                                    'page.settings.backup_frequency',
                                                )}
                                                value={data.backup_schedule}
                                                onChange={(v) =>
                                                    setData(
                                                        'backup_schedule',
                                                        v,
                                                    )
                                                }
                                                options={[
                                                    {
                                                        value: 'hourly',
                                                        label: t(
                                                            'page.settings.every_hour',
                                                        ),
                                                    },
                                                    {
                                                        value: 'daily',
                                                        label: t(
                                                            'page.settings.daily_at_midnight',
                                                        ),
                                                    },
                                                    {
                                                        value: 'weekly',
                                                        label: t(
                                                            'page.settings.every_sunday',
                                                        ),
                                                    },
                                                ]}
                                            />
                                            <div className="flex items-end">
                                                <button className="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white uppercase transition-all hover:bg-slate-800 dark:bg-neutral-700 dark:hover:bg-neutral-600">
                                                    <ArrowPathIcon className="h-4 w-4" />{' '}
                                                    {t(
                                                        'page.settings.trigger_manual_backup',
                                                    )}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </PageSection>
                            </motion.div>
                        )}

                        <div className="flex justify-end gap-3 rounded-xl border border-gray-200 bg-gray-50 p-6 dark:border-neutral-700 dark:bg-neutral-700">
                            <button className="px-4 text-xs font-bold tracking-widest text-gray-500 uppercase hover:text-gray-700 dark:text-neutral-400">
                                {t('common.restore_defaults')}
                            </button>
                            <button
                                disabled={processing}
                                className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-8 py-2.5 text-xs font-bold text-white uppercase shadow-md transition-all hover:bg-blue-700 disabled:opacity-50"
                            >
                                {processing ? (
                                    <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                ) : (
                                    <CheckIcon className="h-4 w-4" />
                                )}
                                {t('common.save_changes')}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
