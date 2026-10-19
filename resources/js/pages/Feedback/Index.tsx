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
import {
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
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
                        <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink-subtle" />
                        <input
                            type="text"
                            placeholder="Search feedback..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full rounded-lg border border-line bg-surface py-2 pr-4 pl-9 text-sm transition-all outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div className="flex items-center gap-3">
                        <Link
                            href={route('feedback.create')}
                            className="flex items-center gap-2 rounded-lg bg-accent px-4 py-2 text-sm font-bold text-ink-inverse shadow-surface transition-all hover:bg-accent-hover"
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
                                className="flex h-full flex-col rounded-xl border border-line bg-surface p-6 transition-all hover:border-accent hover:shadow-surface"
                            >
                                <div className="mb-4 flex items-start justify-between">
                                    <div className="rounded-lg bg-accent-soft p-2.5 text-accent-ink">
                                        <ChatBubbleBottomCenterTextIcon className="h-6 w-6" />
                                    </div>
                                    <StatusBadge
                                        label={statusLabel(feedback.status)}
                                        color={statusColor(feedback.status)}
                                    />
                                </div>
                                <div className="mb-4 flex-grow">
                                    <h3 className="mb-1 text-lg font-bold text-ink">
                                        {feedback.subject}
                                    </h3>
                                    <div className="mb-4 flex items-center gap-1.5 text-xs font-medium text-ink-subtle uppercase">
                                        <TagIcon className="h-3 w-3" />{' '}
                                        {feedback.category}
                                    </div>
                                    <p className="line-clamp-2 text-sm leading-relaxed text-ink-subtle">
                                        {feedback.message}
                                    </p>
                                </div>
                                <div className="flex items-center justify-between border-t border-line pt-4">
                                    <span className="text-[10px] font-bold text-ink-subtle">
                                        {new Date(
                                            feedback.created_at,
                                        ).toLocaleDateString()}
                                    </span>
                                    <button
                                        onClick={() =>
                                            handleViewDetails(feedback)
                                        }
                                        className="flex items-center gap-1.5 rounded-lg bg-accent-soft px-3 py-1.5 text-xs font-bold text-accent-ink"
                                    >
                                        View Details{' '}
                                        <ArrowRightIcon className="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHeaderCell>
                                    Feedback details
                                </TableHeaderCell>
                                <TableHeaderCell>Category</TableHeaderCell>
                                <TableHeaderCell numeric>
                                    Status
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    Submitted
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    <span className="sr-only">Actions</span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filteredFeedback.map((feedback) => (
                                <TableRow key={feedback.id}>
                                    <TableCell>
                                        <div className="text-[13px] font-semibold text-ink">
                                            {feedback.subject}
                                        </div>
                                        <div className="line-clamp-1 text-xs text-ink-subtle">
                                            {feedback.message}
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded bg-surface-inset px-2 py-1 text-xs font-semibold text-ink-muted capitalize">
                                            {feedback.category.replace(
                                                '-',
                                                ' ',
                                            )}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        <StatusBadge
                                            label={statusLabel(feedback.status)}
                                            color={statusColor(feedback.status)}
                                        />
                                    </TableCell>
                                    <TableCell
                                        numeric
                                        muted
                                        className="text-[12px]"
                                    >
                                        {new Date(
                                            feedback.created_at,
                                        ).toLocaleDateString()}
                                    </TableCell>
                                    <TableCell numeric>
                                        <button
                                            onClick={() =>
                                                handleViewDetails(feedback)
                                            }
                                            aria-label={
                                                'View details for ' +
                                                feedback.subject
                                            }
                                            className="p-1.5 text-ink-subtle transition-colors duration-150 ease-premium hover:text-accent"
                                        >
                                            <ArrowRightIcon className="h-4 w-4" />
                                        </button>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
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
                                <h3 className="text-xl font-bold text-ink">
                                    {selectedFeedback.subject}
                                </h3>
                                <div className="mt-1 flex items-center gap-3">
                                    <span className="flex items-center gap-1 text-[10px] font-black tracking-widest text-accent-ink uppercase">
                                        <TagIcon className="h-3 w-3" />{' '}
                                        {selectedFeedback.category.replace(
                                            '-',
                                            ' ',
                                        )}
                                    </span>
                                    <span className="text-[10px] font-bold text-ink-subtle">
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
                            <h4 className="text-xs font-black tracking-widest text-ink-subtle uppercase">
                                Your Message
                            </h4>
                            <div className="rounded-2xl border border-line bg-surface-muted p-5 text-sm leading-relaxed text-ink-muted">
                                {selectedFeedback.message}
                            </div>
                        </div>

                        <div className="space-y-3">
                            <h4 className="flex items-center gap-2 text-xs font-black tracking-widest text-ink-subtle uppercase">
                                <ArrowPathIcon className="h-4 w-4" /> System
                                Response
                            </h4>
                            {selectedFeedback.response ? (
                                <div className="flex gap-4">
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent font-bold text-ink-inverse">
                                        {selectedFeedback.responder?.name.charAt(
                                            0,
                                        )}
                                    </div>
                                    <div className="flex-grow rounded-2xl rounded-tl-none border border-accent/25 bg-accent-soft/50 p-5">
                                        <p className="mb-4 text-sm leading-relaxed text-ink">
                                            {selectedFeedback.response}
                                        </p>
                                        <div className="flex items-center justify-between border-t border-accent/25 pt-3">
                                            <span className="text-[10px] font-bold text-accent-ink">
                                                Assigned:{' '}
                                                {
                                                    selectedFeedback.responder
                                                        ?.name
                                                }
                                            </span>
                                            <span className="text-[10px] font-medium text-ink-subtle">
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
                                    <p className="text-xs text-warning-ink">
                                        Our team will respond shortly.
                                    </p>
                                </div>
                            )}
                        </div>

                        <div className="flex justify-end pt-4">
                            <button
                                onClick={() => setShowModal(false)}
                                className="rounded-xl bg-surface-inset px-6 py-2 text-xs font-bold transition-colors hover:bg-surface-inset"
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
