import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Lock, Eye, EyeOff, ShieldCheck } from 'lucide-react';
import { useTranslation } from '@/hooks/useTranslation';

interface ResetProps {
    token: string;
    email: string;
}

const ResetPassword: React.FC<ResetProps> = ({ token, email }) => {
    const { t } = useTranslation();
    const [showPass, setShowPass] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/reset-password');
    };

    return (
        <>
            <Head title="Reset Password | Yerli HMS" />

            <div className="min-h-screen bg-white dark:bg-neutral-800 flex flex-col lg:flex-row">

                {/* Left Side: Visual */}
                <div className="hidden lg:flex lg:w-1/2 relative bg-slate-900 items-center justify-center p-12 overflow-hidden">
                    <div className="absolute inset-0 opacity-20">
                        <svg className="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <path d="M0 100 C 20 0 50 0 100 100 Z" fill="url(#grad)" />
                            <defs>
                                <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stopColor="#3B82F6" />
                                    <stop offset="100%" stopColor="#60A5FA" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div className="relative z-10 text-center">
                        <div className="w-20 h-20 bg-blue-500 rounded-3xl flex items-center justify-center text-white mx-auto mb-8 shadow-2xl shadow-blue-500/50">
                            <ShieldCheck size={40} />
                        </div>
                        <h1 className="text-4xl font-extrabold text-white mb-4">Set a Strong Password</h1>
                        <p className="text-slate-400 dark:text-neutral-500 max-w-xs mx-auto">Help us keep your health records private by choosing a unique and complex password.</p>
                    </div>
                </div>

                {/* Right Side: Form */}
                <div className="flex-1 flex flex-col justify-center px-8 py-12 lg:px-24 bg-white">
                    <div className="mx-auto w-full max-w-sm">
                        <div className="mb-10">
                            <h2 className="text-3xl font-bold text-slate-900 dark:text-neutral-100 tracking-tight">{t('auth.confirm_password.title')}</h2>
                            <p className="text-slate-500 dark:text-neutral-500 mt-2 font-medium">Create a new password for your account.</p>
                        </div>

                        <form onSubmit={submit} className="space-y-6">
                            {/* Hidden Email or Read-only Email for UX */}
                            <div>
                                <label className="block text-xs font-bold text-slate-400 dark:text-neutral-500 uppercase tracking-widest mb-2">Account Email</label>
                                <input
                                    type="email"
                                    value={data.email}
                                    readOnly
                                    className="block w-full px-4 py-4 bg-slate-100 border border-transparent rounded-2xl text-slate-500 dark:text-neutral-500 text-sm outline-none cursor-not-allowed"
                                />
                            </div>

                            {/* New Password */}
                            <div>
                                <label className="block text-xs font-bold text-slate-400 dark:text-neutral-500 uppercase tracking-widest mb-2">New Password</label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-neutral-500">
                                        <Lock size={18} />
                                    </div>
                                    <input
                                        type={showPass ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={e => setData('password', e.target.value)}
                                        className="block w-full pl-11 pr-12 py-4 bg-slate-50 dark:bg-neutral-700 border border-slate-100 dark:border-neutral-700 rounded-2xl text-slate-900 dark:text-neutral-100 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none"
                                        placeholder="••••••••"
                                        required
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPass(!showPass)}
                                        className="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 dark:text-neutral-500"
                                    >
                                        {showPass ? <EyeOff size={18} /> : <Eye size={18} />}
                                    </button>
                                </div>
                                {errors.password && <p className="mt-2 text-xs text-red-500 font-medium">{errors.password}</p>}
                            </div>

                            {/* Confirm Password */}
                            <div>
                                <label className="block text-xs font-bold text-slate-400 dark:text-neutral-500 uppercase tracking-widest mb-2">Confirm New Password</label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-neutral-500">
                                        <Lock size={18} />
                                    </div>
                                    <input
                                        type="password"
                                        value={data.password_confirmation}
                                        onChange={e => setData('password_confirmation', e.target.value)}
                                        className="block w-full pl-11 pr-4 py-4 bg-slate-50 dark:bg-neutral-700 border border-slate-100 dark:border-neutral-700 rounded-2xl text-slate-900 dark:text-neutral-100 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none"
                                        placeholder="••••••••"
                                        required
                                    />
                                </div>
                                {errors.password_confirmation && <p className="mt-2 text-xs text-red-500 font-medium">{errors.password_confirmation}</p>}
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all disabled:opacity-70"
                            >
                                {processing ? 'Updating...' : 'Update Password'}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ResetPassword;
