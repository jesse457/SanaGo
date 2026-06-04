import { Head, useForm } from '@inertiajs/react';
import { Lock, Eye, EyeOff, ShieldCheck } from 'lucide-react';
import React, { useState } from 'react';
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

            <div className="flex min-h-screen flex-col bg-white lg:flex-row dark:bg-neutral-800">
                {/* Left Side: Visual */}
                <div className="relative hidden items-center justify-center overflow-hidden bg-slate-900 p-12 lg:flex lg:w-1/2">
                    <div className="absolute inset-0 opacity-20">
                        <svg
                            className="h-full w-full"
                            viewBox="0 0 100 100"
                            preserveAspectRatio="none"
                        >
                            <path
                                d="M0 100 C 20 0 50 0 100 100 Z"
                                fill="url(#grad)"
                            />
                            <defs>
                                <linearGradient
                                    id="grad"
                                    x1="0%"
                                    y1="0%"
                                    x2="100%"
                                    y2="0%"
                                >
                                    <stop offset="0%" stopColor="#3B82F6" />
                                    <stop offset="100%" stopColor="#60A5FA" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div className="relative z-10 text-center">
                        <div className="mx-auto mb-8 flex h-20 w-20 items-center justify-center rounded-3xl bg-blue-500 text-white shadow-2xl shadow-blue-500/50">
                            <ShieldCheck size={40} />
                        </div>
                        <h1 className="mb-4 text-4xl font-extrabold text-white">
                            Set a Strong Password
                        </h1>
                        <p className="mx-auto max-w-xs text-slate-400 dark:text-neutral-500">
                            Help us keep your health records private by choosing
                            a unique and complex password.
                        </p>
                    </div>
                </div>

                {/* Right Side: Form */}
                <div className="flex flex-1 flex-col justify-center bg-white px-8 py-12 lg:px-24">
                    <div className="mx-auto w-full max-w-sm">
                        <div className="mb-10">
                            <h2 className="text-3xl font-bold tracking-tight text-slate-900 dark:text-neutral-100">
                                {t('auth.confirm_password.title')}
                            </h2>
                            <p className="mt-2 font-medium text-slate-500 dark:text-neutral-500">
                                Create a new password for your account.
                            </p>
                        </div>

                        <form onSubmit={submit} className="space-y-6">
                            {/* Hidden Email or Read-only Email for UX */}
                            <div>
                                <label className="mb-2 block text-xs font-bold tracking-widest text-slate-400 uppercase dark:text-neutral-500">
                                    Account Email
                                </label>
                                <input
                                    type="email"
                                    value={data.email}
                                    readOnly
                                    className="block w-full cursor-not-allowed rounded-2xl border border-transparent bg-slate-100 px-4 py-4 text-sm text-slate-500 outline-none dark:text-neutral-500"
                                />
                            </div>

                            {/* New Password */}
                            <div>
                                <label className="mb-2 block text-xs font-bold tracking-widest text-slate-400 uppercase dark:text-neutral-500">
                                    New Password
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 dark:text-neutral-500">
                                        <Lock size={18} />
                                    </div>
                                    <input
                                        type={showPass ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={(e) =>
                                            setData('password', e.target.value)
                                        }
                                        className="block w-full rounded-2xl border border-slate-100 bg-slate-50 py-4 pr-12 pl-11 text-sm text-slate-900 transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-700 dark:text-neutral-100"
                                        placeholder="••••••••"
                                        required
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPass(!showPass)}
                                        className="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 dark:text-neutral-500"
                                    >
                                        {showPass ? (
                                            <EyeOff size={18} />
                                        ) : (
                                            <Eye size={18} />
                                        )}
                                    </button>
                                </div>
                                {errors.password && (
                                    <p className="mt-2 text-xs font-medium text-red-500">
                                        {errors.password}
                                    </p>
                                )}
                            </div>

                            {/* Confirm Password */}
                            <div>
                                <label className="mb-2 block text-xs font-bold tracking-widest text-slate-400 uppercase dark:text-neutral-500">
                                    Confirm New Password
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 dark:text-neutral-500">
                                        <Lock size={18} />
                                    </div>
                                    <input
                                        type="password"
                                        value={data.password_confirmation}
                                        onChange={(e) =>
                                            setData(
                                                'password_confirmation',
                                                e.target.value,
                                            )
                                        }
                                        className="block w-full rounded-2xl border border-slate-100 bg-slate-50 py-4 pr-4 pl-11 text-sm text-slate-900 transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-700 dark:text-neutral-100"
                                        placeholder="••••••••"
                                        required
                                    />
                                </div>
                                {errors.password_confirmation && (
                                    <p className="mt-2 text-xs font-medium text-red-500">
                                        {errors.password_confirmation}
                                    </p>
                                )}
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full rounded-2xl bg-blue-600 py-4 font-bold text-white shadow-xl shadow-blue-500/20 transition-all hover:bg-blue-700 disabled:opacity-70"
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
