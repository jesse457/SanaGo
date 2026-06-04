import type { ComponentType, SVGProps } from 'react';

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
}

export default function FormSelect({
    name,
    label,
    icon: Icon,
    required = false,
    disabled = false,
    errors,
    value,
    onChange,
    options,
    placeholder,
    className = '',
}: FormSelectProps) {
    const error = errors?.[name];

    return (
        <div className={className}>
            {label && (
                <label
                    htmlFor={name}
                    className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400"
                >
                    {label}
                    {required && <span className="text-red-500"> *</span>}
                </label>
            )}
            <div className={`relative ${label ? 'mt-1.5' : ''}`}>
                {Icon && (
                    <div className="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2">
                        <Icon
                            className={`h-5 w-5 ${error ? 'text-red-400' : 'text-gray-400 dark:text-neutral-500'}`}
                        />
                    </div>
                )}
                <select
                    id={name}
                    name={name}
                    disabled={disabled}
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                    className={`w-full appearance-none rounded-lg border bg-white py-3 text-sm font-semibold transition-all outline-none dark:bg-neutral-800 dark:text-neutral-100 ${Icon ? 'pr-10 pl-12' : 'pr-10 pl-4'} ${
                        error
                            ? 'border-red-500 focus:ring-2 focus:ring-red-500'
                            : 'border-gray-200 focus:ring-2 focus:ring-blue-500 dark:border-neutral-700'
                    } ${disabled ? 'cursor-not-allowed opacity-50' : ''}`}
                >
                    {placeholder && <option value="">{placeholder}</option>}
                    {options.map((opt) => (
                        <option key={opt.value} value={opt.value}>
                            {opt.label}
                        </option>
                    ))}
                </select>
                <div className="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2">
                    <svg
                        className="h-4 w-4 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </div>
            </div>
            {error && (
                <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                    {error}
                </p>
            )}
        </div>
    );
}
