import type { ComponentType, SVGProps, TextareaHTMLAttributes } from 'react';
import { useControlInvalid } from '@/Components/ui/FieldContext';
import { controlBase, controlState } from '@/Components/ui/Input';
import { cn } from '@/lib/utils';

export interface TextareaProps extends TextareaHTMLAttributes<HTMLTextAreaElement> {
    invalid?: boolean;
    /** Decorative icon rendered inside the control. */
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
}

export function Textarea({
    invalid,
    icon: Icon,
    className,
    rows = 3,
    ...props
}: TextareaProps) {
    const isInvalid = useControlInvalid(invalid, props.name, props.id);

    const field = (
        <textarea
            rows={rows}
            className={cn(
                controlBase,
                // Multi-line controls have no fixed height: they inherit the
                // same border/inset/focus treatment and only pad vertically.
                'min-h-20 resize-y py-2.5 leading-relaxed',
                Icon && 'pl-9',
                isInvalid ? controlState.invalid : controlState.default,
                className,
            )}
            aria-invalid={isInvalid || undefined}
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
