import { Head, Link, useForm } from '@inertiajs/react';
import { Mail, Lock, Eye, EyeOff, ChevronLeft, ArrowRight, Building2, Shield } from 'lucide-react';
import React, { useState } from 'react';
import { useTranslation } from '@/hooks/useTranslation';

interface LoginProps {
    isCentralDomain: boolean;
}

const Login: React.FC<LoginProps> = ({ isCentralDomain }) => {
    const { t } = useTranslation();
    const [showPassword, setShowPassword] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
        type: (isCentralDomain ? 'landlord' : 'tenant') as 'landlord' | 'tenant',
        subdomain: '',
    });

    const handleSubmit = (e: React.SyntheticEvent) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <>
            <Head title="Login | SanaGo HMS Portal" />

            <div className="flex min-h-screen flex-col bg-white lg:flex-row dark:bg-neutral-800">
                {/* Left Side: Branding & Visual (Hidden on mobile) */}
                <div className="relative hidden items-center justify-center overflow-hidden bg-slate-900 p-12 lg:flex lg:w-1/2 dark:bg-neutral-800">
                    {/* Background Decorative Gradient */}
                    <div className="absolute top-0 right-0 -mt-64 -mr-64 h-[500px] w-[500px] rounded-full bg-blue-600/20 blur-[120px]"></div>
                    <div className="absolute bottom-0 left-0 -mb-64 -ml-64 h-[500px] w-[500px] rounded-full bg-blue-400/10 blur-[120px]"></div>

                    <div className="relative z-10 w-full max-w-lg">
                        {/* Logo */}
                        <Link
                            href="/"
                            className="group mb-12 flex items-center gap-2"
                        >
                            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500 font-bold text-white transition-transform group-hover:scale-110">
                                S
                            </div>
                            <span className="text-2xl font-black tracking-tighter text-white">
                                SanaGo
                            </span>
                        </Link>

                        <h1 className="text-5xl leading-tight font-extrabold text-white">
                            Manage your health <br />
                            <span className="text-blue-400">
                                with precision.
                            </span>
                        </h1>
                        <p className="mt-6 text-lg leading-relaxed text-slate-400 dark:text-neutral-500">
                            Access your personalized patient dashboard, view
                            medical records, and schedule appointments with the
                            top specialists in Manhattan.
                        </p>

                        {/* Testimonial Snippet */}
                        <div className="mt-12 rounded-[32px] border border-white/10 bg-white/5 p-8 backdrop-blur-lg dark:bg-neutral-800/5">
                            <p className="text-sm leading-relaxed text-white/80 italic">
                                "The portal makes it so easy to track my
                                recovery and communicate with Dr. Reed. A truly
                                modern healthcare experience."
                            </p>
                            <div className="mt-4 flex items-center gap-3">
                                <div className="flex h-8 w-8 items-center justify-center rounded-full border border-blue-500/30 bg-blue-500/20 text-[10px] font-bold text-blue-400">
                                    SJ
                                </div>
                                <span className="text-xs font-bold tracking-widest text-white uppercase">
                                    Sarah Jenkins
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* The signature "Curved" Image from the design */}
                    <div className="absolute right-[-10%] bottom-[-10%] w-2/3 opacity-20">
                        <svg
                            viewBox="0 0 200 200"
                            xmlns="http://www.w3.org/2000/svg"
                            className="fill-blue-500"
                        >
                            <path
                                d="M44.7,-76.4C58.1,-69.2,69.5,-57.4,77.5,-43.8C85.5,-30.1,90,-15.1,89.1,-0.5C88.2,14.1,81.8,28.2,73,40.4C64.1,52.6,52.8,62.8,39.9,70.5C27,78.2,12.5,83.4,-1.8,86.5C-16.1,89.6,-32.2,90.6,-45.8,84.1C-59.4,77.6,-70.5,63.6,-77.8,48.7C-85.1,33.8,-88.6,18.1,-87.6,2.6C-86.7,-12.9,-81.3,-28.2,-72.1,-41.1C-62.9,-54,-49.9,-64.5,-35.8,-71.3C-21.7,-78.1,-8.8,-81.2,5.5,-81.7C19.8,-82.2,31.3,-83.6,44.7,-76.4Z"
                                transform="translate(100 100)"
                            />
                        </svg>
                    </div>
                </div>

                {/* Right Side: Login Form */}
                <div className="relative flex flex-1 flex-col justify-center bg-white px-6 py-12 lg:px-24 dark:bg-neutral-800">
                    {/* Mobile Logo Only */}
                    <div className="mb-8 flex justify-center lg:hidden">
                        <div className="flex items-center gap-2">
                            <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 font-bold text-white">
                                S
                            </div>
                            <span className="text-xl font-black tracking-tighter text-slate-800 dark:text-neutral-100">
                                SanaGo
                            </span>
                        </div>
                    </div>

                    <div className="mx-auto w-full max-w-sm">
                        <Link
                            href="/"
                            className="mb-10 hidden items-center gap-1 text-sm font-bold text-slate-400 transition hover:text-blue-600 lg:flex dark:text-neutral-500"
                        >
                            <ChevronLeft size={16} /> Back to website
                        </Link>

                        <div className="mb-10">
                            <h2 className="text-3xl font-bold tracking-tight text-slate-900 dark:text-neutral-100">
                                {t('auth.login.title')}
                            </h2>
                            <p className="mt-2 font-medium text-slate-500 dark:text-neutral-500">
                                {t('auth.login.subtitle')}
                            </p>
                        </div>

                        <form onSubmit={handleSubmit} className="space-y-6">
                            {/* Login Type Toggle */}
                            <div>
                                <div className="flex rounded-xl border border-slate-100 bg-slate-50 p-1 dark:border-neutral-700 dark:bg-neutral-700">
                                    <button
                                        type="button"
                                        onClick={() => setData('type', 'landlord')}
                                        className={`flex flex-1 items-center justify-center gap-1.5 rounded-lg py-2.5 text-sm font-bold transition-all ${
                                            data.type === 'landlord'
                                                ? 'bg-white text-slate-900 shadow-sm dark:bg-neutral-600 dark:text-neutral-100'
                                                : 'text-slate-500 hover:text-slate-700 dark:text-neutral-400'
                                        }`}
                                    >
                                        <Shield size={14} />
                                        {t('auth.login.landlord_tab')}
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setData('type', 'tenant')}
                                        className={`flex flex-1 items-center justify-center gap-1.5 rounded-lg py-2.5 text-sm font-bold transition-all ${
                                            data.type === 'tenant'
                                                ? 'bg-white text-slate-900 shadow-sm dark:bg-neutral-600 dark:text-neutral-100'
                                                : 'text-slate-500 hover:text-slate-700 dark:text-neutral-400'
                                        }`}
                                    >
                                        <Building2 size={14} />
                                        {t('auth.login.tenant_tab')}
                                    </button>
                                </div>
                            </div>

                            {/* Subdomain Field (Tenant only) */}
                            {data.type === 'tenant' && (
                                <div>
                                    <label
                                        className="mb-2 block text-xs font-bold tracking-widest text-slate-400 uppercase dark:text-neutral-500"
                                        htmlFor="subdomain"
                                    >
                                        {t('auth.login.subdomain_label')}
                                    </label>
                                    <div className="relative">
                                        <input
                                            id="subdomain"
                                            type="text"
                                            value={data.subdomain}
                                            onChange={(e) =>
                                                setData('subdomain', e.target.value)
                                            }
                                            className="block w-full rounded-2xl border border-slate-100 bg-slate-50 py-4 pr-4 pl-4 text-sm text-slate-900 transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-700 dark:text-neutral-100"
                                            placeholder={t('auth.login.subdomain_placeholder')}
                                            required
                                        />
                                    </div>
                                    {errors.subdomain && (
                                        <p className="mt-1 text-xs font-medium text-red-500">
                                            {errors.subdomain}
                                        </p>
                                    )}
                                </div>
                            )}

                            {/* Email Field */}
                            <div>
                                <label
                                    className="mb-2 block text-xs font-bold tracking-widest text-slate-400 uppercase dark:text-neutral-500"
                                    htmlFor="email"
                                >
                                    {t('auth.login.email_label')}
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 dark:text-neutral-500">
                                        <Mail size={18} />
                                    </div>
                                    <input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) =>
                                            setData('email', e.target.value)
                                        }
                                        className="block w-full rounded-2xl border border-slate-100 bg-slate-50 py-4 pr-4 pl-11 text-sm text-slate-900 transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-700 dark:text-neutral-100"
                                        placeholder="name@example.com"
                                        required
                                    />
                                </div>
                                {errors.email && (
                                    <p className="mt-1 text-xs font-medium text-red-500">
                                        {errors.email}
                                    </p>
                                )}
                            </div>

                            {/* Password Field */}
                            <div>
                                <div className="mb-2 flex justify-between">
                                    <label
                                        className="block text-xs font-bold tracking-widest text-slate-400 uppercase dark:text-neutral-500"
                                        htmlFor="password"
                                    >
                                        {t('auth.login.password_label')}
                                    </label>
                                    <Link
                                        href="/forgot-password"
                                        className="text-xs font-bold text-blue-600 hover:text-blue-700"
                                    >
                                        {t('auth.login.forgot_password')}
                                    </Link>
                                </div>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 dark:text-neutral-500">
                                        <Lock size={18} />
                                    </div>
                                    <input
                                        id="password"
                                        type={
                                            showPassword ? 'text' : 'password'
                                        }
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
                                        onClick={() =>
                                            setShowPassword(!showPassword)
                                        }
                                        className="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 dark:text-neutral-400 dark:text-neutral-500 dark:hover:text-neutral-400"
                                    >
                                        {showPassword ? (
                                            <EyeOff size={18} />
                                        ) : (
                                            <Eye size={18} />
                                        )}
                                    </button>
                                </div>
                                {errors.password && (
                                    <p className="mt-1 text-xs font-medium text-red-500">
                                        {errors.password}
                                    </p>
                                )}
                            </div>

                            {/* Remember Me */}
                            <div className="flex items-center">
                                <input
                                    id="remember"
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={(e) =>
                                        setData('remember', e.target.checked)
                                    }
                                    className="h-4 w-4 rounded border-slate-200 text-blue-600 focus:ring-blue-500 dark:border-neutral-700"
                                />
                                <label
                                    htmlFor="remember"
                                    className="ml-2 block text-sm font-medium text-slate-500 dark:text-neutral-500"
                                >
                                    {t('auth.login.remember_me')}
                                </label>
                            </div>

                            {/* Submit Button */}
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-600 py-4 font-bold text-white shadow-xl shadow-blue-500/20 transition-all hover:bg-blue-700 active:scale-[0.98] disabled:opacity-70"
                            >
                                {processing
                                    ? t('auth.login.submitting')
                                    : t('auth.login.submit')}
                                {!processing && <ArrowRight size={18} />}
                            </button>
                        </form>

                        {/* Footer */}
                        <p className="mt-10 text-center text-sm font-medium text-slate-500 dark:text-neutral-500">
                            {t('auth.login.contact_admin')}
                        </p>
                    </div>

                    {/* Form Footer */}
                    <div className="absolute right-0 bottom-8 left-0 flex justify-center gap-6 text-[10px] font-bold tracking-widest text-slate-300 uppercase dark:text-neutral-600">
                        <Link
                            href="#"
                            className="transition hover:text-slate-500 dark:text-neutral-500"
                        >
                            Support
                        </Link>
                        <Link
                            href="#"
                            className="transition hover:text-slate-500 dark:text-neutral-500"
                        >
                            Privacy
                        </Link>
                        <Link
                            href="#"
                            className="transition hover:text-slate-500 dark:text-neutral-500"
                        >
                            Security
                        </Link>
                    </div>
                </div>
            </div>
        </>
    );
};

export default Login;
