import { Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import type { ReactNode } from 'react';
import {
    Badge,
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
import { priorityTone, statusLabel, statusTone } from './ticketStatus';

export interface TicketRow {
    id: number;
    subject: string;
    status: string;
    priority: string;
    created_at: string;
    tenant_name?: string | null;
    user_name?: string | null;
}

interface TicketTableProps {
    tickets: TicketRow[];
    /** Builds the detail URL for a row. */
    href: (ticket: TicketRow) => string;
    /** Show the tenant column (super-admin screens). */
    showTenant?: boolean;
}

/**
 * TicketTable — the ticket list used by both ticket indexes. Search filters
 * subject, tenant and requester locally; everything else is a plain table.
 */
export function TicketTable({
    tickets,
    href,
    showTenant = false,
}: TicketTableProps) {
    const [search, setSearch] = useState('');

    const filtered = useMemo(() => {
        const term = search.trim().toLowerCase();

        if (term === '') {
            return tickets;
        }

        return tickets.filter((ticket) =>
            [ticket.subject, ticket.tenant_name, ticket.user_name]
                .filter(Boolean)
                .some((field) => String(field).toLowerCase().includes(term)),
        );
    }, [tickets, search]);

    return (
        <>
            <Toolbar>
                <ToolbarSearch
                    value={search}
                    onChange={setSearch}
                    placeholder="Search tickets…"
                />
                <ToolbarSpacer />
                <span className="text-[12px] text-ink-subtle tabular-nums">
                    {filtered.length} of {tickets.length}
                </span>
            </Toolbar>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHeaderCell>Subject</TableHeaderCell>
                        {showTenant && (
                            <TableHeaderCell>Tenant</TableHeaderCell>
                        )}
                        <TableHeaderCell>Priority</TableHeaderCell>
                        <TableHeaderCell>Status</TableHeaderCell>
                        <TableHeaderCell numeric>Opened</TableHeaderCell>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {filtered.map((ticket) => (
                        <TableRow key={ticket.id}>
                            <TableCell>
                                <Link
                                    href={href(ticket)}
                                    className="font-semibold text-ink transition-colors duration-150 hover:text-accent-ink"
                                >
                                    {ticket.subject}
                                </Link>
                                <p className="mt-0.5 text-[12px] text-ink-subtle">
                                    #{ticket.id}
                                    {ticket.user_name
                                        ? ` · ${ticket.user_name}`
                                        : ''}
                                </p>
                            </TableCell>
                            {showTenant && (
                                <TableCell muted>
                                    {ticket.tenant_name || 'No tenant'}
                                </TableCell>
                            )}
                            <TableCell>
                                <Badge tone={priorityTone(ticket.priority)}>
                                    {ticket.priority}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Badge tone={statusTone(ticket.status)} dot>
                                    {statusLabel(ticket.status)}
                                </Badge>
                            </TableCell>
                            <TableCell numeric muted>
                                {new Date(
                                    ticket.created_at,
                                ).toLocaleDateString()}
                            </TableCell>
                        </TableRow>
                    ))}
                    {filtered.length === 0 && (
                        <TableRow>
                            <TableCell
                                colSpan={showTenant ? 5 : 4}
                                className="py-10 text-center text-ink-subtle"
                            >
                                No tickets match “{search}”.
                            </TableCell>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        </>
    );
}

/** Small meta row for a detail header: status, priority and opened date. */
export function TicketStatusMeta({
    status,
    priority,
    createdAt,
}: {
    status: string;
    priority: string;
    createdAt: string;
}): ReactNode {
    return (
        <>
            <Badge tone={statusTone(status)} dot>
                {statusLabel(status)}
            </Badge>
            <Badge tone={priorityTone(priority)}>{priority}</Badge>
            <span>Opened {new Date(createdAt).toLocaleDateString()}</span>
        </>
    );
}
