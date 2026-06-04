import {
    HomeIcon as HomeIconSolid,
    ChevronRightIcon as ChevronRightIconSolid
} from '@heroicons/react/20/solid';
import { PaperAirplaneIcon, ArrowPathIcon } from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import React from 'react';
import AppLayout from '@/Layouts/AppLayout';

export default function CreateFeedback() {
    // --- Form Hook ---
    const { data, setData, post, processing, errors, reset } = useForm({
        subject: '',
        category: 'dashboard',
        message: '',
    });

    // --- Submit Logic ---
    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/feedback/store', {
            onSuccess: () => reset(),
        });
    };

    return (
        <AppLayout headerTitle="Submit Feedback" headerSubtitle="Share your thoughts or report app issues">
            <Head title="Submit Feedback" />

            <div className="max-w-7xl mx-auto">
                {/* 1. Header & Breadcrumbs */}
                <header className="mb-6">
                    <nav className="flex text-xs font-medium text-gray-500 dark:text-neutral-400 mb-2">
                        <ol className="inline-flex items-center space-x-1">
                            <li className="flex items-center">
                                <Link href="/dashboard" className="hover:text-blue-600 flex items-center">
                                    <HomeIconSolid className="w-3 h-3 mr-1.5" /> Home
                                </Link>
                            </li>
                            <li className="flex items-center">
                                <ChevronRightIconSolid className="w-3 h-3 text-gray-300 mx-1" />
                                <Link href="/feedback" className="hover:text-blue-600">Feedbacks</Link>
                            </li>
                            <li className="flex items-center">
                                <ChevronRightIconSolid className="w-3 h-3 text-gray-300 mx-1" />
                                <span className="text-gray-900 dark:text-neutral-100 font-bold">Submit Feedback</span>
                            </li>
                        </ol>
                    </nav>
                    <h2 className="text-2xl font-bold text-gray-900 dark:text-neutral-100 tracking-tight">Submit Feedback</h2>
                    <p className="mt-1 text-sm text-gray-500 dark:text-neutral-400">Let us know what we can improve or report any issues you are facing.</p>
                </header>

                {/* 2. Form Content */}
                <div className="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-slate-200 dark:border-neutral-800 overflow-hidden">

                    {/* Header */}
                    <div className="px-6 py-5 border-b border-slate-100 dark:border-neutral-800 flex items-center justify-between bg-slate-50/50 dark:bg-neutral-800/30">
                        <div>
                            <h3 className="text-base font-bold text-slate-900 dark:text-neutral-100">New Feedback</h3>
                            <p className="mt-1 text-xs text-slate-500">Provide details for your submission below.</p>
                        </div>
                        <span className="hidden sm:inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/30 px-3 py-1 text-xs font-bold text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                            App Feedback
                        </span>
                    </div>

                    <form onSubmit={submit} className="p-6 sm:p-8 space-y-8">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            {/* Subject */}
                            <div className="space-y-1.5">
                                <label className="block text-sm font-bold text-slate-700 dark:text-neutral-300">Subject <span className="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    value={data.subject}
                                    onChange={e => setData('subject', e.target.value)}
                                    placeholder="Brief title of the issue/request"
                                    className="block w-full rounded-xl border-slate-200 dark:border-neutral-700 dark:bg-neutral-800 text-sm py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500"
                                />
                                {errors.subject && <p className="text-red-500 text-xs font-medium">{errors.subject}</p>}
                            </div>

                            {/* Category */}
                            <div className="space-y-1.5">
                                <label className="block text-sm font-bold text-slate-700 dark:text-neutral-300">Category <span className="text-red-500">*</span></label>
                                <select
                                    value={data.category}
                                    onChange={e => setData('category', e.target.value)}
                                    className="block w-full rounded-xl border-slate-200 dark:border-neutral-700 dark:bg-neutral-800 text-sm py-2.5 px-4"
                                >
                                    <option value="dashboard">Dashboard</option>
                                    <option value="shift-management">Shift Management</option>
                                    <option value="revenue-report">Revenue Report</option>
                                    <option value="settings">Settings</option>
                                    <option value="other">Other System Issues</option>
                                </select>
                                {errors.category && <p className="text-red-500 text-xs font-medium">{errors.category}</p>}
                            </div>
                        </div>

                        {/* Message */}
                        <div className="space-y-1.5">
                            <label className="block text-sm font-bold text-slate-700 dark:text-neutral-300">Message <span className="text-red-500">*</span></label>
                            <textarea
                                rows={6}
                                value={data.message}
                                onChange={e => setData('message', e.target.value)}
                                placeholder="Describe your experience or feature request in detail..."
                                className="block w-full rounded-xl border-slate-200 dark:border-neutral-700 dark:bg-neutral-800 text-sm py-3 px-4 focus:ring-blue-500 focus:border-blue-500 resize-none"
                            ></textarea>
                            <div className="flex justify-between items-center text-xs text-slate-400 mt-1">
                                {errors.message ? <p className="text-red-500 font-medium">{errors.message}</p> : <span></span>}
                                <span>Please keep your message clear and constructive.</span>
                            </div>
                        </div>

                        {/* Footer */}
                        <div className="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-6 pt-6 border-t border-slate-100 dark:border-neutral-800">
                            <div className="text-xs text-slate-500 dark:text-neutral-400">
                                Submitting feedback means agreeing to our community standards.
                            </div>

                            <div className="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">
                                <button
                                    type="button"
                                    onClick={() => reset()}
                                    className="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 rounded-xl border border-slate-300 dark:border-neutral-700 text-slate-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 hover:bg-slate-50 transition-all text-sm font-bold shadow-sm"
                                >
                                    Start Over
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-md disabled:opacity-50 transition-all text-sm font-bold"
                                >
                                    {processing ? (
                                        <>
                                            <ArrowPathIcon className="w-4 h-4 animate-spin" />
                                            Sending...
                                        </>
                                    ) : (
                                        <>
                                            <PaperAirplaneIcon className="w-4 h-4 -rotate-45" />
                                            Send Feedback
                                        </>
                                    )}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
