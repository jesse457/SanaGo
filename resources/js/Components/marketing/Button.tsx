import { Link } from '@inertiajs/react';
import { cva } from 'class-variance-authority';
import type { VariantProps } from 'class-variance-authority';
import type { ComponentPropsWithoutRef } from 'react';
import { cn } from '@/lib/utils';

const buttonVariants = cva(
    'inline-flex items-center justify-center rounded-2xl font-bold transition-all duration-200 active:scale-[0.98] disabled:pointer-events-none disabled:opacity-70',
    {
        variants: {
            variant: {
                primary:
                    'bg-blue-600 text-white shadow-xl shadow-blue-200 hover:bg-blue-700 dark:shadow-blue-900/30',
                secondary:
                    'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700',
                ghost: 'text-slate-600 hover:bg-slate-100 hover:text-blue-600 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-blue-400',
            },
            size: {
                sm: 'px-4 py-2 text-xs',
                md: 'px-6 py-2.5 text-sm',
                lg: 'px-8 py-4 text-base',
                xl: 'px-12 py-4 text-base',
            },
        },
        defaultVariants: {
            variant: 'primary',
            size: 'md',
        },
    },
);

type ButtonProps = ComponentPropsWithoutRef<'button'> &
    VariantProps<typeof buttonVariants> & {
        href?: string;
    };

export default function Button({
    variant,
    size,
    className,
    href,
    children,
    ...props
}: ButtonProps) {
    const classes = cn(buttonVariants({ variant, size }), className);

    if (href) {
        return (
            <Link href={href} className={classes}>
                {children}
            </Link>
        );
    }

    return (
        <button className={classes} {...props}>
            {children}
        </button>
    );
}
