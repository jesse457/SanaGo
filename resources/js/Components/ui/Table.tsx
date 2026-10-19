import type {
    HTMLAttributes,
    ReactNode,
    TdHTMLAttributes,
    ThHTMLAttributes,
} from 'react';
import { cn } from '@/lib/utils';

interface TableProps extends HTMLAttributes<HTMLTableElement> {
    /** Skip the bordered surface wrapper when the table sits inside a Card. */
    bare?: boolean;
    /** Wrap even a `bare` table in a horizontal scroll container. */
    scroll?: boolean;
    containerClassName?: string;
    children: ReactNode;
}

/**
 * Table — the one data table for the app: hairline rows, tabular figures,
 * quiet header labels, row hover. Compose with the parts below.
 */
export function Table({
    bare = false,
    scroll = false,
    containerClassName,
    className,
    children,
    ...props
}: TableProps) {
    const table = (
        <table
            className={cn(
                'w-full border-collapse text-left text-[13px]',
                className,
            )}
            {...props}
        >
            {children}
        </table>
    );

    if (bare) {
        return scroll ? (
            <div className={cn('overflow-x-auto', containerClassName)}>
                {table}
            </div>
        ) : (
            table
        );
    }

    return (
        <div
            className={cn(
                'overflow-hidden rounded-xl border border-line bg-surface',
                containerClassName,
            )}
        >
            <div className="overflow-x-auto">{table}</div>
        </div>
    );
}

export function TableHeader({
    className,
    children,
    ...props
}: HTMLAttributes<HTMLTableSectionElement>) {
    return (
        <thead className={cn('bg-surface-muted', className)} {...props}>
            {children}
        </thead>
    );
}

export function TableBody({
    className,
    children,
    ...props
}: HTMLAttributes<HTMLTableSectionElement>) {
    return (
        <tbody className={cn('divide-y divide-line', className)} {...props}>
            {children}
        </tbody>
    );
}

export function TableRow({
    className,
    children,
    ...props
}: HTMLAttributes<HTMLTableRowElement>) {
    return (
        <tr
            className={cn(
                'transition-colors duration-150 ease-premium hover:bg-surface-hover',
                className,
            )}
            {...props}
        >
            {children}
        </tr>
    );
}

type CellAlign = 'left' | 'center' | 'right';

interface TableHeaderCellProps extends ThHTMLAttributes<HTMLTableCellElement> {
    numeric?: boolean;
    align?: CellAlign;
}

const alignClasses: Record<CellAlign, string> = {
    left: 'text-left',
    center: 'text-center',
    right: 'text-right',
};

export function TableHeaderCell({
    className,
    numeric = false,
    children,
    ...props
}: TableHeaderCellProps) {
    return (
        <th
            scope="col"
            className={cn(
                'border-b border-line px-4 py-2.5 text-[10px] font-semibold tracking-[0.11em] whitespace-nowrap text-ink-subtle uppercase',
                numeric ? 'text-right' : 'text-left',
                className,
            )}
            {...props}
        >
            {children}
        </th>
    );
}

interface TableCellProps extends TdHTMLAttributes<HTMLTableCellElement> {
    numeric?: boolean;
    muted?: boolean;
    align?: CellAlign;
}

export function TableCell({
    className,
    numeric = false,
    muted = false,
    align,
    children,
    ...props
}: TableCellProps) {
    return (
        <td
            className={cn(
                'px-4 py-3 align-middle',
                align ? alignClasses[align] : numeric && 'text-right',
                numeric && 'tabular-nums',
                muted ? 'text-ink-muted' : 'text-ink',
                className,
            )}
            {...props}
        >
            {children}
        </td>
    );
}

export function TableCaption({
    className,
    children,
    ...props
}: HTMLAttributes<HTMLTableCaptionElement>) {
    return (
        <caption
            className={cn(
                'px-4 py-3 text-left text-[12px] text-ink-subtle',
                className,
            )}
            {...props}
        >
            {children}
        </caption>
    );
}
