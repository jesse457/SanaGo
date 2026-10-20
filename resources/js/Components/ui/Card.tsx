import type { ComponentType, HTMLAttributes, ReactNode } from 'react';
import { resolveIconNode } from '@/lib/icons';
import { cn } from '@/lib/utils';

type DivProps = HTMLAttributes<HTMLDivElement>;

/**
 * Card = one surface with a hairline. No drop shadow by default: elevation is
 * reserved for overlays and floating chrome. The rule is hairline OR elevation,
 * never both — `interactive` is the single place a card lifts, because a hover
 * response needs the depth cue.
 */
export function Card({
    className,
    children,
    interactive = false,
    ...props
}: DivProps & { children?: ReactNode; interactive?: boolean }) {
    return (
        <div
            className={cn(
                'relative rounded-xl border border-line bg-surface',
                interactive &&
                    'transition-[border-color,box-shadow,transform] duration-300 ease-premium hover:-translate-y-0.5 hover:border-line-strong hover:shadow-raised motion-reduce:transform-none',
                className,
            )}
            {...props}
        >
            {children}
        </div>
    );
}

export function CardHeader({
    className,
    children,
    actions,
    ...props
}: DivProps & { children?: ReactNode; actions?: ReactNode }) {
    return (
        <div
            className={cn(
                'flex items-start justify-between gap-4 border-b border-line px-5 py-4 sm:px-6',
                className,
            )}
            {...props}
        >
            <div className="min-w-0">{children}</div>
            {actions && (
                <div className="flex shrink-0 items-center gap-2">
                    {actions}
                </div>
            )}
        </div>
    );
}

export function CardTitle({
    className,
    children,
    ...props
}: HTMLAttributes<HTMLHeadingElement>) {
    return (
        <h3
            className={cn(
                'text-[15px] leading-tight font-semibold tracking-tight text-ink',
                className,
            )}
            {...props}
        >
            {children}
        </h3>
    );
}

export function CardDescription({
    className,
    children,
    ...props
}: HTMLAttributes<HTMLParagraphElement>) {
    return (
        <p
            className={cn(
                'mt-1 text-[13px] leading-relaxed text-ink-muted',
                className,
            )}
            {...props}
        >
            {children}
        </p>
    );
}

export function CardContent({
    className,
    children,
    ...props
}: DivProps & { children?: ReactNode }) {
    return (
        <div className={cn('px-5 py-4 sm:px-6', className)} {...props}>
            {children}
        </div>
    );
}

export function CardFooter({
    className,
    children,
    ...props
}: DivProps & { children?: ReactNode }) {
    return (
        <div
            className={cn(
                'flex flex-wrap items-center justify-end gap-2 border-t border-line bg-surface-muted/60 px-5 py-3.5 sm:px-6',
                className,
            )}
            {...props}
        >
            {children}
        </div>
    );
}

interface SectionCardProps {
    title?: string;
    description?: string;
    /** An icon component, or an already-built icon node. */
    icon?: ComponentType<React.SVGProps<SVGSVGElement>> | ReactNode;
    actions?: ReactNode;
    footer?: ReactNode;
    children: ReactNode;
    className?: string;
    contentClassName?: string;
}

/**
 * SectionCard — the "page section" pattern: header, body, optional footer.
 * Replaces the per-page `rounded-xl border ... p-6` blocks.
 */
export function SectionCard({
    title,
    description,
    icon,
    actions,
    footer,
    children,
    className,
    contentClassName,
}: SectionCardProps) {
    const iconNode = resolveIconNode(icon, 'h-[18px] w-[18px]');

    return (
        <Card className={className}>
            {title && (
                <CardHeader
                    actions={actions}
                    className="gap-3 px-5 py-4 sm:px-6"
                >
                    <div className="flex items-center gap-3">
                        {iconNode && (
                            <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-accent">
                                {iconNode}
                            </span>
                        )}
                        <div className="min-w-0">
                            <CardTitle>{title}</CardTitle>
                            {description && (
                                <CardDescription>{description}</CardDescription>
                            )}
                        </div>
                    </div>
                </CardHeader>
            )}
            <div className={cn('px-5 py-4 sm:px-6', contentClassName)}>
                {children}
            </div>
            {footer && <CardFooter>{footer}</CardFooter>}
        </Card>
    );
}
