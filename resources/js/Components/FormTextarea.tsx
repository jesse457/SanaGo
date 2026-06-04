import type { ComponentType, SVGProps } from 'react';

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

export default function FormTextarea({
    name,
    label,
    placeholder,
    icon: Icon,
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
                    <div className="pointer-events-none absolute top-4 left-4">
                        <Icon
                            className={`h-5 w-5 ${error ? 'text-red-400' : 'text-gray-400 dark:text-neutral-500'}`}
                        />
                    </div>
                )}
                <textarea
                    id={name}
                    name={name}
                    placeholder={placeholder}
                    disabled={disabled}
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                    rows={rows}
                    className={`w-full resize-none rounded-lg border bg-white py-3 text-sm font-semibold transition-all outline-none dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500 ${Icon ? 'pr-4 pl-12' : 'px-4'} ${
                        error
                            ? 'border-red-500 focus:ring-2 focus:ring-red-500'
                            : 'border-gray-200 focus:ring-2 focus:ring-blue-500 dark:border-neutral-700'
                    } ${disabled ? 'cursor-not-allowed opacity-50' : ''}`}
                />
            </div>
            {error && (
                <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                    {error}
                </p>
            )}
        </div>
    );
}
