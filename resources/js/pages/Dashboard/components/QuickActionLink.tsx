import { Link } from '@inertiajs/react';
import type { ComponentType, SVGProps } from 'react';

export interface QuickActionLinkProps {
    href: string;
    label: string;
    icon: ComponentType<SVGProps<SVGSVGElement>>;
}

/**
 * QuickActionLink — compact icon + label navigation used in dashboard quick
 * action rows.
 */
export function QuickActionLink({
    href,
    label,
    icon: Icon,
}: QuickActionLinkProps) {
    return (
        <Link
            href={href}
            className="group flex items-center gap-2.5 rounded-md border border-line bg-surface px-3 py-2 text-[13px] font-medium text-ink transition-[border-color,background-color,transform] duration-200 ease-premium hover:border-line-strong hover:bg-surface-hover focus-visible:ring-2 focus-visible:ring-accent/45 focus-visible:outline-none active:scale-[0.985]"
        >
            <Icon className="h-4 w-4 shrink-0 text-accent transition-transform duration-200 ease-premium group-hover:-translate-y-px" />
            <span className="truncate">{label}</span>
        </Link>
    );
}
