import { useForm } from '@inertiajs/react';
import { Building2, Mail, MessageSquare, User } from 'lucide-react';
import type { FormEvent } from 'react';
import Button from '@/Components/marketing/Button';
import { cn } from '@/lib/utils';

interface DemoRequestFormProps {
    className?: string;
}

export default function DemoRequestForm({ className }: DemoRequestFormProps) {
    const { data, setData, post, processing, errors, wasSuccessful, reset } =
        useForm({
            name: '',
            email: '',
            company: '',
            message: '',
        });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post('/contact', {
            onSuccess: () => reset(),
        });
    };

    return (
        <div
            className={cn(
                'rounded-3xl border border-slate-100 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-900',
                className,
            )}
        >
            {wasSuccessful && (
                <div className="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300">
                    Thank you for your interest! We will be in touch shortly.
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-5">
                <div>
                    <label
                        htmlFor="name"
                        className="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-neutral-300"
                    >
                        Full Name *
                    </label>
                    <div className="relative">
                        <User className="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-slate-400 dark:text-neutral-500" />
                        <input
                            id="name"
                            type="text"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            className="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pr-4 pl-10 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500"
                            placeholder="Dr. Jane Smith"
                            required
                        />
                    </div>
                    {errors.name && (
                        <p className="mt-1 text-xs text-red-500">
                            {errors.name}
                        </p>
                    )}
                </div>

                <div>
                    <label
                        htmlFor="email"
                        className="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-neutral-300"
                    >
                        Email Address *
                    </label>
                    <div className="relative">
                        <Mail className="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-slate-400 dark:text-neutral-500" />
                        <input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            className="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pr-4 pl-10 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500"
                            placeholder="jane@hospital.com"
                            required
                        />
                    </div>
                    {errors.email && (
                        <p className="mt-1 text-xs text-red-500">
                            {errors.email}
                        </p>
                    )}
                </div>

                <div>
                    <label
                        htmlFor="company"
                        className="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-neutral-300"
                    >
                        Hospital / Company
                    </label>
                    <div className="relative">
                        <Building2 className="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-slate-400 dark:text-neutral-500" />
                        <input
                            id="company"
                            type="text"
                            value={data.company}
                            onChange={(e) => setData('company', e.target.value)}
                            className="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pr-4 pl-10 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500"
                            placeholder="City General Hospital"
                        />
                    </div>
                    {errors.company && (
                        <p className="mt-1 text-xs text-red-500">
                            {errors.company}
                        </p>
                    )}
                </div>

                <div>
                    <label
                        htmlFor="message"
                        className="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-neutral-300"
                    >
                        Message
                    </label>
                    <div className="relative">
                        <MessageSquare className="absolute top-4 left-3.5 h-4 w-4 text-slate-400 dark:text-neutral-500" />
                        <textarea
                            id="message"
                            rows={4}
                            value={data.message}
                            onChange={(e) => setData('message', e.target.value)}
                            className="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 py-3 pr-4 pl-10 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500"
                            placeholder="Tell us about your hospital's needs..."
                        />
                    </div>
                    {errors.message && (
                        <p className="mt-1 text-xs text-red-500">
                            {errors.message}
                        </p>
                    )}
                </div>

                <Button
                    type="submit"
                    size="lg"
                    className="w-full"
                    disabled={processing}
                >
                    {processing ? 'Sending...' : 'Request a Demo'}
                </Button>
            </form>
        </div>
    );
}
