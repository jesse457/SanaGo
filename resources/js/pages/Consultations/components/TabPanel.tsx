import { motion } from 'framer-motion';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

export const tabVariants = {
    initial: { opacity: 0, y: 8 },
    animate: { opacity: 1, y: 0 },
    exit: { opacity: 0, y: -8 },
};

interface TabPanelProps {
    children: ReactNode;
    className?: string;
}

/**
 * TabPanel — the animated frame every consultation tab shares. Keeps the entry
 * motion and the reading width identical across tabs.
 */
export function TabPanel({ children, className }: TabPanelProps) {
    return (
        <motion.div
            initial={tabVariants.initial}
            animate={tabVariants.animate}
            exit={tabVariants.exit}
            transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
            className={cn('mx-auto max-w-4xl space-y-4', className)}
        >
            {children}
        </motion.div>
    );
}
