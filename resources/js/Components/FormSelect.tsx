import type { ComponentType, SVGProps } from 'react';
import { Field } from '@/Components/ui/Field';
import { Select } from '@/Components/ui/Select';

interface SelectOption {
    value: string | number;
    label: string;
}

interface FormSelectProps {
    name: string;
    label?: string;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    required?: boolean;
    disabled?: boolean;
    errors?: Record<string, string>;
    value: string | number;
    onChange: (value: string) => void;
    options: SelectOption[];
    placeholder?: string;
    className?: string;
    invalid?: boolean;
}

/**
 * FormSelect — legacy-friendly select wrapper kept for existing pages.
 *
 * New code should compose `<Field>` + `<Select>` from `@/Components/ui`
 * directly.
 */
export default function FormSelect({
    name,
    label,
    required = false,
    disabled = false,
    errors,
    value,
    onChange,
    options,
    placeholder,
    className = '',
    invalid,
}: FormSelectProps) {
    const error = errors?.[name];

    return (
        <Field
            label={label}
            htmlFor={name}
            required={required}
            error={error}
            className={className}
        >
            <Select
                id={name}
                name={name}
                disabled={disabled}
                value={value}
                onChange={(event) => onChange(event.target.value)}
                invalid={invalid ?? Boolean(error)}
            >
                {placeholder && <option value="">{placeholder}</option>}
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </Select>
        </Field>
    );
}

export type { SelectOption as FormSelectOption };

/** `icon` is accepted for backwards compatibility; the chevron owns that slot. */
export type FormSelectIcon = ComponentType<SVGProps<SVGSVGElement>>;
