import { Head, Link, useForm } from '@inertiajs/react';
import { Mail, ArrowLeft, KeyRound, CheckCircle2 } from 'lucide-react';
import React from 'react';
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

            <div className="flex min-h-screen flex-col bg-white lg:flex-row dark:bg-neutral-800">
                {/* Left Side: Branded Visual */}
                <div className="relative hidden items-center justify-center overflow-hidden bg-slate-900 p-12 lg:flex lg:w-1/2">
                    <div className="absolute top-0 right-0 h-96 w-96 rounded-full bg-blue-500/10 blur-[100px]"></div>
                    <div className="relative z-10 max-w-md">
                        <div className="mb-8 flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-500/20 text-blue-400">
                            <KeyRound size={32} />
                        </div>
                        <h1 className="text-4xl leading-tight font-extrabold text-white">
                            Securing your <br />
                            <span className="text-blue-400">medical data.</span>
                        </h1>
                        <p className="mt-6 leading-relaxed text-slate-400 dark:text-neutral-500">
                            If you've forgotten your access credentials, our
                            automated system will help you regain access to your
                            patient records securely.
                        </p>
                    </div>
                </div>

                {/* Right Side: Form */}
                <div className="relative flex flex-1 flex-col justify-center bg-white px-8 py-12 lg:px-24 dark:bg-neutral-800">
                    <div className="mx-auto w-full max-w-sm">
                        <Link
                            href="/login"
                            className="group mb-10 inline-flex items-center gap-2 text-sm font-bold text-slate-400 transition hover:text-blue-600 dark:text-neutral-500"
                        >
                            <ArrowLeft
                                size={16}
                                className="transition-transform group-hover:-translate-x-1"
                            />{' '}
                            Back to Login
                        </Link>

                        {!wasSuccessful ? (
                            <>
                                <div className="mb-10">
                                    <h2 className="text-3xl font-bold tracking-tight text-slate-900 dark:text-neutral-100">
                                        {t('auth.forgot_password.title')}
                                    </h2>
                                    <p className="mt-2 font-medium text-slate-500 dark:text-neutral-500">
                                        Enter the email associated with your
                                        account.
                                    </p>
                                </div>

                                <form onSubmit={submit} className="space-y-6">
                                    <div>
                                        <label className="mb-2 block text-xs font-bold tracking-widest text-slate-400 uppercase dark:text-neutral-500">
                                            {t('auth.login.email_label')}
                                        </label>
                                        <div className="relative">
                                            <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 dark:text-neutral-500">
                                                <Mail size={18} />
                                            </div>
                                            <input
                                                type="email"
                                                value={data.email}
                                                onChange={(e) =>
                                                    setData(
                                                        'email',
                                                        e.target.value,
                                                    )
                                                }
                                                className="block w-full rounded-2xl border border-slate-100 bg-slate-50 py-4 pr-4 pl-11 text-sm text-slate-900 transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-700 dark:text-neutral-100"
                                                placeholder="name@example.com"
                                                required
                                            />
                                        </div>
                                        {errors.email && (
                                            <p className="mt-2 text-xs font-medium text-red-500">
                                                {errors.email}
                                            </p>
                                        )}
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full rounded-2xl bg-blue-600 py-4 font-bold text-white shadow-xl shadow-blue-500/20 transition-all hover:bg-blue-700 disabled:opacity-70"
                                    >
                                        {processing
                                            ? 'Sending...'
                                            : 'Send Reset Link'}
                                    </button>
                                </form>
                            </>
                        ) : (
                            <div className="text-center">
                                <div className="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 text-emerald-500">
                                    <CheckCircle2 size={40} />
                                </div>
                                <h2 className="text-2xl font-bold text-slate-900 dark:text-neutral-100">
                                    Check your email
                                </h2>
                                <p className="mt-4 leading-relaxed text-slate-500 dark:text-neutral-500">
                                    We've sent a password reset link to{' '}
                                    <span className="font-bold text-slate-700 dark:text-neutral-300">
                                        {data.email}
                                    </span>
                                    .
                                </p>
                                <button
                                    onClick={() => window.location.reload()}
                                    className="mt-8 font-bold text-blue-600 hover:underline"
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
