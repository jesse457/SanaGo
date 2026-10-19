import type { ComponentType, SVGProps } from 'react';
import { Field } from '@/Components/ui/Field';
import { Textarea } from '@/Components/ui/Textarea';

interface FormTextareaProps {
    name: string;
    label?: string;
    placeholder?: string;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    required?: boolean;
    disabled?: boolean;
    errors?: Record<string, string>;
    value: string;
    onChange: (value: string) => void;
    rows?: number;
    className?: string;
}

/**
 * FormTextarea — legacy-friendly textarea wrapper kept for existing pages.
 *
 * New code should compose `<Field>` + `<Textarea>` from `@/Components/ui`
 * directly.
 */
export default function FormTextarea({
    name,
    label,
    placeholder,
    required = false,
    disabled = false,
    errors,
    value,
    onChange,
    rows = 4,
    className = '',
}: FormTextareaProps) {
    const error = errors?.[name];

    return (
        <Field
            label={label}
            htmlFor={name}
            required={required}
            error={error}
            className={className}
        >
            <Textarea
                id={name}
                name={name}
                placeholder={placeholder}
                disabled={disabled}
                value={value}
                onChange={(event) => onChange(event.target.value)}
                rows={rows}
                invalid={Boolean(error)}
            />
        </Field>
    );
}
