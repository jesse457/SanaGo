import { XMarkIcon } from '@heroicons/react/24/outline';
import { AnimatePresence, motion } from 'framer-motion';
import type { ReactNode } from 'react';
import { Button } from '@/Components/ui';
import type { Translate } from './types';

interface PatientDrawerProps {
    open: boolean;
    onClose: () => void;
    title: string;
    children: ReactNode;
    t: Translate;
}

/**
 * PatientDrawer — the left sheet that carries the patient rail on small screens.
 * Motion is transform/opacity only, per the house style.
 */
export function PatientDrawer({
    open,
    onClose,
    title,
    children,
    t,
}: PatientDrawerProps) {
    return (
        <AnimatePresence>
            {open && (
                <>
                    <motion.div
                        key="backdrop"
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        transition={{ duration: 0.2 }}
                        className="fixed inset-0 z-40 bg-ink/30 backdrop-blur-[2px]"
                        onClick={onClose}
                    />

                    <motion.aside
                        key="drawer"
                        initial={{ x: '-100%' }}
                        animate={{ x: 0 }}
                        exit={{ x: '-100%' }}
                        transition={{
                            type: 'spring',
                            damping: 28,
                            stiffness: 300,
                        }}
                        className="fixed inset-y-0 left-0 z-50 flex w-[85vw] max-w-sm flex-col bg-surface shadow-float"
                    >
                        <div className="flex items-center justify-between border-b border-line px-4 py-3">
                            <h2 className="text-[13px] font-semibold tracking-tight text-ink">
                                {title}
                            </h2>
                            <Button
                                variant="ghost"
                                size="icon"
                                icon={XMarkIcon}
                                aria-label={t('searchPatient')}
                                onClick={onClose}
                            />
                        </div>
                        {children}
                    </motion.aside>
                </>
            )}
        </AnimatePresence>
    );
}
