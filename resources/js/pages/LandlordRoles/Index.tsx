import {
    KeyIcon,
    PencilSquareIcon,
    PlusIcon,
    ShieldCheckIcon,
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
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { DeleteRoleDialog } from './components/DeleteRoleDialog';
import type { DeletableRole } from './components/DeleteRoleDialog';

interface Role {
    id: number;
    name: string;
    permissions: string[];
    users_count: number;
}

export default function LandlordRolesIndex({ roles }: { roles: Role[] }) {
    const [search, setSearch] = useState('');
    const [delModal, setDelModal] = useState<DeletableRole | null>(null);

    const filtered = useMemo(
        () =>
            roles.filter((role) =>
                role.name.toLowerCase().includes(search.toLowerCase()),
            ),
        [roles, search],
    );

    const assignedUsers = useMemo(
        () => roles.reduce((total, role) => total + role.users_count, 0),
        [roles],
    );

    const permissionCount = useMemo(
        () =>
            roles.reduce(
                (total, role) => total + (role.permissions?.length ?? 0),
                0,
            ),
        [roles],
    );

    return (
        <SuperAdminLayout
            headerTitle="Roles & Permissions"
            headerSubtitle="Manage landlord organization roles"
        >
            <Head title="Roles & Permissions" />

            <div className="space-y-5">
                <PageHeader
                    eyebrow="Access control"
                    title="Roles & permissions"
                    description="Each role is a named bundle of permissions granted to landlord staff accounts."
                    icon={ShieldCheckIcon}
                    breadcrumbs={[{ label: 'Roles & permissions' }]}
                    actions={
                        <Button href="/landlord-roles/create" icon={PlusIcon}>
                            Create role
                        </Button>
                    }
                    meta={
                        <>
                            <span className="tabular-nums">
                                {roles.length} roles
                            </span>
                            <span className="tabular-nums">
                                {assignedUsers} assignments
                            </span>
                        </>
                    }
                />

                <StatGrid columns={3}>
                    <StatCard
                        label="Roles"
                        value={roles.length}
                        icon={ShieldCheckIcon}
                        tone="accent"
                    />
                    <StatCard
                        label="Staff assigned"
                        value={assignedUsers}
                        icon={UserGroupIcon}
                        tone="neutral"
                    />
                    <StatCard
                        label="Permissions granted"
                        value={permissionCount}
                        icon={KeyIcon}
                        tone="positive"
                        hint="Total grants across all roles"
                    />
                </StatGrid>

                <Toolbar>
                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder="Search roles…"
                    />
                    <ToolbarSpacer />
                    <Badge tone="muted" variant="outline">
                        {filtered.length} shown
                    </Badge>
                </Toolbar>

                {filtered.length > 0 ? (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>Role</TableHeaderCell>
                                <TableHeaderCell>Permissions</TableHeaderCell>
                                <TableHeaderCell numeric>Users</TableHeaderCell>
                                <TableHeaderCell numeric>
                                    Actions
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filtered.map((role) => (
                                <TableRow key={role.id}>
                                    <TableCell>
                                        <div className="flex min-w-0 items-center gap-2.5">
                                            <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-accent">
                                                <ShieldCheckIcon className="h-4 w-4" />
                                            </span>
                                            <span className="truncate text-[13px] font-semibold tracking-tight text-ink">
                                                {role.name}
                                            </span>
                                        </div>
                                    </TableCell>
                                    <TableCell muted>
                                        <div className="flex flex-wrap items-center gap-1">
                                            {(role.permissions ?? [])
                                                .slice(0, 3)
                                                .map((permission) => (
                                                    <Badge
                                                        key={permission}
                                                        tone="neutral"
                                                        variant="soft"
                                                    >
                                                        {permission.replace(
                                                            /-/g,
                                                            ' ',
                                                        )}
                                                    </Badge>
                                                ))}
                                            {(role.permissions?.length ?? 0) >
                                                3 && (
                                                <Badge
                                                    tone="muted"
                                                    variant="outline"
                                                >
                                                    +
                                                    {(role.permissions
                                                        ?.length ?? 0) - 3}{' '}
                                                    more
                                                </Badge>
                                            )}
                                            {(role.permissions?.length ?? 0) ===
                                                0 && (
                                                <span className="text-[12px] text-ink-subtle">
                                                    No permissions
                                                </span>
                                            )}
                                        </div>
                                    </TableCell>
                                    <TableCell numeric muted>
                                        <span className="inline-flex items-center gap-1.5">
                                            <UserGroupIcon className="h-3.5 w-3.5" />
                                            {role.users_count}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                href={`/landlord-roles/${role.id}/edit`}
                                                aria-label={`Edit ${role.name}`}
                                            >
                                                <PencilSquareIcon className="h-[18px] w-[18px]" />
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                onClick={() =>
                                                    setDelModal(role)
                                                }
                                                aria-label={`Delete ${role.name}`}
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
                        icon={ShieldCheckIcon}
                        title={
                            search
                                ? 'No roles match that search'
                                : 'No roles yet'
                        }
                        description={
                            search
                                ? 'Try a different role name.'
                                : 'Create a role to group permissions for your staff.'
                        }
                        action={
                            search
                                ? undefined
                                : {
                                      label: 'Create role',
                                      href: '/landlord-roles/create',
                                  }
                        }
                    />
                )}
            </div>

            <DeleteRoleDialog
                role={delModal}
                onClose={() => setDelModal(null)}
            />
        </SuperAdminLayout>
    );
}
