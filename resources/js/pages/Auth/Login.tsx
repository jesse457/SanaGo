import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import {
  Mail,
  Lock,
  Eye,
  EyeOff,
  ChevronLeft,
  ArrowRight
} from 'lucide-react';
import { useTranslation } from '@/hooks/useTranslation';

const Login: React.FC = () => {
  const { t } = useTranslation();
  const [showPassword, setShowPassword] = useState(false);

  // Inertia form helper (Standard for Laravel/Inertia setups)
  const { data, setData, post, processing, errors } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/login');
  };

  return (
    <>
      <Head title="Login | Yerli HMS Portal" />

      <div className="min-h-screen bg-white dark:bg-neutral-800 flex flex-col lg:flex-row">

        {/* Left Side: Branding & Visual (Hidden on mobile) */}
        <div className="hidden lg:flex lg:w-1/2 relative bg-slate-900 dark:bg-neutral-800 overflow-hidden items-center justify-center p-12">
          {/* Background Decorative Gradient */}
          <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[120px] -mr-64 -mt-64"></div>
          <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-400/10 rounded-full blur-[120px] -ml-64 -mb-64"></div>

          <div className="relative z-10 w-full max-w-lg">
            {/* Logo */}
            <Link href="/" className="flex items-center gap-2 mb-12 group">
              <div className="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center text-white font-bold group-hover:scale-110 transition-transform">Y</div>
              <span className="text-2xl font-black tracking-tighter text-white">YERLI</span>
            </Link>

            <h1 className="text-5xl font-extrabold text-white leading-tight">
              Manage your health <br />
              <span className="text-blue-400">with precision.</span>
            </h1>
            <p className="mt-6 text-slate-400 dark:text-neutral-500 text-lg leading-relaxed">
              Access your personalized patient dashboard, view medical records, and schedule appointments with the top specialists in Manhattan.
            </p>

            {/* Testimonial Snippet */}
            <div className="mt-12 p-8 bg-white/5 dark:bg-neutral-800/5 backdrop-blur-lg border border-white/10 rounded-[32px]">
              <p className="text-white/80 italic text-sm leading-relaxed">
                "The portal makes it so easy to track my recovery and communicate with Dr. Reed. A truly modern healthcare experience."
              </p>
              <div className="mt-4 flex items-center gap-3">
                <div className="w-8 h-8 rounded-full bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-[10px] font-bold text-blue-400">SJ</div>
                <span className="text-xs font-bold text-white uppercase tracking-widest">Sarah Jenkins</span>
              </div>
            </div>
          </div>

          {/* The signature "Curved" Image from the design */}
          <div className="absolute bottom-[-10%] right-[-10%] w-2/3 opacity-20">
             <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" className="fill-blue-500">
                <path d="M44.7,-76.4C58.1,-69.2,69.5,-57.4,77.5,-43.8C85.5,-30.1,90,-15.1,89.1,-0.5C88.2,14.1,81.8,28.2,73,40.4C64.1,52.6,52.8,62.8,39.9,70.5C27,78.2,12.5,83.4,-1.8,86.5C-16.1,89.6,-32.2,90.6,-45.8,84.1C-59.4,77.6,-70.5,63.6,-77.8,48.7C-85.1,33.8,-88.6,18.1,-87.6,2.6C-86.7,-12.9,-81.3,-28.2,-72.1,-41.1C-62.9,-54,-49.9,-64.5,-35.8,-71.3C-21.7,-78.1,-8.8,-81.2,5.5,-81.7C19.8,-82.2,31.3,-83.6,44.7,-76.4Z" transform="translate(100 100)" />
             </svg>
          </div>
        </div>

        {/* Right Side: Login Form */}
        <div className="flex-1 flex flex-col justify-center px-6 py-12 lg:px-24 bg-white dark:bg-neutral-800 relative">

          {/* Mobile Logo Only */}
          <div className="lg:hidden flex justify-center mb-8">
            <div className="flex items-center gap-2">
              <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">Y</div>
              <span className="text-xl font-black tracking-tighter text-slate-800 dark:text-neutral-100">YERLI</span>
            </div>
          </div>

          <div className="mx-auto w-full max-w-sm">
            <Link href="/" className="hidden lg:flex items-center text-sm font-bold text-slate-400 dark:text-neutral-500 hover:text-blue-600 transition mb-10 gap-1">
              <ChevronLeft size={16} /> Back to website
            </Link>

            <div className="mb-10">
              <h2 className="text-3xl font-bold text-slate-900 dark:text-neutral-100 tracking-tight">{t('auth.login.title')}</h2>
              <p className="text-slate-500 dark:text-neutral-500 mt-2 font-medium">{t('auth.login.subtitle')}</p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-6">
              {/* Email Field */}
              <div>
                <label className="block text-xs font-bold text-slate-400 dark:text-neutral-500 uppercase tracking-widest mb-2" htmlFor="email">
                  {t('auth.login.email_label')}
                </label>
                <div className="relative">
                  <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-neutral-500">
                    <Mail size={18} />
                  </div>
                  <input
                    id="email"
                    type="email"
                    value={data.email}
                    onChange={e => setData('email', e.target.value)}
                    className="block w-full pl-11 pr-4 py-4 bg-slate-50 dark:bg-neutral-700 border border-slate-100 dark:border-neutral-700 rounded-2xl text-slate-900 dark:text-neutral-100 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none"
                    placeholder="name@example.com"
                    required
                  />
                </div>
                {errors.email && <p className="mt-1 text-xs text-red-500 font-medium">{errors.email}</p>}
              </div>

              {/* Password Field */}
              <div>
                <div className="flex justify-between mb-2">
                  <label className="block text-xs font-bold text-slate-400 dark:text-neutral-500 uppercase tracking-widest" htmlFor="password">
                    {t('auth.login.password_label')}
                  </label>
                  <Link href="#" className="text-xs font-bold text-blue-600 hover:text-blue-700">
                    {t('auth.login.forgot_password')}
                  </Link>
                </div>
                <div className="relative">
                  <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-neutral-500">
                    <Lock size={18} />
                  </div>
                  <input
                    id="password"
                    type={showPassword ? 'text' : 'password'}
                    value={data.password}
                    onChange={e => setData('password', e.target.value)}
                    className="block w-full pl-11 pr-12 py-4 bg-slate-50 dark:bg-neutral-700 border border-slate-100 dark:border-neutral-700 rounded-2xl text-slate-900 dark:text-neutral-100 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none"
                    placeholder="••••••••"
                    required
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 dark:text-neutral-500 hover:text-slate-600 dark:text-neutral-400 dark:hover:text-neutral-400"
                  >
                    {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                  </button>
                </div>
                {errors.password && <p className="mt-1 text-xs text-red-500 font-medium">{errors.password}</p>}
              </div>

              {/* Remember Me */}
              <div className="flex items-center">
                <input
                  id="remember"
                  type="checkbox"
                  checked={data.remember}
                  onChange={e => setData('remember', e.target.checked)}
                  className="w-4 h-4 text-blue-600 border-slate-200 dark:border-neutral-700 rounded focus:ring-blue-500"
                />
                <label htmlFor="remember" className="ml-2 block text-sm font-medium text-slate-500 dark:text-neutral-500">
                  {t('auth.login.remember_me')}
                </label>
              </div>

              {/* Submit Button */}
              <button
                type="submit"
                disabled={processing}
                className="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all active:scale-[0.98] disabled:opacity-70"
              >
                {processing ? t('auth.login.submitting') : t('auth.login.submit')}
                {!processing && <ArrowRight size={18} />}
              </button>
            </form>

            {/* Footer Links */}
            <p className="mt-10 text-center text-sm text-slate-500 dark:text-neutral-500 font-medium">
              New patient?{' '}
              <Link href="/register" className="text-blue-600 font-bold hover:underline">
                Create an account
              </Link>
            </p>
          </div>

          {/* Form Footer */}
          <div className="absolute bottom-8 left-0 right-0 flex justify-center gap-6 text-[10px] font-bold text-slate-300 dark:text-neutral-600 uppercase tracking-widest">
            <Link href="#" className="hover:text-slate-500 dark:text-neutral-500 transition">Support</Link>
            <Link href="#" className="hover:text-slate-500 dark:text-neutral-500 transition">Privacy</Link>
            <Link href="#" className="hover:text-slate-500 dark:text-neutral-500 transition">Security</Link>
          </div>
        </div>

      </div>
    </>
  );
};

export default Login;
