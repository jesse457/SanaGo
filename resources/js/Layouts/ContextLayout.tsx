import { usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface ContextLayoutProps {
    children: ReactNode;
    headerTitle?: string;
    headerSubtitle?: string;
    headerAction?: ReactNode;
}

/**
 * ContextLayout — picks the shell for pages that exist in both contexts.
 *
 * `/profile`, `/settings` and the notification inbox are shared routes, so
 * rendering `AppLayout` unconditionally leaves landlord users inside the
 * hospital chrome (and with tenant-only nav / permissions). The shared
 * `isLandlord` prop is set by HandleInertiaRequests from the tenancy state.
 */
export default function ContextLayout({
    children,
    headerTitle,
    headerSubtitle,
    headerAction,
}: ContextLayoutProps) {
    const { props } = usePage();
    const isLandlord = Boolean((props as { isLandlord?: boolean }).isLandlord);

    if (isLandlord) {
        return (
            <SuperAdminLayout
                headerTitle={headerTitle}
                headerSubtitle={headerSubtitle}
            >
                {children}
            </SuperAdminLayout>
        );
    }

    return (
        <AppLayout
            headerTitle={headerTitle}
            headerSubtitle={headerSubtitle}
            headerAction={headerAction}
        >
            {children}
        </AppLayout>
    );
}
