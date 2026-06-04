import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import { type VariantProps, cva } from 'class-variance-authority';
import { type ComponentPropsWithoutRef } from 'react';

const buttonVariants = cva(
  'inline-flex items-center justify-center font-bold rounded-2xl transition-all duration-200 active:scale-[0.98] disabled:opacity-70 disabled:pointer-events-none',
  {
    variants: {
      variant: {
        primary:
          'bg-blue-600 text-white shadow-xl shadow-blue-200 dark:shadow-blue-900/30 hover:bg-blue-700',
        secondary:
          'bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 text-slate-700 dark:text-neutral-300 hover:bg-slate-50 dark:hover:bg-neutral-700',
        ghost:
          'text-slate-600 dark:text-neutral-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-neutral-800',
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
