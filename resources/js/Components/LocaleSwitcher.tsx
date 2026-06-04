import {
    LanguageIcon,
    ChevronDownIcon,
    CheckIcon,
} from '@heroicons/react/24/outline';
import { useState, useRef, useEffect } from 'react';
import { useTranslation } from '@/hooks/useTranslation';

const LOCALES = [
    { code: 'en', label: 'English', shortLabel: 'EN', flag: '🇬🇧' },
    { code: 'fr', label: 'Français', shortLabel: 'FR', flag: '🇫🇷' },
];

interface LocaleSwitcherProps {
    variant?: 'icon' | 'full';
    className?: string;
}

export function LocaleSwitcher({
    variant = 'icon',
    className = '',
}: LocaleSwitcherProps) {
    const { locale, switchLocale } = useTranslation();
    const [isOpen, setIsOpen] = useState(false);
    const dropdownRef = useRef<HTMLDivElement>(null);

    // Auto-close dropdown when clicking outside
    useEffect(() => {
        function handleClickOutside(event: MouseEvent) {
            if (
                dropdownRef.current &&
                !dropdownRef.current.contains(event.target as Node)
            ) {
                setIsOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);

        return () =>
            document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const activeLocale = LOCALES.find((l) => l.code === locale) || LOCALES[0];

    // ==========================================
    // VARIANT 1: Sliding Pill Switch (Premium Toggle)
    // ==========================================
    if (variant === 'icon') {
        return (
            <div
                className={`relative inline-flex rounded-xl bg-gray-100 p-1 select-none dark:bg-neutral-800 ${className}`}
            >
                {/* Sliding active background indicator */}
                <div
                    className={`absolute top-1 bottom-1 w-[calc(50%-4px)] rounded-lg bg-white shadow-sm transition-all duration-300 ease-in-out dark:bg-neutral-700 ${
                        locale === 'en' ? 'left-1' : 'left-[calc(50%+2px)]'
                    }`}
                />
                <button
                    onClick={() => switchLocale('en')}
                    className={`relative z-10 rounded-lg px-3 py-1 text-xs font-bold transition-colors duration-200 ${
                        locale === 'en'
                            ? 'text-gray-900 dark:text-white'
                            : 'text-gray-400 hover:text-gray-600 dark:text-neutral-500 dark:hover:text-neutral-300'
                    }`}
                >
                    EN
                </button>
                <button
                    onClick={() => switchLocale('fr')}
                    className={`relative z-10 rounded-lg px-3 py-1 text-xs font-bold transition-colors duration-200 ${
                        locale === 'fr'
                            ? 'text-gray-900 dark:text-white'
                            : 'text-gray-400 hover:text-gray-600 dark:text-neutral-500 dark:hover:text-neutral-300'
                    }`}
                >
                    FR
                </button>
            </div>
        );
    }

    // ==========================================
    // VARIANT 2: Modern Custom Dropdown Menu
    // ==========================================
    return (
        <div className={`relative inline-flex ${className}`} ref={dropdownRef}>
            {/* Dropdown Trigger Button */}
            <button
                type="button"
                onClick={() => setIsOpen(!isOpen)}
                className="dark:hover:bg-neutral-850 flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:border-gray-300 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500/20 focus:outline-none dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:border-neutral-700"
            >
                <span
                    className="shrink-0 text-base leading-none"
                    aria-hidden="true"
                >
                    {activeLocale.flag}
                </span>
                <span className="shrink-0">{activeLocale.label}</span>
                <ChevronDownIcon
                    className={`h-3.5 w-3.5 shrink-0 text-gray-400 transition-transform duration-200 dark:text-neutral-500 ${
                        isOpen ? 'rotate-180' : ''
                    }`}
                />
            </button>

            {/* Floating Dropdown Panel */}
            {isOpen && (
                <div className="animate-in fade-in slide-in-from-top-1 absolute top-full right-0 z-50 mt-2 w-44 rounded-xl border border-gray-100 bg-white py-1 shadow-lg duration-150 dark:border-neutral-800 dark:bg-neutral-900">
                    {LOCALES.map((item) => (
                        <button
                            key={item.code}
                            type="button"
                            onClick={() => {
                                switchLocale(item.code);
                                setIsOpen(false);
                            }}
                            className={`flex w-full items-center justify-between px-3.5 py-2.5 text-left text-sm transition-colors duration-150 ${
                                locale === item.code
                                    ? 'bg-blue-50/50 font-medium text-blue-600 dark:bg-blue-950/20 dark:text-blue-400'
                                    : 'text-gray-600 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-800/50'
                            }`}
                        >
                            <div className="flex items-center gap-2">
                                <span
                                    className="text-base leading-none"
                                    aria-hidden="true"
                                >
                                    {item.flag}
                                </span>
                                <span>{item.label}</span>
                            </div>
                            {locale === item.code && (
                                <CheckIcon className="h-4 w-4 shrink-0 text-blue-500 dark:text-blue-400" />
                            )}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}
