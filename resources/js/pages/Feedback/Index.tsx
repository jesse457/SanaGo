import React, { useState, useMemo } from 'react';
import { Head, Link } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    HomeIcon,
    ChevronRightIcon,
    MagnifyingGlassIcon,
    PlusIcon,
    ArrowPathIcon,
    ChatBubbleLeftRightIcon,
    XMarkIcon,
    TagIcon,
    ClockIcon,
    InboxIcon,
    CheckBadgeIcon,
    ChatBubbleBottomCenterTextIcon,
    Squares2X2Icon,
    ListBulletIcon,
    ArrowRightIcon
} from '@heroicons/react/24/outline';

import AppLayout from '@/Layouts/AppLayout';
import Modal from '@/Components/Modal'; // Using your reusable modal

// --- Types ---
interface Feedback {
    id: number;
    subject: string;
    message: string;
    category: string;
    status: 'pending' | 'resolved' | 'closed';
    created_at: string;
    updated_at: string;
    response?: string;
    responder?: { name: string };
}

interface Props {
    feedbacks: Feedback[];
}

type ViewMode = 'grid' | 'table';

// --- Animation Variants ---
const containerVariants = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.05 } }
};

const itemVariants = {
    hidden: { y: 20, opacity: 0 },
    visible: { y: 0, opacity: 1, transition: { type: 'spring', stiffness: 300, damping: 24 } }
};

export default function FeedbackHistory({ feedbacks }: Props) {
    const [viewMode, setViewMode] = useState<ViewMode>('table');
    const [search, setSearch] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [selectedFeedback, setSelectedFeedback] = useState<Feedback | null>(null);

    // --- Backend Filtering ---
    const filteredFeedback = useMemo(() => {
        return feedbacks.filter(f =>
            f.subject.toLowerCase().includes(search.toLowerCase()) ||
            f.category.toLowerCase().includes(search.toLowerCase())
        );
    }, [search, feedbacks]);

    // --- Stats Calculation ---
    const stats = useMemo(() => ({
        total: feedbacks.length,
        resolved: feedbacks.filter(f => f.status === 'resolved').length,
        pending: feedbacks.filter(f => f.status === 'pending').length,
        closed: feedbacks.filter(f => f.status === 'closed').length,
    }), [feedbacks]);

    const handleViewDetails = (feedback: Feedback) => {
        setSelectedFeedback(feedback);
        setShowModal(true);
    };

    return (
        <AppLayout
            headerTitle="Feedback History"
            headerSubtitle="Track your requests and system suggestions."
        >
            <Head title="Feedback History" />

            <motion.div
                initial="hidden"
                animate="visible"
                variants={containerVariants}
                className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-12"
            >
                {/* --- Breadcrumbs --- */}
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 transition-colors">
                            <HomeIcon className="w-4 h-4 mr-1" /> Home
                        </li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-medium">Feedback</li>
                    </ol>
                </nav>

                {/* --- Stats Overview --- */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard label="Total" value={stats.total} icon={<ChatBubbleLeftRightIcon />} color="blue" delay={0.1} />
                    <StatCard label="Resolved" value={stats.resolved} icon={<CheckBadgeIcon />} color="emerald" delay={0.2} />
                    <StatCard label="Pending" value={stats.pending} icon={<ClockIcon />} color="amber" delay={0.3} />
                    <StatCard label="Closed" value={stats.closed} icon={<XMarkIcon />} color="purple" delay={0.4} />
                </div>

                {/* --- Header Controls --- */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="relative w-full sm:w-80">
                        <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                        <input
                            type="text"
                            placeholder="Search feedback..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="pl-9 pr-4 py-2 w-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                        />
                    </div>

                    <div className="flex items-center gap-3">
                        <Link
                            href={route('feedback.create')}
                            className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-blue-700 transition-all"
                        >
                            <PlusIcon className="w-4 h-4" /> New Feedback
                        </Link>
                        <div className="flex bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-1 shadow-sm">
                            <button onClick={() => setViewMode('grid')} className={`p-1.5 rounded-md transition-all ${viewMode === 'grid' ? 'bg-blue-50 text-blue-600' : 'text-gray-400 dark:text-neutral-500'}`}>
                                <Squares2X2Icon className="w-5 h-5" />
                            </button>
                            <button onClick={() => setViewMode('table')} className={`p-1.5 rounded-md transition-all ${viewMode === 'table' ? 'bg-blue-50 text-blue-600' : 'text-gray-400 dark:text-neutral-500'}`}>
                                <ListBulletIcon className="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>

                {/* --- Main Content --- */}
                <AnimatePresence mode="wait">
                    {filteredFeedback.length === 0 ? (
                        <motion.div
                            key="empty"
                            initial={{ opacity: 0 }} animate={{ opacity: 1 }}
                            className="text-center py-20 bg-white dark:bg-neutral-800 rounded-xl border border-dashed border-gray-300"
                        >
                            <InboxIcon className="w-12 h-12 text-gray-300 mx-auto mb-4" />
                            <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100">No feedback found</h3>
                            <p className="text-xs text-gray-500 dark:text-neutral-500 mt-1">Submit your first suggestion using the button above.</p>
                        </motion.div>
                    ) : viewMode === 'grid' ? (
                        <motion.div
                            key="grid"
                            initial={{ opacity: 0, x: -20 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: 20 }}
                            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            {filteredFeedback.map((feedback) => (
                                <motion.div
                                    key={feedback.id}
                                    variants={itemVariants}
                                    className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 hover:border-blue-400 hover:shadow-md transition-all flex flex-col h-full"
                                >
                                    <div className="flex justify-between items-start mb-4">
                                        <div className="p-2.5 rounded-lg bg-blue-50 text-blue-600">
                                            <ChatBubbleBottomCenterTextIcon className="w-6 h-6" />
                                        </div>
                                        <StatusBadge status={feedback.status} />
                                    </div>
                                    <div className="mb-4 flex-grow">
                                        <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100 mb-1">{feedback.subject}</h3>
                                        <div className="flex items-center gap-1.5 text-xs text-gray-400 dark:text-neutral-500 mb-4 font-medium uppercase">
                                            <TagIcon className="w-3 h-3" /> {feedback.category}
                                        </div>
                                        <p className="text-sm text-gray-500 dark:text-neutral-500 line-clamp-2 leading-relaxed">
                                            {feedback.message}
                                        </p>
                                    </div>
                                    <div className="pt-4 border-t border-gray-100 dark:border-neutral-700 flex items-center justify-between">
                                        <span className="text-[10px] font-bold text-gray-400 dark:text-neutral-500">
                                            {new Date(feedback.created_at).toLocaleDateString()}
                                        </span>
                                        <button
                                            onClick={() => handleViewDetails(feedback)}
                                            className="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg font-bold text-xs"
                                        >
                                            View Details <ArrowRightIcon className="w-4 h-4" />
                                        </button>
                                    </div>
                                </motion.div>
                            ))}
                        </motion.div>
                    ) : (
                        <motion.div
                            key="table"
                            initial={{ opacity: 0, x: 20 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: -20 }}
                            className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden"
                        >
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead className="bg-gray-50 dark:bg-neutral-700">
                                    <tr>
                                        <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase">Feedback Details</th>
                                        <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase">Category</th>
                                        <th className="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase">Status</th>
                                        <th className="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase">Submitted</th>
                                        <th className="px-6 py-4"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                                    {filteredFeedback.map((feedback) => (
                                        <tr key={feedback.id} className="hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                                            <td className="px-6 py-4">
                                                <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">{feedback.subject}</div>
                                                <div className="text-xs text-gray-500 dark:text-neutral-500 line-clamp-1">{feedback.message}</div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <span className="text-xs font-bold text-gray-600 dark:text-neutral-400 bg-gray-100 px-2 py-1 rounded capitalize">
                                                    {feedback.category.replace('-', ' ')}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <StatusBadge status={feedback.status} />
                                            </td>
                                            <td className="px-6 py-4 text-right text-xs font-medium text-gray-400 dark:text-neutral-500">
                                                {new Date(feedback.created_at).toLocaleDateString()}
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <button onClick={() => handleViewDetails(feedback)} className="p-1.5 text-gray-400 dark:text-neutral-500 hover:text-blue-600 transition-all">
                                                    <ArrowRightIcon className="w-5 h-5" />
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </motion.div>
                    )}
                </AnimatePresence>
            </motion.div>

            {/* --- REUSABLE MODAL: DETAILS --- */}
            <Modal
                isOpen={showModal}
                onClose={() => setShowModal(false)}
                title="Feedback Details"
                maxWidth="max-w-2xl"
            >
                {selectedFeedback && (
                    <div className="space-y-8">
                        <div className="flex justify-between items-start">
                            <div>
                                <h3 className="text-xl font-bold text-gray-900 dark:text-neutral-100">{selectedFeedback.subject}</h3>
                                <div className="flex items-center gap-3 mt-1">
                                    <span className="text-[10px] font-black text-blue-600 uppercase tracking-widest flex items-center gap-1">
                                        <TagIcon className="w-3 h-3" /> {selectedFeedback.category.replace('-', ' ')}
                                    </span>
                                    <span className="text-[10px] text-gray-400 dark:text-neutral-500 font-bold">ID: #{selectedFeedback.id}</span>
                                </div>
                            </div>
                            <StatusBadge status={selectedFeedback.status} />
                        </div>

                        <div className="space-y-3">
                            <h4 className="text-xs font-black text-gray-400 dark:text-neutral-500 uppercase tracking-widest">Your Message</h4>
                            <div className="p-5 bg-gray-50 dark:bg-neutral-700 rounded-2xl border border-gray-100 dark:border-neutral-700 text-sm text-gray-700 dark:text-neutral-300 leading-relaxed">
                                {selectedFeedback.message}
                            </div>
                        </div>

                        <div className="space-y-3">
                            <h4 className="text-xs font-black text-gray-400 dark:text-neutral-500 uppercase tracking-widest flex items-center gap-2">
                                <ArrowPathIcon className="w-4 h-4" /> System Response
                            </h4>
                            {selectedFeedback.response ? (
                                <div className="flex gap-4">
                                    <div className="h-10 w-10 flex-shrink-0 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white">
                                        {selectedFeedback.responder?.name.charAt(0)}
                                    </div>
                                    <div className="flex-grow bg-blue-50/50 rounded-2xl rounded-tl-none p-5 border border-blue-100">
                                        <p className="text-sm text-gray-800 dark:text-neutral-100 leading-relaxed mb-4">{selectedFeedback.response}</p>
                                        <div className="flex items-center justify-between border-t border-blue-100 pt-3">
                                            <span className="text-[10px] font-bold text-blue-600">Assigned: {selectedFeedback.responder?.name}</span>
                                            <span className="text-[10px] text-gray-400 dark:text-neutral-500 font-medium">{new Date(selectedFeedback.updated_at).toLocaleDateString()}</span>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="text-center py-10 bg-amber-50/30 rounded-2xl border border-dashed border-amber-200">
                                    <ClockIcon className="mx-auto h-8 w-8 text-amber-400 mb-2" />
                                    <p className="text-sm font-bold text-amber-700">Under Review</p>
                                    <p className="text-xs text-amber-600">Our team will respond shortly.</p>
                                </div>
                            )}
                        </div>

                        <div className="flex justify-end pt-4">
                            <button onClick={() => setShowModal(false)} className="px-6 py-2 bg-gray-100 rounded-xl text-xs font-bold hover:bg-gray-200 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                )}
            </Modal>
        </AppLayout>
    );
}

// --- Sub-Components ---

function StatCard({ label, value, icon, color, delay }: any) {
    const colorClasses: any = {
        blue: 'bg-blue-50 text-blue-600',
        emerald: 'bg-emerald-50 text-emerald-600',
        purple: 'bg-purple-50 text-purple-600',
        amber: 'bg-amber-50 text-amber-600'
    };

    return (
        <motion.div
            initial={{ scale: 0.95, opacity: 0 }}
            animate={{ scale: 1, opacity: 1 }}
            transition={{ delay, type: 'spring', stiffness: 200 }}
            className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm flex items-center gap-4"
        >
            <div className={`p-3 rounded-lg ${colorClasses[color]}`}>
                {React.isValidElement(icon) && React.cloneElement(icon as React.ReactElement, { className: "w-5 h-5" } as any)}
            </div>
            <div>
                <p className="text-xl font-bold text-gray-900 dark:text-neutral-100 leading-none">{value}</p>
                <p className="text-xs font-semibold text-gray-500 dark:text-neutral-500 mt-1">{label}</p>
            </div>
        </motion.div>
    );
}

function StatusBadge({ status }: { status: string }) {
    const styles: any = {
        'pending': 'bg-amber-50 text-amber-700 border-amber-100',
        'resolved': 'bg-emerald-50 text-emerald-700 border-emerald-100',
        'closed': 'bg-gray-100 text-gray-500 dark:text-neutral-500 border-gray-200 dark:border-neutral-700',
    };

    return (
        <span className={`px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border ${styles[status] || 'bg-gray-50 text-gray-600 dark:text-neutral-400 border-gray-100 dark:border-neutral-700'}`}>
            {status}
        </span>
    );
}