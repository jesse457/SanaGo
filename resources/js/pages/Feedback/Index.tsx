import {
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
    ArrowRightIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import { useState, useMemo } from 'react';

import EmptyState from '@/Components/EmptyState';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import ViewToggle from '@/Components/ViewToggle';
import AppLayout from '@/Layouts/AppLayout';

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

function statusLabel(status: string): string {
    return status.charAt(0).toUpperCase() + status.slice(1);
}

function statusColor(status: string): 'amber' | 'emerald' | 'gray' {
    switch (status) {
        case 'pending':
            return 'amber';
        case 'resolved':
            return 'emerald';
        case 'closed':
            return 'gray';
        default:
            return 'gray';
    }
}

export default function FeedbackHistory({ feedbacks }: Props) {
    const [viewMode, setViewMode] = useState<ViewMode>('table');
    const [search, setSearch] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [selectedFeedback, setSelectedFeedback] = useState<Feedback | null>(
        null,
    );

    // --- Backend Filtering ---
    const filteredFeedback = useMemo(() => {
        return feedbacks.filter(
            (f) =>
                f.subject.toLowerCase().includes(search.toLowerCase()) ||
                f.category.toLowerCase().includes(search.toLowerCase()),
        );
    }, [search, feedbacks]);

    // --- Stats Calculation ---
    const stats = useMemo(
        () => ({
            total: feedbacks.length,
            resolved: feedbacks.filter((f) => f.status === 'resolved').length,
            pending: feedbacks.filter((f) => f.status === 'pending').length,
            closed: feedbacks.filter((f) => f.status === 'closed').length,
        }),
        [feedbacks],
    );

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

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: 'Home', href: '/' },
                        { label: 'Feedback' },
                    ]}
                />

                {/* Stats Overview */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        label="Total"
                        value={stats.total}
                        icon={ChatBubbleLeftRightIcon}
                        color="blue"
                    />
                    <StatCard
                        label="Resolved"
                        value={stats.resolved}
                        icon={CheckBadgeIcon}
                        color="emerald"
                    />
                    <StatCard
                        label="Pending"
                        value={stats.pending}
                        icon={ClockIcon}
                        color="amber"
                    />
                    <StatCard
                        label="Closed"
                        value={stats.closed}
                        icon={XMarkIcon}
                        color="purple"
                    />
                </div>

                {/* Header Controls */}
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="relative w-full sm:w-80">
                        <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                        <input
                            type="text"
                            placeholder="Search feedback..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm transition-all outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800"
                        />
                    </div>

                    <div className="flex items-center gap-3">
                        <Link
                            href={route('feedback.create')}
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition-all hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" /> New Feedback
                        </Link>
                        <ViewToggle view={viewMode} onChange={setViewMode} />
                    </div>
                </div>

                {/* Main Content */}
                {filteredFeedback.length === 0 ? (
                    <EmptyState
                        icon={InboxIcon}
                        title="No feedback found"
                        description="Submit your first suggestion using the button above."
                    />
                ) : viewMode === 'grid' ? (
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {filteredFeedback.map((feedback) => (
                            <div
                                key={feedback.id}
                                className="flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                            >
                                <div className="mb-4 flex items-start justify-between">
                                    <div className="rounded-lg bg-blue-50 p-2.5 text-blue-600">
                                        <ChatBubbleBottomCenterTextIcon className="h-6 w-6" />
                                    </div>
                                    <StatusBadge
                                        label={statusLabel(feedback.status)}
                                        color={statusColor(feedback.status)}
                                    />
                                </div>
                                <div className="mb-4 flex-grow">
                                    <h3 className="mb-1 text-lg font-bold text-gray-900 dark:text-neutral-100">
                                        {feedback.subject}
                                    </h3>
                                    <div className="mb-4 flex items-center gap-1.5 text-xs font-medium text-gray-400 uppercase dark:text-neutral-500">
                                        <TagIcon className="h-3 w-3" />{' '}
                                        {feedback.category}
                                    </div>
                                    <p className="line-clamp-2 text-sm leading-relaxed text-gray-500 dark:text-neutral-500">
                                        {feedback.message}
                                    </p>
                                </div>
                                <div className="flex items-center justify-between border-t border-gray-100 pt-4 dark:border-neutral-700">
                                    <span className="text-[10px] font-bold text-gray-400 dark:text-neutral-500">
                                        {new Date(
                                            feedback.created_at,
                                        ).toLocaleDateString()}
                                    </span>
                                    <button
                                        onClick={() =>
                                            handleViewDetails(feedback)
                                        }
                                        className="flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700"
                                    >
                                        View Details{' '}
                                        <ArrowRightIcon className="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead className="bg-gray-50 dark:bg-neutral-700">
                                <tr>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase dark:text-neutral-500">
                                        Feedback Details
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase dark:text-neutral-500">
                                        Category
                                    </th>
                                    <th className="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase dark:text-neutral-500">
                                        Status
                                    </th>
                                    <th className="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase dark:text-neutral-500">
                                        Submitted
                                    </th>
                                    <th className="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                                {filteredFeedback.map((feedback) => (
                                    <tr
                                        key={feedback.id}
                                        className="transition-colors hover:bg-gray-50 dark:hover:bg-neutral-700"
                                    >
                                        <td className="px-6 py-4">
                                            <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                {feedback.subject}
                                            </div>
                                            <div className="line-clamp-1 text-xs text-gray-500 dark:text-neutral-500">
                                                {feedback.message}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="rounded bg-gray-100 px-2 py-1 text-xs font-bold text-gray-600 capitalize dark:text-neutral-400">
                                                {feedback.category.replace(
                                                    '-',
                                                    ' ',
                                                )}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <StatusBadge
                                                label={statusLabel(
                                                    feedback.status,
                                                )}
                                                color={statusColor(
                                                    feedback.status,
                                                )}
                                            />
                                        </td>
                                        <td className="px-6 py-4 text-right text-xs font-medium text-gray-400 dark:text-neutral-500">
                                            {new Date(
                                                feedback.created_at,
                                            ).toLocaleDateString()}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <button
                                                onClick={() =>
                                                    handleViewDetails(feedback)
                                                }
                                                className="p-1.5 text-gray-400 transition-all hover:text-blue-600 dark:text-neutral-500"
                                            >
                                                <ArrowRightIcon className="h-5 w-5" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            {/* Modal: Feedback Details */}
            <Modal
                isOpen={showModal}
                onClose={() => setShowModal(false)}
                title="Feedback Details"
                maxWidth="max-w-2xl"
            >
                {selectedFeedback && (
                    <div className="space-y-8">
                        <div className="flex items-start justify-between">
                            <div>
                                <h3 className="text-xl font-bold text-gray-900 dark:text-neutral-100">
                                    {selectedFeedback.subject}
                                </h3>
                                <div className="mt-1 flex items-center gap-3">
                                    <span className="flex items-center gap-1 text-[10px] font-black tracking-widest text-blue-600 uppercase">
                                        <TagIcon className="h-3 w-3" />{' '}
                                        {selectedFeedback.category.replace(
                                            '-',
                                            ' ',
                                        )}
                                    </span>
                                    <span className="text-[10px] font-bold text-gray-400 dark:text-neutral-500">
                                        ID: #{selectedFeedback.id}
                                    </span>
                                </div>
                            </div>
                            <StatusBadge
                                label={statusLabel(selectedFeedback.status)}
                                color={statusColor(selectedFeedback.status)}
                            />
                        </div>

                        <div className="space-y-3">
                            <h4 className="text-xs font-black tracking-widest text-gray-400 uppercase dark:text-neutral-500">
                                Your Message
                            </h4>
                            <div className="rounded-2xl border border-gray-100 bg-gray-50 p-5 text-sm leading-relaxed text-gray-700 dark:border-neutral-700 dark:bg-neutral-700 dark:text-neutral-300">
                                {selectedFeedback.message}
                            </div>
                        </div>

                        <div className="space-y-3">
                            <h4 className="flex items-center gap-2 text-xs font-black tracking-widest text-gray-400 uppercase dark:text-neutral-500">
                                <ArrowPathIcon className="h-4 w-4" /> System
                                Response
                            </h4>
                            {selectedFeedback.response ? (
                                <div className="flex gap-4">
                                    <div className="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-600 font-bold text-white">
                                        {selectedFeedback.responder?.name.charAt(
                                            0,
                                        )}
                                    </div>
                                    <div className="flex-grow rounded-2xl rounded-tl-none border border-blue-100 bg-blue-50/50 p-5">
                                        <p className="mb-4 text-sm leading-relaxed text-gray-800 dark:text-neutral-100">
                                            {selectedFeedback.response}
                                        </p>
                                        <div className="flex items-center justify-between border-t border-blue-100 pt-3">
                                            <span className="text-[10px] font-bold text-blue-600">
                                                Assigned:{' '}
                                                {
                                                    selectedFeedback.responder
                                                        ?.name
                                                }
                                            </span>
                                            <span className="text-[10px] font-medium text-gray-400 dark:text-neutral-500">
                                                {new Date(
                                                    selectedFeedback.updated_at,
                                                ).toLocaleDateString()}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="rounded-2xl border border-dashed border-amber-200 bg-amber-50/30 py-10 text-center">
                                    <ClockIcon className="mx-auto mb-2 h-8 w-8 text-amber-400" />
                                    <p className="text-sm font-bold text-amber-700">
                                        Under Review
                                    </p>
                                    <p className="text-xs text-amber-600">
                                        Our team will respond shortly.
                                    </p>
                                </div>
                            )}
                        </div>

                        <div className="flex justify-end pt-4">
                            <button
                                onClick={() => setShowModal(false)}
                                className="rounded-xl bg-gray-100 px-6 py-2 text-xs font-bold transition-colors hover:bg-gray-200"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                )}
            </Modal>
        </AppLayout>
    );
}
