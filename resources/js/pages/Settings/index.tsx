import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    HomeIcon,
    ChevronRightIcon,
    ShieldCheckIcon,
    GlobeAltIcon,
    ServerStackIcon,
    EnvelopeIcon,
    UserGroupIcon,
    Cog8ToothIcon,
    LockClosedIcon,
    BellAlertIcon,
    PhotoIcon,
    KeyIcon,
    ArrowPathIcon,
    CheckIcon,
    FingerPrintIcon
} from '@heroicons/react/24/outline';
import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

export default function SystemSettings() {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState('general');

    const tabs = [
        { id: 'general', name: t('page.settings.tab_general'), icon: Cog8ToothIcon },
        { id: 'localization', name: t('page.settings.tab_localization'), icon: GlobeAltIcon },
        { id: 'security', name: t('page.settings.tab_security'), icon: ShieldCheckIcon },
        { id: 'integrations', name: t('page.settings.tab_integrations'), icon: EnvelopeIcon },
        { id: 'backups', name: t('page.settings.tab_backups'), icon: ServerStackIcon },
    ];

    const { data, setData, post, processing } = useForm({
        hospital_name: 'St. Mary’s General Hospital',
        timezone: 'UTC +0',
        currency: 'USD',
        smtp_host: 'smtp.hospital.com',
        backup_schedule: 'daily',
        two_factor: true,
        session_timeout: '30'
    });

    return (
        <AppLayout
            headerTitle={t('page.settings.title')}
            headerSubtitle={t('page.settings.subtitle')}
        >
            <Head title={t('page.settings.head_title')} />

            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-12">
                
                {/* --- Breadcrumbs --- */}
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="hover:text-blue-600 cursor-pointer transition-colors flex items-center">
                            <HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}
                        </li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-medium">{t('page.settings.system_settings')}</li>
                    </ol>
                </nav>

                <div className="flex flex-col lg:flex-row gap-8">
                    
                    {/* --- Sidebar Tabs --- */}
                    <aside className="w-full lg:w-64 shrink-0 space-y-1">
                        {tabs.map((tab) => (
                            <button
                                key={tab.id}
                                onClick={() => setActiveTab(tab.id)}
                                className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all border ${
                                    activeTab === tab.id 
                                    ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-100' 
                                    : 'bg-white text-gray-500 dark:text-neutral-500 border-transparent hover:bg-gray-50 dark:hover:bg-neutral-700 hover:text-gray-900 dark:text-neutral-100 dark:hover:text-neutral-100'
                                }`}
                            >
                                <tab.icon className="w-5 h-5" />
                                {tab.name}
                            </button>
                        ))}
                    </aside>

                    {/* --- Main Settings Panel --- */}
                    <main className="flex-grow bg-white dark:bg-neutral-800 rounded-2xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden min-h-[600px]">
                        
                        <div className="p-8 space-y-8">
                            
                            {/* --- General & Branding --- */}
                            {activeTab === 'general' && (
                                <motion.div initial={{ opacity: 0, x: 10 }} animate={{ opacity: 1, x: 0 }} className="space-y-6">
                                    <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-widest border-b border-gray-100 dark:border-neutral-700 pb-4">{t('page.settings.general_profile')}</h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div className="space-y-1">
                                            <label className="text-[10px] font-black text-gray-400 dark:text-neutral-500 uppercase">{t('page.settings.hospital_legal_name')}</label>
                                            <input type="text" value={data.hospital_name} onChange={e => setData('hospital_name', e.target.value)} className="w-full border-gray-200 dark:border-neutral-700 rounded-xl text-sm font-bold focus:ring-blue-500" />
                                        </div>
                                        <div className="space-y-1">
                                            <label className="text-[10px] font-black text-gray-400 dark:text-neutral-500 uppercase">{t('page.settings.hospital_logo')}</label>
                                            <div className="flex items-center gap-4">
                                                <div className="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center"><PhotoIcon className="w-6 h-6 text-gray-400 dark:text-neutral-500" /></div>
                                                <button className="text-xs font-bold text-blue-600 hover:underline">{t('page.settings.change_logo')}</button>
                                            </div>
                                        </div>
                                    </div>
                                </motion.div>
                            )}

                            {/* --- Security & Access --- */}
                            {activeTab === 'security' && (
                                <motion.div initial={{ opacity: 0, x: 10 }} animate={{ opacity: 1, x: 0 }} className="space-y-8">
                                    <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-widest border-b border-gray-100 dark:border-neutral-700 pb-4">{t('page.settings.security_protocols')}</h3>

                                    <div className="space-y-4">
                                        <div className="flex items-center justify-between p-4 bg-slate-50 dark:bg-neutral-700 rounded-xl border border-slate-100 dark:border-neutral-700">
                                            <div className="flex items-center gap-4">
                                                <FingerPrintIcon className="w-8 h-8 text-blue-600" />
                                                <div>
                                                    <p className="text-sm font-bold text-slate-900 dark:text-neutral-100">{t('page.settings.two_factor_title')}</p>
                                                    <p className="text-xs text-slate-500 dark:text-neutral-500">{t('page.settings.two_factor_desc')}</p>
                                                </div>
                                            </div>
                                            <input type="checkbox" checked={data.two_factor} onChange={e => setData('two_factor', e.target.checked)} className="w-5 h-5 rounded border-gray-300 text-blue-600" />
                                        </div>

                                        <div className="flex items-center justify-between p-4 bg-slate-50 dark:bg-neutral-700 rounded-xl border border-slate-100 dark:border-neutral-700">
                                            <div className="flex items-center gap-4">
                                                <LockClosedIcon className="w-8 h-8 text-blue-600" />
                                                <div>
                                                    <p className="text-sm font-bold text-slate-900 dark:text-neutral-100">{t('page.settings.auto_session_timeout')}</p>
                                                    <p className="text-xs text-slate-500 dark:text-neutral-500">{t('page.settings.auto_session_timeout_desc')}</p>
                                                </div>
                                            </div>
                                            <select value={data.session_timeout} onChange={e => setData('session_timeout', e.target.value)} className="text-xs font-bold border-gray-200 dark:border-neutral-700 rounded-lg">
                                                <option value="15">{t('page.settings.minutes_15')}</option>
                                                <option value="30">{t('page.settings.minutes_30')}</option>
                                                <option value="60">{t('page.settings.minutes_60')}</option>
                                            </select>
                                        </div>
                                    </div>
                                </motion.div>
                            )}

                            {/* --- Regional Settings --- */}
                            {activeTab === 'localization' && (
                                <motion.div initial={{ opacity: 0, x: 10 }} animate={{ opacity: 1, x: 0 }} className="space-y-6">
                                    <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-widest border-b border-gray-100 dark:border-neutral-700 pb-4">{t('page.settings.regional_billing')}</h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div className="space-y-1">
                                            <label className="text-[10px] font-black text-gray-400 dark:text-neutral-500 uppercase">{t('page.settings.primary_currency')}</label>
                                            <select value={data.currency} className="w-full border-gray-200 dark:border-neutral-700 rounded-xl text-sm font-bold">
                                                <option value="USD">{t('page.settings.currency_usd')}</option>
                                                <option value="GHS">{t('page.settings.currency_ghs')}</option>
                                                <option value="EUR">{t('page.settings.currency_eur')}</option>
                                            </select>
                                        </div>
                                        <div className="space-y-1">
                                            <label className="text-[10px] font-black text-gray-400 dark:text-neutral-500 uppercase">{t('page.settings.system_timezone')}</label>
                                            <select value={data.timezone} className="w-full border-gray-200 dark:border-neutral-700 rounded-xl text-sm font-bold">
                                                <option value="UTC +0">{t('page.settings.timezone_gmt')}</option>
                                                <option value="UTC +1">{t('page.settings.timezone_cet')}</option>
                                            </select>
                                        </div>
                                    </div>
                                </motion.div>
                            )}

                            {/* --- Maintenance & Backups --- */}
                            {activeTab === 'backups' && (
                                <motion.div initial={{ opacity: 0, x: 10 }} animate={{ opacity: 1, x: 0 }} className="space-y-6">
                                    <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-widest border-b border-gray-100 dark:border-neutral-700 pb-4">{t('page.settings.infrastructure_maintenance')}</h3>
                                    <div className="bg-amber-50 border border-amber-100 p-4 rounded-xl flex gap-3">
                                        <BellAlertIcon className="w-5 h-5 text-amber-600 shrink-0" />
                                        <p className="text-xs text-amber-800 leading-relaxed font-medium">
                                            {t('page.settings.backup_notification')}
                                        </p>
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div className="space-y-1">
                                            <label className="text-[10px] font-black text-gray-400 dark:text-neutral-500 uppercase">{t('page.settings.backup_frequency')}</label>
                                            <select value={data.backup_schedule} onChange={e => setData('backup_schedule', e.target.value)} className="w-full border-gray-200 dark:border-neutral-700 rounded-xl text-sm font-bold">
                                                <option value="hourly">{t('page.settings.every_hour')}</option>
                                                <option value="daily">{t('page.settings.daily_at_midnight')}</option>
                                                <option value="weekly">{t('page.settings.every_sunday')}</option>
                                            </select>
                                        </div>
                                        <div className="flex items-end">
                                            <button className="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-neutral-800 text-white text-xs font-bold rounded-xl hover:bg-slate-800 transition-all uppercase">
                                                <ArrowPathIcon className="w-4 h-4" /> {t('page.settings.trigger_manual_backup')}
                                            </button>
                                        </div>
                                    </div>
                                </motion.div>
                            )}

                        </div>

                        {/* --- Panel Footer --- */}
                        <div className="px-8 py-4 bg-gray-50 dark:bg-neutral-700 border-t border-gray-100 dark:border-neutral-700 flex justify-end gap-3">
                            <button className="text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase tracking-widest px-4 hover:text-gray-700 dark:text-neutral-300">{t('common.restore_defaults')}</button>
                            <button
                                disabled={processing}
                                className="inline-flex items-center gap-2 px-8 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 shadow-md transition-all uppercase"
                            >
                                {processing ? <ArrowPathIcon className="w-4 h-4 animate-spin" /> : <CheckIcon className="w-4 h-4" />}
                                {t('common.save_changes')}
                            </button>
                        </div>
                    </main>
                </div>
            </div>
        </AppLayout>
    );
}