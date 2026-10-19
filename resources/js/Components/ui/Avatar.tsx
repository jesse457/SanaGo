import { cn } from '@/lib/utils';

export interface AvatarProps {
    src?: string | null;
    name?: string | null;
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    /** Rounded square (default) reads less templated than a perfect circle. */
    shape?: 'squircle' | 'circle';
    className?: string;
}

const sizeClasses = {
    xs: 'h-6 w-6 text-[10px] rounded-[7px]',
    sm: 'h-8 w-8 text-[11px] rounded-[9px]',
    md: 'h-10 w-10 text-[13px] rounded-[11px]',
    lg: 'h-12 w-12 text-[15px] rounded-[13px]',
    xl: 'h-16 w-16 text-[19px] rounded-[17px]',
};

const toneByHash = [
    'bg-accent-soft text-accent-ink',
    'bg-positive-soft text-positive-ink',
    'bg-warning-soft text-warning-ink',
    'bg-surface-inset text-ink-muted',
];

function initials(name?: string | null): string {
    if (!name) {
        return '—';
    }

    const parts = name.trim().split(/\s+/).filter(Boolean);

    if (parts.length === 0) {
        return '—';
    }

    if (parts.length === 1) {
        return parts[0].slice(0, 2).toUpperCase();
    }

    return `${parts[0][0]}${parts[parts.length - 1][0]}`.toUpperCase();
}

function hashTone(name?: string | null): string {
    const key = name ?? '';
    let sum = 0;

    for (let index = 0; index < key.length; index += 1) {
        sum += key.charCodeAt(index);
    }

    return toneByHash[sum % toneByHash.length];
}

/** Avatar — squircle identity chip with deterministic initials fallback. */
export function Avatar({
    src,
    name,
    size = 'md',
    shape = 'squircle',
    className,
}: AvatarProps) {
    return (
        <span
            className={cn(
                'inline-flex shrink-0 items-center justify-center overflow-hidden border border-line font-semibold tracking-tight select-none',
                sizeClasses[size],
                shape === 'circle' && 'rounded-full',
                !src && hashTone(name),
                className,
            )}
            title={name ?? undefined}
        >
            {src ? (
                <img
                    src={src}
                    alt={name ? `Portrait of ${name}` : ''}
                    className="h-full w-full object-cover"
                    loading="lazy"
                />
            ) : (
                <span className="tabular-nums">{initials(name)}</span>
            )}
        </span>
    );
}
