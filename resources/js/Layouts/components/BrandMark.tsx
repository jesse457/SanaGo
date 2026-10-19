import { Link } from '@inertiajs/react';
import type { ComponentType, SVGProps } from 'react';
import { cn } from '@/lib/utils';

export type BrandMarkSize = 'sm' | 'md';

export interface BrandMarkProps {
    icon: ComponentType<SVGProps<SVGSVGElement>>;
    /** Eyebrow under the wordmark; omit for the bare mark. */
    caption?: string;
    href?: string;
    size?: BrandMarkSize;
    /** Collapsed rail: render the glyph only. */
    compact?: boolean;
    className?: string;
}

const glyphSizeClasses: Record<BrandMarkSize, string> = {
    sm: 'h-8 w-8 rounded-lg',
    md: 'h-9 w-9 rounded-[10px]',
};

const iconSizeClasses: Record<BrandMarkSize, string> = {
    sm: 'h-4 w-4',
    md: 'h-[18px] w-[18px]',
};

/**
 * BrandMark — the shared "glyph + SanaGo wordmark" block used by every shell.
 * Ink-solid glyph rather than a gradient badge; the accent is left for state.
 */
export function BrandMark({
    icon: Icon,
    caption,
    href,
    size = 'sm',
    compact = false,
    className,
}: BrandMarkProps) {
    const content = (
        <>
            <span
                className={cn(
                    'flex shrink-0 items-center justify-center bg-ink text-ink-inverse',
                    glyphSizeClasses[size],
                )}
            >
                <Icon className={iconSizeClasses[size]} />
            </span>
            {!compact && (
                <span className="min-w-0 overflow-hidden">
                    <span className="block truncate text-[14px] leading-tight font-semibold tracking-tight whitespace-nowrap text-ink">
                        SanaGo
                    </span>
                    {caption && (
                        <span className="mt-0.5 block truncate text-[9.5px] leading-none font-medium tracking-[0.18em] text-ink-subtle uppercase">
                            {caption}
                        </span>
                    )}
                </span>
            )}
        </>
    );

    const base = cn(
        'group flex min-w-0 items-center gap-2.5 rounded-md transition-opacity duration-200 ease-premium',
        className,
    );

    if (!href) {
        return <div className={base}>{content}</div>;
    }

    return (
        <Link href={href} className={cn(base, 'hover:opacity-80')}>
            {content}
        </Link>
    );
}
