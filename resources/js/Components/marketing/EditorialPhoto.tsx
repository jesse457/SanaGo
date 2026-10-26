import { cn } from '@/lib/utils';

export type PhotoRatio =
    | 'portrait'
    | 'tall'
    | 'square'
    | 'landscape'
    | 'wide'
    | 'cinema';

const ratios: Record<PhotoRatio, string> = {
    portrait: 'aspect-[4/5]',
    tall: 'aspect-[3/4]',
    square: 'aspect-square',
    landscape: 'aspect-[4/3]',
    wide: 'aspect-[16/10]',
    /** Thin editorial band — fills a wide cell without towering over it. */
    cinema: 'aspect-[21/8]',
};

export interface EditorialPhotoProps {
    src: string;
    alt: string;
    /** Short label printed on the frame — a room, a shift, a study. */
    caption?: string;
    ratio?: PhotoRatio;
    /** Load immediately: only the image above the fold should opt in. */
    priority?: boolean;
    className?: string;
    imageClassName?: string;
}

/**
 * EditorialPhoto — the public site's photographic frame.
 *
 * One geometry, one hairline, one caption tab. Photography on the site is
 * evidence of the work, not decoration: each frame names what it shows, is
 * cropped to a fixed ratio so rows stay level, and never sits on a shadow.
 */
export function EditorialPhoto({
    src,
    alt,
    caption,
    ratio = 'landscape',
    priority = false,
    className,
    imageClassName,
}: EditorialPhotoProps) {
    return (
        <figure
            className={cn(
                'group relative overflow-hidden rounded-xl border border-line bg-surface-inset',
                ratios[ratio],
                className,
            )}
        >
            <img
                src={src}
                alt={alt}
                loading={priority ? 'eager' : 'lazy'}
                decoding={priority ? 'sync' : 'async'}
                className={cn(
                    'h-full w-full object-cover transition-transform duration-[900ms] ease-premium group-hover:scale-[1.03]',
                    imageClassName,
                )}
            />
            {/* Hairline inner edge: keeps the frame crisp where a photo is pale. */}
            <span
                aria-hidden
                className="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-ink/5 ring-inset"
            />
            {caption && (
                <figcaption className="absolute bottom-3 left-3 max-w-[85%] rounded-md border border-line/60 bg-canvas/88 px-2 py-1 font-mono text-[9.5px] font-medium tracking-[0.16em] text-ink-muted uppercase backdrop-blur-[3px]">
                    {caption}
                </figcaption>
            )}
        </figure>
    );
}

/**
 * PhotoNote — the credit/licence line that must accompany press photography.
 */
export function PhotoNote({
    children,
    className,
}: {
    children: React.ReactNode;
    className?: string;
}) {
    return (
        <p
            className={cn(
                'mt-3 font-mono text-[9.5px] tracking-[0.14em] text-ink-subtle uppercase',
                className,
            )}
        >
            {children}
        </p>
    );
}
