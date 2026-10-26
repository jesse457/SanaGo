import { usePage } from '@inertiajs/react';
import { createContext, useContext } from 'react';

export interface FieldErrorContextValue {
    invalid: boolean;
    error?: string | null;
}

export const FieldErrorContext = createContext<FieldErrorContextValue | null>(
    null,
);

/**
 * Lets a control inside `<Field>` inherit the field's validation state, so
 * passing `error` alone is enough to render the control (and its border) red.
 * An explicit `invalid` prop still wins.
 */
export function useFieldError(): FieldErrorContextValue | null {
    return useContext(FieldErrorContext);
}

interface ErrorPageProps {
    [key: string]: unknown;
    errors?: Record<string, string>;
}

/**
 * Resolve whether a control is invalid.
 *
 * Precedence: explicit `invalid` → enclosing `<Field error>` → the page's
 * validation bag keyed by the control's `name`/`id`. The last step catches
 * fields where a form forgot to forward its error, so no input silently stays
 * neutral while the server rejected it.
 */
export function useControlInvalid(
    explicitInvalid: boolean | undefined,
    name?: string,
    id?: string,
): boolean {
    const field = useFieldError();
    const { errors } = usePage<ErrorPageProps>().props;

    if (explicitInvalid !== undefined) {
        return explicitInvalid;
    }

    if (field !== null) {
        return field.invalid;
    }

    const key = name ?? id;

    return key ? Boolean(errors?.[key]) : false;
}

/**
 * The page-level error for a field, used by `<Field>` when a form does not
 * forward it explicitly.
 */
export function usePageFieldError(key?: string): string | undefined {
    const { errors } = usePage<ErrorPageProps>().props;

    return key ? errors?.[key] : undefined;
}
