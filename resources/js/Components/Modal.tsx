import type { ReactNode } from 'react';
import { Dialog } from '@/Components/ui/Dialog';
import type { DialogSize } from '@/Components/ui/Dialog';

interface ModalProps {
    isOpen: boolean;
    onClose: () => void;
    title: string;
    children: ReactNode;
    maxWidth?: string;
    description?: ReactNode;
    footer?: ReactNode;
}

const sizeByWidth: Record<string, DialogSize> = {
    'max-w-sm': 'sm',
    'max-w-md': 'md',
    'max-w-lg': 'lg',
    'max-w-xl': 'lg',
    'max-w-2xl': 'xl',
    'max-w-3xl': '2xl',
    'max-w-4xl': '2xl',
};

/**
 * Modal — legacy modal kept for existing pages (`isOpen` / `maxWidth` API).
 *
 * New code should use `<Dialog>` from `@/Components/ui`, which adds sizes,
 * footers and a mobile sheet variant.
 */
export default function Modal({
    isOpen,
    onClose,
    title,
    children,
    maxWidth = 'max-w-lg',
    description,
    footer,
}: ModalProps) {
    return (
        <Dialog
            open={isOpen}
            onClose={onClose}
            title={title}
            description={description}
            footer={footer}
            size={sizeByWidth[maxWidth] ?? 'lg'}
        >
            {children}
        </Dialog>
    );
}
