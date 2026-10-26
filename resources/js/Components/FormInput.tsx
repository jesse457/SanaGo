import type { ComponentType, SVGProps } from 'react';
import { Field } from '@/Components/ui/Field';
import { Input } from '@/Components/ui/Input';

interface FormInputProps {
    name: string;
    label?: string;
    type?: string;
    placeholder?: string;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    required?: boolean;
    disabled?: boolean;
    errors?: Record<string, string>;
    value: string | number;
    onChange: (value: string) => void;
    className?: string;
    invalid?: boolean;
    autoComplete?: string;
    step?: string;
    min?: string | number;
    max?: string | number;
}

/**
 * FormInput — legacy-friendly field wrapper kept for existing pages.
 *
 * New code should compose `<Field>` + `<Input>` from `@/Components/ui` directly.
 */
export default function FormInput({
    name,
    label,
    type = 'text',
    placeholder,
    icon: Icon,
    required = false,
    disabled = false,
    errors,
    value,
    onChange,
    className = '',
    invalid,
    autoComplete,
    step,
    min,
    max,
}: FormInputProps) {
    const error = errors?.[name];

    return (
        <Field
            label={label}
            htmlFor={name}
            required={required}
            error={error}
            className={className}
        >
            <Input
                id={name}
                name={name}
                type={type}
                step={step}
                min={min}
                max={max}
                autoComplete={autoComplete}
                placeholder={placeholder}
                disabled={disabled}
                value={value}
                onChange={(event) => onChange(event.target.value)}
                icon={Icon}
                invalid={invalid ?? Boolean(error)}
            />
        </Field>
    );
}
