import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/react';
import { EllipsisHorizontalIcon } from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';
import type { ComponentType, ReactNode, SVGProps } from 'react';
import { cn } from '@/lib/utils';

export interface DropdownItem {
    label: string;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    onSelect?: () => void;
    href?: string;
    method?: 'get' | 'post' | 'put' | 'patch' | 'delete';
    danger?: boolean;
    disabled?: boolean;
    separatorBefore?: boolean;
}

export interface DropdownMenuProps {
    items: DropdownItem[];
    /** Defaults to a quiet ellipsis trigger. */
    trigger?: ReactNode;
    label?: string;
    align?: 'left' | 'right';
    className?: string;
}

const itemClass =
    'flex w-full items-center gap-2 rounded-md px-2.5 py-1.5 text-left text-[13px] font-medium transition-colors duration-150 ease-premium focus:outline-none';

/**
 * DropdownMenu — row and header overflow actions. Keyboard navigable, closes on
 * select, escapes to dismiss.
 */
export function DropdownMenu({
    items,
    trigger,
    label = 'Open actions',
    align = 'right',
    className,
}: DropdownMenuProps) {
    return (
        <Menu as="div" className={cn('relative inline-block', className)}>
            <MenuButton
                aria-label={label}
                className="inline-flex h-8 w-8 items-center justify-center rounded-md text-ink-subtle transition-colors duration-150 ease-premium hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none data-[open]:bg-surface-hover data-[open]:text-ink"
            >
                {trigger ?? (
                    <EllipsisHorizontalIcon className="h-[18px] w-[18px]" />
                )}
            </MenuButton>

            <MenuItems
                transition
                anchor={align === 'right' ? 'bottom end' : 'bottom start'}
                className={cn(
                    'z-50 min-w-44 origin-top rounded-lg border border-line bg-surface p-1',
                    'shadow-float outline-none',
                    'transition duration-150 ease-premium data-[closed]:scale-95 data-[closed]:opacity-0',
                    align === 'right'
                        ? '[--anchor-gap:6px]'
                        : '[--anchor-gap:6px]',
                )}
            >
                {items.map((item, index) => {
                    const Icon = item.icon;
                    const content = (
                        <>
                            {Icon && (
                                <Icon className="h-4 w-4 shrink-0 opacity-80" />
                            )}
                            <span className="truncate">{item.label}</span>
                        </>
                    );

                    return (
                        <div
                            key={`${item.label}-${index}`}
                            className="contents"
                        >
                            {item.separatorBefore && (
                                <div className="my-1 h-px bg-line" />
                            )}
                            {item.onSelect || item.disabled ? (
                                <MenuItem disabled={item.disabled}>
                                    <button
                                        type="button"
                                        onClick={item.onSelect}
                                        className={cn(
                                            itemClass,
                                            'data-[disabled]:cursor-not-allowed data-[disabled]:opacity-45',
                                            item.danger
                                                ? 'text-critical-ink data-[focus]:bg-critical-soft'
                                                : 'text-ink-muted data-[focus]:bg-surface-hover data-[focus]:text-ink',
                                        )}
                                    >
                                        {content}
                                    </button>
                                </MenuItem>
                            ) : (
                                <MenuItem disabled={item.disabled}>
                                    <Link
                                        href={item.href ?? '#'}
                                        method={item.method}
                                        className={cn(
                                            itemClass,
                                            item.danger
                                                ? 'text-critical-ink data-[focus]:bg-critical-soft'
                                                : 'text-ink-muted data-[focus]:bg-surface-hover data-[focus]:text-ink',
                                        )}
                                    >
                                        {content}
                                    </Link>
                                </MenuItem>
                            )}
                        </div>
                    );
                })}
            </MenuItems>
        </Menu>
    );
}
