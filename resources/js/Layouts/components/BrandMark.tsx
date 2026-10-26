import type { ReactNode } from 'react';
import { SanaGoLockup } from '@/Components/brand/SanaGoLogo';

export type BrandMarkSize = 'sm' | 'md';

export interface BrandMarkProps {
    /** Eyebrow under the wordmark; omit for the bare lockup. */
    caption?: ReactNode;
    href?: string;
    size?: BrandMarkSize;
    /** Collapsed rail: render the glyph only. */
    compact?: boolean;
    className?: string;
}

/**
 * BrandMark — the shell-facing name for the SanaGo lockup.
 *
 * The identity itself (glyph geometry, the tile pairing, the "Sana" / "Go"
 * colour split) lives in `@/Components/brand/SanaGoLogo`, so the mark in the
 * header, the sidebar, the footer and the favicon can never drift apart. This
 * wrapper exists so layouts keep importing the shell-level vocabulary they
 * already speak.
 */
export function BrandMark({
    caption,
    href,
    size = 'sm',
    compact = false,
    className,
}: BrandMarkProps) {
    return (
        <SanaGoLockup
            size={size}
            caption={caption}
            href={href}
            compact={compact}
            className={className}
        />
    );
}
