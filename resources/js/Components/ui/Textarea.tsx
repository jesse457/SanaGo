import type { ComponentType, SVGProps, TextareaHTMLAttributes } from 'react';
import { controlBase, controlState } from '@/Components/ui/Input';
import { cn } from '@/lib/utils';

export interface TextareaProps extends TextareaHTMLAttributes<HTMLTextAreaElement> {
    invalid?: boolean;
    /** Decorative icon rendered inside the control. */
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
}

export function Textarea({
    invalid = false,
    icon: Icon,
    className,
    rows = 3,
    ...props
}: TextareaProps) {
    const field = (
        <textarea
            rows={rows}
            className={cn(
                controlBase,
                // Multi-line controls have no fixed height: they inherit the
                // same border/inset/focus treatment and only pad vertically.
                'min-h-20 resize-y py-2.5 leading-relaxed',
                Icon && 'pl-9',
                invalid ? controlState.invalid : controlState.default,
                className,
            )}
            aria-invalid={invalid || undefined}
            {...props}
        />
    );

    if (!Icon) {
        return field;
    }

    return (
        <div className="relative">
            <Icon className="pointer-events-none absolute top-3.5 left-3 h-4 w-4 text-ink-subtle" />
            {field}
        </div>
    );
}
