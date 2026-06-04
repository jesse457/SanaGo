import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { Mail, ArrowLeft, KeyRound, CheckCircle2 } from 'lucide-react';
import { useTranslation } from '@/hooks/useTranslation';

const ForgotPassword: React.FC = () => {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, wasSuccessful } = useForm({
        email: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/forgot-password');
    };

    return (
        <>
            <Head title="Forgot Password | Yerli HMS" />

            <div className="min-h-screen bg-white dark:bg-neutral-800 flex flex-col lg:flex-row">

                {/* Left Side: Branded Visual */}
                <div className="hidden lg:flex lg:w-1/2 relative bg-slate-900 items-center justify-center p-12 overflow-hidden">
                    <div className="absolute top-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full blur-[100px]"></div>
                    <div className="relative z-10 max-w-md">
                        <div className="w-16 h-16 bg-blue-500/20 rounded-3xl flex items-center justify-center text-blue-400 mb-8">
                            <KeyRound size={32} />
                        </div>
                        <h1 className="text-4xl font-extrabold text-white leading-tight">
                            Securing your <br />
                            <span className="text-blue-400">medical data.</span>
                        </h1>
                        <p className="mt-6 text-slate-400 dark:text-neutral-500 leading-relaxed">
                            If you've forgotten your access credentials, our automated system will help you regain access to your patient records securely.
                        </p>
                    </div>
                </div>

                {/* Right Side: Form */}
                <div className="flex-1 flex flex-col justify-center px-8 py-12 lg:px-24 bg-white dark:bg-neutral-800 relative">
                    <div className="mx-auto w-full max-w-sm">
                        <Link href="/login" className="inline-flex items-center text-sm font-bold text-slate-400 dark:text-neutral-500 hover:text-blue-600 transition mb-10 gap-2 group">
                            <ArrowLeft size={16} className="group-hover:-translate-x-1 transition-transform" /> Back to Login
                        </Link>

                        {!wasSuccessful ? (
                            <>
                                <div className="mb-10">
                                    <h2 className="text-3xl font-bold text-slate-900 dark:text-neutral-100 tracking-tight">{t('auth.forgot_password.title')}</h2>
                                    <p className="text-slate-500 dark:text-neutral-500 mt-2 font-medium">Enter the email associated with your account.</p>
                                </div>

                                <form onSubmit={submit} className="space-y-6">
                                    <div>
                                        <label className="block text-xs font-bold text-slate-400 dark:text-neutral-500 uppercase tracking-widest mb-2">{t('auth.login.email_label')}</label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-neutral-500">
                                                <Mail size={18} />
                                            </div>
                                            <input
                                                type="email"
                                                value={data.email}
                                                onChange={e => setData('email', e.target.value)}
                                                className="block w-full pl-11 pr-4 py-4 bg-slate-50 dark:bg-neutral-700 border border-slate-100 dark:border-neutral-700 rounded-2xl text-slate-900 dark:text-neutral-100 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none"
                                                placeholder="name@example.com"
                                                required
                                            />
                                        </div>
                                        {errors.email && <p className="mt-2 text-xs text-red-500 font-medium">{errors.email}</p>}
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all disabled:opacity-70"
                                    >
                                        {processing ? 'Sending...' : 'Send Reset Link'}
                                    </button>
                                </form>
                            </>
                        ) : (
                            <div className="text-center">
                                <div className="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mx-auto mb-6">
                                    <CheckCircle2 size={40} />
                                </div>
                                <h2 className="text-2xl font-bold text-slate-900 dark:text-neutral-100">Check your email</h2>
                                <p className="text-slate-500 dark:text-neutral-500 mt-4 leading-relaxed">
                                    We've sent a password reset link to <span className="font-bold text-slate-700 dark:text-neutral-300">{data.email}</span>.
                                </p>
                                <button
                                    onClick={() => window.location.reload()}
                                    className="mt-8 text-blue-600 font-bold hover:underline"
                                >
                                    Didn't receive the email? Try again
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
};

export default ForgotPassword;

