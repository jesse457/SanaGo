import { createContext, useContext, useCallback, useMemo, createElement } from 'react';
import { usePage, router } from '@inertiajs/react';

type Translations = Record<string, any>;

interface TranslationContextType {
    locale: string;
    t: (key: string, replacements?: Record<string, string | number>) => string;
    switchLocale: (locale: string) => void;
}

const TranslationContext = createContext<TranslationContextType | null>(null);

function resolveNestedKey(key: string, translations: Translations): string | undefined {
    // Try exact path first
    const result = key.split('.').reduce((obj, segment) => {
        if (obj && typeof obj === 'object' && segment in obj) {
            return obj[segment];
        }
        return undefined;
    }, translations as any);

    if (result !== undefined) return result;

    // Fallback for flat keys: try general.* then common.*
    if (!key.includes('.')) {
        const g = (translations as any)?.general;
        if (g && key in g) return g[key];
        const c = (translations as any)?.common;
        if (c && key in c) return c[key];
    }

    return undefined;
}

function interpolate(value: string, replacements: Record<string, string | number>): string {
    return Object.entries(replacements).reduce(
        (str, [k, v]) => str.replace(new RegExp(`:${k}`, 'g'), String(v)),
        value,
    );
}

/**
 * Wrap layout children to provide cached translations via context.
 * Use inside layouts where usePage() is available.
 */
export function TranslationProvider({ children }: { children: React.ReactNode }) {
    const { props } = usePage();
    const { locale, translations } = props as any;

    const t = useCallback(
        (key: string, replacements?: Record<string, string | number>) => {
            const value = resolveNestedKey(key, translations ?? {});
            if (value === undefined) {
                return key;
            }
            if (replacements) {
                return interpolate(value, replacements);
            }
            return value;
        },
        [translations],
    );

    const switchLocale = useCallback((newLocale: string) => {
        router.post('/locale', { locale: newLocale }, { preserveScroll: true });
    }, []);

    const value = useMemo(() => ({ locale, t, switchLocale }), [locale, t, switchLocale]);

    return createElement(TranslationContext.Provider, { value }, children);
}

/**
 * Translation hook that works at ANY level inside the Inertia tree.
 * Uses context if available (inside a TranslationProvider), otherwise reads
 * directly from Inertia page props — so it works at page top-level too.
 */
export function useTranslation(): TranslationContextType {
    const context = useContext(TranslationContext);
    const { props } = usePage();

    // If inside a TranslationProvider, use cached context
    if (context) {
        return context;
    }

    // Fallback: read directly from Inertia page props (works at page top-level)
    const { locale, translations } = props as any;

    const t = (key: string, replacements?: Record<string, string | number>) => {
        const value = resolveNestedKey(key, translations ?? {});
        if (value === undefined) {
            return key;
        }
        if (replacements) {
            return interpolate(value, replacements);
        }
        return value;
    };

    const switchLocale = (newLocale: string) => {
        router.post('/locale', { locale: newLocale }, { preserveScroll: true });
    };

    return { locale: locale ?? 'en', t, switchLocale };
}
