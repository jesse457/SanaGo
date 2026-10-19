import {
    ArrowDownTrayIcon,
    EnvelopeIcon,
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import {
    Badge,
    Button,
    EmptyState,
    PageHeader,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { DeleteUserDialog } from './components/DeleteUserDialog';
import type { DeletableUser } from './components/DeleteUserDialog';

interface UserItem {
    id: number;
    name: string;
    email: string;
    roles: string[];
    is_active: boolean;
    created_at: string;
}

function initials(name: string): string {
    return name
        .split(' ')
        .filter(Boolean)
        .map((part) => part.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

export default function LandlordUsersIndex({ users }: { users: UserItem[] }) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [delModal, setDelModal] = useState<DeletableUser | null>(null);

    const filtered = useMemo(
        () =>
            users.filter(
                (user) =>
                    user.name.toLowerCase().includes(search.toLowerCase()) ||
                    user.email.toLowerCase().includes(search.toLowerCase()),
            ),
        [users, search],
    );

    const activeCount = useMemo(
        () => users.filter((user) => user.is_active).length,
        [users],
    );

    return (
        <SuperAdminLayout
            headerTitle="Staff Users"
            headerSubtitle="Manage landlord organization members"
        >
            <Head title="Staff Users" />

            <div className="space-y-5">
                <PageHeader
                    eyebrow="Organization"
                    title="Staff users"
                    description="Everyone with a landlord-level account, and the role that scopes what they can reach."
                    icon={UserGroupIcon}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/' },
                        { label: 'Staff users' },
                    ]}
                    actions={
                        <Button href="/landlord-users/create" icon={PlusIcon}>
                            Add user
                        </Button>
                    }
                    meta={
                        <>
                            <span className="tabular-nums">
                                {users.length} total
                            </span>
                            <span className="tabular-nums">
                                {activeCount} active
                            </span>
                            <span className="tabular-nums">
                                {users.length - activeCount} inactive
                            </span>
                        </>
                    }
                />

                <StatGrid columns={3}>
                    <StatCard
                        label="Total users"
                        value={users.length}
                        icon={UserGroupIcon}
                        tone="accent"
                    />
                    <StatCard
                        label="Active"
                        value={activeCount}
                        icon={UserGroupIcon}
                        tone="positive"
                    />
                    <StatCard
                        label="Inactive"
                        value={users.length - activeCount}
                        icon={UserGroupIcon}
                        tone={
                            users.length - activeCount > 0
                                ? 'warning'
                                : 'neutral'
                        }
                    />
                </StatGrid>

                <Toolbar>
                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder="Search by name or email…"
                    />
                    <ToolbarSpacer />
                    <Badge tone="muted" variant="outline">
                        {filtered.length} shown
                    </Badge>
                    <Button
                        variant="secondary"
                        size="sm"
                        icon={ArrowDownTrayIcon}
                        disabled
                    >
                        Export
                    </Button>
                </Toolbar>

                {filtered.length > 0 ? (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>User</TableHeaderCell>
                                <TableHeaderCell>Roles</TableHeaderCell>
                                <TableHeaderCell>Status</TableHeaderCell>
                                <TableHeaderCell>Added</TableHeaderCell>
                                <TableHeaderCell numeric>
                                    Actions
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filtered.map((user) => (
                                <TableRow key={user.id}>
                                    <TableCell>
                                        <div className="flex min-w-0 items-center gap-3">
                                            <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-accent-soft text-[11px] font-semibold tracking-wide text-accent-ink">
                                                {initials(user.name)}
                                            </span>
                                            <div className="min-w-0">
                                                <p className="truncate text-[13px] font-semibold tracking-tight text-ink">
                                                    {user.name}
                                                </p>
                                                <p className="mt-0.5 flex items-center gap-1 truncate text-[12px] text-ink-subtle">
                                                    <EnvelopeIcon className="h-3 w-3 shrink-0" />
                                                    {user.email}
                                                </p>
                                            </div>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex flex-wrap gap-1">
                                            {(user.roles || []).length > 0 ? (
                                                user.roles.map((role) => (
                                                    <Badge
                                                        key={role}
                                                        tone="accent"
                                                        variant="soft"
                                                    >
                                                        {role.replace(
                                                            /-/g,
                                                            ' ',
                                                        )}
                                                    </Badge>
                                                ))
                                            ) : (
                                                <span className="text-[12px] text-ink-subtle">
                                                    No role
                                                </span>
                                            )}
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            tone={
                                                user.is_active
                                                    ? 'positive'
                                                    : 'critical'
                                            }
                                            dot
                                        >
                                            {user.is_active
                                                ? 'Active'
                                                : 'Inactive'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell muted>
                                        <span className="text-[12.5px]">
                                            {user.created_at
                                                ? new Date(
                                                      user.created_at,
                                                  ).toLocaleDateString()
                                                : '—'}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                href={`/landlord-users/${user.id}/edit`}
                                                aria-label={`Edit ${user.name}`}
                                            >
                                                <PencilSquareIcon className="h-[18px] w-[18px]" />
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onClick={() =>
                                                    setDelModal(user)
                                                }
                                                aria-label={`Delete ${user.name}`}
                                                className="text-critical-ink hover:bg-critical-soft"
                                            >
                                                <TrashIcon className="h-[18px] w-[18px]" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                ) : (
                    <EmptyState
                        icon={UserGroupIcon}
                        title={
                            search
                                ? 'No staff users match that search'
                                : 'No staff users yet'
                        }
                        description={
                            search
                                ? 'Try a different name or email address.'
                                : 'Add the first landlord staff account to get started.'
                        }
                        action={
                            search
                                ? undefined
                                : {
                                      label: 'Add user',
                                      href: '/landlord-users/create',
                                  }
                        }
                    />
                )}
            </div>

            <DeleteUserDialog
                user={delModal}
                onClose={() => setDelModal(null)}
            />
        </SuperAdminLayout>
    );
}
