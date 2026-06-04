import { useState, useRef, useEffect } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import { LanguageIcon, ChevronDownIcon, CheckIcon } from '@heroicons/react/24/outline';

const LOCALES = [
    { code: 'en', label: 'English', shortLabel: 'EN', flag: '🇬🇧' },
    { code: 'fr', label: 'Français', shortLabel: 'FR', flag: '🇫🇷' },
];

interface LocaleSwitcherProps {
    variant?: 'icon' | 'full';
    className?: string;
}

export function LocaleSwitcher({ variant = 'icon', className = '' }: LocaleSwitcherProps) {
    const { locale, switchLocale } = useTranslation();
    const [isOpen, setIsOpen] = useState(false);
    const dropdownRef = useRef<HTMLDivElement>(null);

    // Auto-close dropdown when clicking outside
    useEffect(() => {
        function handleClickOutside(event: MouseEvent) {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
                setIsOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const activeLocale = LOCALES.find((l) => l.code === locale) || LOCALES[0];

    // ==========================================
    // VARIANT 1: Sliding Pill Switch (Premium Toggle)
    // ==========================================
    if (variant === 'icon') {
        return (
            <div className={`relative inline-flex bg-gray-100 dark:bg-neutral-800 p-1 rounded-xl select-none ${className}`}>
                {/* Sliding active background indicator */}
                <div
                    className={`absolute top-1 bottom-1 w-[calc(50%-4px)] bg-white dark:bg-neutral-700 rounded-lg shadow-sm transition-all duration-300 ease-in-out ${
                        locale === 'en' ? 'left-1' : 'left-[calc(50%+2px)]'
                    }`}
                />
                <button
                    onClick={() => switchLocale('en')}
                    className={`relative z-10 px-3 py-1 text-xs font-bold rounded-lg transition-colors duration-200 ${
                        locale === 'en'
                            ? 'text-gray-900 dark:text-white'
                            : 'text-gray-400 hover:text-gray-600 dark:text-neutral-500 dark:hover:text-neutral-300'
                    }`}
                >
                    EN
                </button>
                <button
                    onClick={() => switchLocale('fr')}
                    className={`relative z-10 px-3 py-1 text-xs font-bold rounded-lg transition-colors duration-200 ${
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
                className="flex items-center gap-2 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800
                    hover:border-gray-300 dark:hover:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-850
                    rounded-xl px-3.5 py-2 text-sm font-medium text-gray-700 dark:text-neutral-200
                    shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
            >
                <span className="text-base leading-none shrink-0" aria-hidden="true">
                    {activeLocale.flag}
                </span>
                <span className="shrink-0">{activeLocale.label}</span>
                <ChevronDownIcon
                    className={`w-3.5 h-3.5 text-gray-400 dark:text-neutral-500 transition-transform duration-200 shrink-0 ${
                        isOpen ? 'rotate-180' : ''
                    }`}
                />
            </button>

            {/* Floating Dropdown Panel */}
            {isOpen && (
                <div className="absolute right-0 top-full mt-2 w-44 bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-xl shadow-lg py-1 z-50 animate-in fade-in slide-in-from-top-1 duration-150">
                    {LOCALES.map((item) => (
                        <button
                            key={item.code}
                            type="button"
                            onClick={() => {
                                switchLocale(item.code);
                                setIsOpen(false);
                            }}
                            className={`w-full flex items-center justify-between px-3.5 py-2.5 text-sm text-left transition-colors duration-150
                                ${
                                    locale === item.code
                                        ? 'bg-blue-50/50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 font-medium'
                                        : 'text-gray-600 dark:text-neutral-300 hover:bg-gray-50 dark:hover:bg-neutral-800/50'
                                }`}
                        >
                            <div className="flex items-center gap-2">
                                <span className="text-base leading-none" aria-hidden="true">
                                    {item.flag}
                                </span>
                                <span>{item.label}</span>
                            </div>
                            {locale === item.code && (
                                <CheckIcon className="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0" />
                            )}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}
