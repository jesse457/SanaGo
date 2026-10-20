import {
    MegaphoneIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import {
    Alert,
    Button,
    Card,
    Dialog,
    EmptyState,
    PageHeader,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import {
    AnnouncementStatusBadge,
    AnnouncementTypeBadge,
} from '@/pages/Announcements/components/AnnouncementBadges';

interface Announcement {
    id: number;
    title: string;
    body: string;
    type: string;
    is_published: boolean;
    published_at: string | null;
    expires_at: string | null;
    created_at: string;
}

export default function AnnouncementsIndex({
    announcements,
}: {
    announcements: Announcement[];
}) {
    const { t } = useTranslation();
    const [delModal, setDelModal] = useState<Announcement | null>(null);
    const { delete: destroy, processing } = useForm();

    const handleDelete = () => {
        if (delModal) {
            destroy(`/announcements/${delModal.id}`, {
                onSuccess: () => setDelModal(null),
            });
        }
    };

    return (
        <SuperAdminLayout
            headerTitle="Announcements"
            headerSubtitle="System-wide announcements for all tenants"
        >
            <Head title="Announcements" />

            <div className="space-y-6 pb-12">
                <PageHeader
                    title="Announcements"
                    description="System-wide messages shown to every tenant on their dashboard."
                    icon={MegaphoneIcon}
                    breadcrumbs={[{ label: 'Announcements' }]}
                    actions={
                        <Button href="/announcements/create" icon={PlusIcon}>
                            New announcement
                        </Button>
                    }
                />

                {announcements.length === 0 ? (
                    <EmptyState
                        icon={MegaphoneIcon}
                        title="No announcements yet"
                        description="Get started by creating your first system-wide announcement."
                        action={{
                            label: 'New announcement',
                            href: '/announcements/create',
                        }}
                    />
                ) : (
                    <div className="flex flex-col gap-3">
                        {announcements.map((announcement) => (
                            <Card
                                key={announcement.id}
                                className="group p-5 transition-colors duration-200 ease-premium hover:border-line-strong"
                            >
                                <div className="flex items-start gap-4">
                                    <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-accent">
                                        <MegaphoneIcon className="h-[18px] w-[18px]" />
                                    </span>

                                    <div className="min-w-0 flex-1">
                                        <div className="flex flex-wrap items-center gap-2">
                                            <h3 className="text-[15px] font-semibold tracking-tight text-ink">
                                                {announcement.title}
                                            </h3>
                                            <AnnouncementTypeBadge
                                                type={announcement.type}
                                            />
                                            <AnnouncementStatusBadge
                                                isPublished={
                                                    announcement.is_published
                                                }
                                            />
                                        </div>

                                        <p className="mt-2 line-clamp-2 text-[13px] leading-relaxed text-ink-muted">
                                            {announcement.body}
                                        </p>

                                        <div className="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1 text-[12px] text-ink-subtle">
                                            {announcement.published_at && (
                                                <span className="tabular-nums">
                                                    Published{' '}
                                                    {new Date(
                                                        announcement.published_at,
                                                    ).toLocaleString()}
                                                </span>
                                            )}
                                            {announcement.expires_at && (
                                                <span className="tabular-nums">
                                                    Expires{' '}
                                                    {new Date(
                                                        announcement.expires_at,
                                                    ).toLocaleString()}
                                                </span>
                                            )}
                                        </div>
                                    </div>

                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        icon={TrashIcon}
                                        aria-label={`Delete ${announcement.title}`}
                                        onClick={() =>
                                            setDelModal(announcement)
                                        }
                                        className="shrink-0 opacity-0 group-hover:opacity-100 focus-visible:opacity-100"
                                    />
                                </div>
                            </Card>
                        ))}
                    </div>
                )}
            </div>

            <Dialog
                open={delModal !== null}
                onClose={() => setDelModal(null)}
                title="Delete announcement"
                description="This cannot be undone."
                size="sm"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setDelModal(null)}
                            disabled={processing}
                        >
                            {t('common.cancel')}
                        </Button>
                        <Button
                            variant="danger"
                            loading={processing}
                            onClick={handleDelete}
                        >
                            {processing ? 'Deleting…' : 'Delete'}
                        </Button>
                    </>
                }
            >
                <Alert tone="critical" title="Permanent removal">
                    Deleting <strong>{delModal?.title}</strong> removes it for
                    every tenant immediately.
                </Alert>
            </Dialog>
        </SuperAdminLayout>
    );
}
