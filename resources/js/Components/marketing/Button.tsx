import type { ButtonHTMLAttributes, ReactNode } from 'react';
import type { ButtonProps } from '@/Components/ui/Button';
import { Button as UiButton } from '@/Components/ui/Button';

/**
 * Marketing call-to-action.
 *
 * A thin semantic layer over the shared kit button: marketing pages speak
 * `primary` / `secondary` / `ghost`, the kit keeps the geometry, the pill-free
 * radii and the focus ring. `primary` maps to the kit's `accent`, so the site's
 * one considered blue carries every conversion point.
 */

type MarketingVariant = 'primary' | 'secondary' | 'ghost';
type MarketingSize = 'md' | 'lg' | 'xl';

export interface MarketingButtonProps {
    variant?: MarketingVariant;
    size?: MarketingSize;
    href?: string;
    className?: string;
    children?: ReactNode;
}

const variantMap = {
    primary: 'accent',
    secondary: 'secondary',
    ghost: 'ghost',
} as const;

/* `xl` gets extra horizontal breathing room rather than a new control height. */
const sizeMap = {
    md: 'md',
    lg: 'lg',
    xl: 'lg',
} as const;

export default function Button({
    variant = 'primary',
    size = 'md',
    href,
    className,
    children,
    ...props
}: MarketingButtonProps &
    Omit<ButtonHTMLAttributes<HTMLButtonElement>, 'className' | 'children'>) {
    const shared = {
        variant: variantMap[variant],
        size: sizeMap[size],
        className:
            size === 'xl' ? `px-6 text-[15px] ${className ?? ''}` : className,
        children,
    };

    if (href) {
        return <UiButton href={href} {...(shared as ButtonProps)} />;
    }

    return (
        <UiButton {...(shared as ButtonProps)} {...(props as ButtonProps)} />
    );
}
