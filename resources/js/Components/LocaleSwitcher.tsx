import {
    CheckIcon,
    ChevronDownIcon,
    LanguageIcon,
} from '@heroicons/react/24/outline';
import { useEffect, useRef, useState } from 'react';
import { SegmentedControl } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

const LOCALES = [
    { code: 'en', label: 'English', shortLabel: 'EN' },
    { code: 'fr', label: 'Français', shortLabel: 'FR' },
];

interface LocaleSwitcherProps {
    variant?: 'icon' | 'full';
    className?: string;
}

/**
 * LocaleSwitcher — language choice. The compact variant is a segmented
 * control; the full variant is a hairline dropdown. Locales are labelled with
 * typographic codes, not flag emoji.
 */
export function LocaleSwitcher({
    variant = 'icon',
    className = '',
}: LocaleSwitcherProps) {
    const { locale, switchLocale } = useTranslation();
    const [isOpen, setIsOpen] = useState(false);
    const dropdownRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        function handleClickOutside(event: MouseEvent) {
            if (
                dropdownRef.current &&
                !dropdownRef.current.contains(event.target as Node)
            ) {
                setIsOpen(false);
            }
        }

        function handleEscape(event: KeyboardEvent) {
            if (event.key === 'Escape') {
                setIsOpen(false);
            }
        }

        document.addEventListener('mousedown', handleClickOutside);
        document.addEventListener('keydown', handleEscape);

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
            document.removeEventListener('keydown', handleEscape);
        };
    }, []);

    const activeLocale =
        LOCALES.find((item) => item.code === locale) ?? LOCALES[0];

    if (variant === 'icon') {
        return (
            <SegmentedControl
                size="sm"
                className={className}
                value={activeLocale.code}
                onChange={(value) => switchLocale(value)}
                options={LOCALES.map((item) => ({
                    value: item.code,
                    label: item.shortLabel,
                }))}
            />
        );
    }

    return (
        <div className={`relative inline-flex ${className}`} ref={dropdownRef}>
            <button
                type="button"
                onClick={() => setIsOpen((previous) => !previous)}
                aria-expanded={isOpen}
                aria-haspopup="listbox"
                className="flex items-center gap-1.5 rounded-md border border-line bg-surface py-1 pr-2 pl-2 text-[12.5px] font-medium text-ink-muted transition-colors duration-200 ease-premium hover:border-line-strong hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
            >
                <LanguageIcon className="h-4 w-4 shrink-0 text-ink-subtle" />
                <span className="font-mono text-[11px] tracking-tight">
                    {activeLocale.shortLabel}
                </span>
                <ChevronDownIcon
                    className={`h-3 w-3 shrink-0 text-ink-subtle transition-transform duration-200 ease-premium ${isOpen ? 'rotate-180' : ''}`}
                />
            </button>

            {isOpen && (
                <div
                    role="listbox"
                    className="animate-fade-in absolute top-full right-0 z-50 mt-1.5 w-44 origin-top-right overflow-hidden rounded-lg border border-line bg-surface p-1 shadow-float"
                >
                    {LOCALES.map((item) => {
                        const active = locale === item.code;

                        return (
                            <button
                                key={item.code}
                                type="button"
                                role="option"
                                aria-selected={active}
                                onClick={() => {
                                    switchLocale(item.code);
                                    setIsOpen(false);
                                }}
                                className={`flex w-full items-center justify-between rounded-md px-2.5 py-1.5 text-left text-[13px] transition-colors duration-150 ease-premium ${
                                    active
                                        ? 'bg-accent-soft font-semibold text-accent-ink'
                                        : 'text-ink-muted hover:bg-surface-hover hover:text-ink'
                                }`}
                            >
                                <span className="flex items-center gap-2">
                                    <span className="font-mono text-[10.5px] tracking-tight text-ink-subtle">
                                        {item.shortLabel}
                                    </span>
                                    {item.label}
                                </span>
                                {active && (
                                    <CheckIcon className="h-3.5 w-3.5 shrink-0 text-accent" />
                                )}
                            </button>
                        );
                    })}
                </div>
            )}
        </div>
    );
}
