import { Link } from '@inertiajs/react';
import type {
    ComponentProps,
    ComponentType,
    HTMLAttributes,
    MouseEventHandler,
    ReactNode,
    SVGProps,
} from 'react';
import { cn } from '@/lib/utils';

export type IconType = ComponentType<SVGProps<SVGSVGElement>>;

export type ButtonVariant =
    | 'primary'
    | 'accent'
    | 'secondary'
    | 'outline'
    | 'ghost'
    | 'danger';

export type ButtonSize = 'sm' | 'md' | 'lg' | 'icon';

/**
 * Deliberately permissive: one geometry for buttons and Inertia links, so pages
 * can pass either DOM attributes or link attributes without a discriminated
 * union getting in the way.
 */
export type ButtonProps = Omit<
    HTMLAttributes<HTMLElement>,
    'children' | 'className' | 'color'
> & {
    variant?: ButtonVariant;
    size?: ButtonSize;
    icon?: IconType;
    trailingIcon?: IconType;
    loading?: boolean;
    fullWidth?: boolean;
    className?: string;
    children?: ReactNode;
    /** Renders an Inertia link with identical geometry. */
    href?: string;
    method?: 'get' | 'post' | 'put' | 'patch' | 'delete';
    as?: string;
    data?: Record<string, unknown>;
    preserveScroll?: boolean;
    preserveState?: boolean;
    only?: string[];
    replace?: boolean;
    type?: 'button' | 'submit' | 'reset';
    disabled?: boolean;
    name?: string;
    value?: string | number;
    form?: string;
    onClick?: MouseEventHandler<HTMLElement>;
} & Pick<ComponentProps<typeof Link>, 'prefetch' | 'cacheFor'>;

const base =
    'group relative inline-flex items-center justify-center gap-2 rounded-md font-semibold tracking-[-0.006em] whitespace-nowrap select-none ' +
    'transition-[background-color,border-color,color,box-shadow,transform] duration-200 ease-premium ' +
    'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/45 focus-visible:ring-offset-2 focus-visible:ring-offset-canvas ' +
    'active:scale-[0.98] active:duration-100 disabled:pointer-events-none disabled:opacity-45 ' +
    'motion-reduce:transform-none';

const variantClasses: Record<ButtonVariant, string> = {
    /* Primary CTAs are ink-solid: the one considered accent is the surface shift. */
    primary:
        'bg-ink text-ink-inverse shadow-surface hover:bg-ink/88 hover:shadow-raised active:bg-ink',
    accent: 'bg-accent text-ink-inverse shadow-surface hover:bg-accent-hover hover:shadow-raised active:bg-accent',
    secondary:
        'border border-line bg-surface text-ink shadow-surface hover:border-line-strong hover:bg-surface-hover active:bg-surface-inset',
    outline:
        'border border-line-strong bg-transparent text-ink hover:bg-surface-hover active:bg-surface-inset',
    ghost: 'bg-transparent text-ink-muted hover:bg-surface-hover hover:text-ink active:bg-surface-inset',
    danger:
        'bg-critical text-white shadow-surface hover:bg-critical/88 hover:shadow-raised active:bg-critical ' +
        'focus-visible:ring-critical/45',
};

const sizeClasses: Record<ButtonSize, string> = {
    sm: 'h-8 gap-1.5 px-3 text-[13px]',
    md: 'h-10 px-4 text-sm',
    lg: 'h-11 px-5 text-[15px]',
    icon: 'h-9 w-9 p-0',
};

const iconSizeClasses: Record<ButtonSize, string> = {
    sm: 'h-4 w-4',
    md: 'h-4 w-4',
    lg: 'h-[18px] w-[18px]',
    icon: 'h-[18px] w-[18px]',
};

function Spinner({ className }: { className?: string }) {
    return (
        <svg
            className={cn('animate-spin', className)}
            viewBox="0 0 24 24"
            fill="none"
            aria-hidden="true"
        >
            <circle
                cx="12"
                cy="12"
                r="9"
                stroke="currentColor"
                strokeWidth="2.25"
                strokeLinecap="round"
                strokeDasharray="42 58"
                opacity="0.9"
            />
        </svg>
    );
}

/**
 * The single button primitive for the whole app.
 *
 * `primary` is ink-solid (the editorial CTA), `accent` carries the app's one
 * considered color, everything else stays quiet. Pass `href` to render an
 * Inertia link with identical geometry.
 */
export function Button(props: ButtonProps) {
    const {
        variant = 'primary',
        size = 'md',
        icon: Icon,
        trailingIcon: Trailing,
        loading = false,
        fullWidth = false,
        className,
        children,
        ...rest
    } = props;

    const classes = cn(
        base,
        variantClasses[variant],
        sizeClasses[size],
        fullWidth && 'w-full',
        className,
    );

    const content = (
        <>
            {loading ? (
                <Spinner className={iconSizeClasses[size]} />
            ) : (
                Icon && (
                    <Icon
                        className={cn(
                            iconSizeClasses[size],
                            'shrink-0 opacity-90 transition-transform duration-200 ease-premium group-hover:-translate-y-px',
                        )}
                    />
                )
            )}
            {children}
            {Trailing && !loading && (
                <Trailing
                    className={cn(
                        iconSizeClasses[size],
                        'shrink-0 opacity-80 transition-transform duration-200 ease-premium group-hover:translate-x-0.5',
                    )}
                />
            )}
        </>
    );

    // An icon-only button has no accessible name from its content, so derive
    // one from what the caller gave us rather than shipping a nameless control.
    const fallbackLabel =
        size === 'icon' && !children
            ? ((rest['aria-label'] as string | undefined) ??
              (rest.title as string | undefined) ??
              (loading ? 'Loading' : undefined))
            : undefined;

    if (typeof rest.href === 'string') {
        const { href, method, ...linkProps } = rest;

        return (
            <Link
                href={href}
                method={method}
                className={classes}
                aria-label={fallbackLabel}
                // Link and button share the geometry; the prop bags overlap
                // enough that a single cast keeps call sites clean.
                {...(linkProps as Record<string, unknown>)}
            >
                {content}
            </Link>
        );
    }

    const { type = 'button', disabled, ...buttonProps } = rest;

    return (
        <button
            type={type}
            disabled={disabled || loading}
            className={classes}
            aria-label={fallbackLabel}
            aria-busy={loading || undefined}
            {...(buttonProps as Record<string, unknown>)}
        >
            {content}
        </button>
    );
}
