import {
    ArrowRightStartOnRectangleIcon,
    Cog6ToothIcon,
    UserCircleIcon,
} from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';
import { Avatar, Badge } from '@/Components/ui';
import { cn } from '@/lib/utils';

export interface NavUser {
    name?: string | null;
    email?: string | null;
}

export interface UserMenuPanelProps {
    user: NavUser | null;
    role: string;
    roleLabel: string;
    labels: {
        profile: string;
        profileDescription: string;
        settings: string;
        settingsDescription: string;
        signOut: string;
        signOutDescription: string;
    };
    onClose: () => void;
    onSignOut: () => void;
    /** Where the settings row points; defaults to the tenant settings page. */
    settingsHref?: string;
    /** Hide the settings row when the role cannot use that destination. */
    showSettings?: boolean;
    className?: string;
}

/**
 * UserMenuPanel — identity header plus profile/settings/sign-out rows. Anchored
 * by the caller, so it carries no positioning of its own beyond offsets.
 */
export function UserMenuPanel({
    user,
    roleLabel,
    labels,
    onClose,
    onSignOut,
    settingsHref = '/settings',
    showSettings = true,
    className,
}: UserMenuPanelProps) {
    const items = [
        {
            href: '/profile',
            icon: UserCircleIcon,
            label: labels.profile,
            description: labels.profileDescription,
        },
        ...(showSettings
            ? [
                  {
                      href: settingsHref,
                      icon: Cog6ToothIcon,
                      label: labels.settings,
                      description: labels.settingsDescription,
                  },
              ]
            : []),
    ];

    return (
        <div
            className={cn(
                'animate-fade-in absolute top-full right-0 mt-2 w-72 overflow-hidden rounded-lg border border-line bg-surface shadow-float',
                className,
            )}
        >
            <div className="flex items-center gap-3 border-b border-line p-3.5">
                <Avatar name={user?.name} size="md" />
                <div className="min-w-0 flex-1">
                    <p className="truncate text-[13px] font-semibold tracking-tight text-ink">
                        {user?.name}
                    </p>
                    <p className="mt-0.5 truncate text-[11.5px] text-ink-subtle">
                        {user?.email}
                    </p>
                </div>
                <Badge tone="accent" className="shrink-0">
                    {roleLabel}
                </Badge>
            </div>

            <div className="p-1">
                {items.map((item) => (
                    <Link
                        key={item.href}
                        href={item.href}
                        onClick={onClose}
                        className="group flex items-center gap-2.5 rounded-md px-2.5 py-2 transition-colors duration-150 ease-premium hover:bg-surface-hover focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                    >
                        <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-surface-inset text-ink-subtle transition-colors duration-150 group-hover:text-ink">
                            <item.icon className="h-4 w-4" />
                        </span>
                        <span className="min-w-0 flex-1">
                            <span className="block truncate text-[13px] font-medium text-ink">
                                {item.label}
                            </span>
                            <span className="block truncate text-[11px] text-ink-subtle">
                                {item.description}
                            </span>
                        </span>
                    </Link>
                ))}
            </div>

            <div className="border-t border-line p-1">
                <button
                    type="button"
                    onClick={() => {
                        onClose();
                        onSignOut();
                    }}
                    className="group flex w-full items-center gap-2.5 rounded-md px-2.5 py-2 text-left transition-colors duration-150 ease-premium hover:bg-critical-soft focus-visible:ring-2 focus-visible:ring-critical/40 focus-visible:outline-none"
                >
                    <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-critical-soft text-critical-ink">
                        <ArrowRightStartOnRectangleIcon className="h-4 w-4" />
                    </span>
                    <span className="min-w-0 flex-1">
                        <span className="block truncate text-[13px] font-medium text-critical-ink">
                            {labels.signOut}
                        </span>
                        <span className="block truncate text-[11px] text-ink-subtle">
                            {labels.signOutDescription}
                        </span>
                    </span>
                </button>
            </div>
        </div>
    );
}
