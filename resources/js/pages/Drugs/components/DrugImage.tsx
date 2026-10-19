import { cn } from '@/lib/utils';

const sizeClasses = {
    sm: 'h-10 w-10 rounded-lg text-sm',
    md: 'h-12 w-12 rounded-lg text-[15px]',
};

interface DrugImageProps {
    src?: string | null;
    name: string;
    size?: keyof typeof sizeClasses;
    className?: string;
}

/**
 * DrugImage — the medication thumbnail with a deterministic initial fallback so
 * inventory rows and cards keep the same silhouette whether a photo exists.
 */
export function DrugImage({
    src,
    name,
    size = 'md',
    className,
}: DrugImageProps) {
    return (
        <span
            className={cn(
                'flex shrink-0 items-center justify-center overflow-hidden border border-line bg-accent-soft font-semibold tracking-tight text-accent-ink',
                sizeClasses[size],
                className,
            )}
        >
            {src ? (
                <img
                    src={src}
                    alt=""
                    loading="lazy"
                    className="h-full w-full object-cover"
                />
            ) : (
                name.charAt(0).toUpperCase()
            )}
        </span>
    );
}
