import { Dialog, Transition } from '@headlessui/react';
import { motion, AnimatePresence } from 'framer-motion';
import { Fragment, ReactNode } from 'react';
import { XMarkIcon } from '@heroicons/react/24/outline';

interface ModalProps {
    isOpen: boolean;
    onClose: () => void;
    title: string;
    children: ReactNode;
    maxWidth?: string;
}

export default function Modal({ isOpen, onClose, title, children, maxWidth = "max-w-lg" }: ModalProps) {
    return (
        <AnimatePresence>
            {isOpen && (
                <Dialog
                    static
                    as={motion.div}
                    open={isOpen}
                    onClose={onClose}
                    className="relative z-50"
                >
                    {/* Backdrop */}
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        className="fixed inset-0 bg-gray-900/40 backdrop-blur-md"
                    />

                    <div className="fixed inset-0 overflow-y-auto">
                        <div className="flex min-h-full items-center justify-center p-4">
                            <motion.div
                                initial={{ opacity: 0, scale: 0.9, y: 20 }}
                                animate={{ opacity: 1, scale: 1, y: 0 }}
                                exit={{ opacity: 0, scale: 0.9, y: 20 }}
                                transition={{ type: 'spring', damping: 25, stiffness: 300 }}
                                className={`relative w-full ${maxWidth} overflow-hidden rounded-[32px] bg-white dark:bg-neutral-800 shadow-2xl dark:shadow-neutral-950/50`}
                            >
                                {/* Header */}
                                <div className="flex items-center justify-between border-b border-gray-50 dark:border-neutral-700 bg-gray-50/30 dark:bg-neutral-700/30 px-8 py-6">
                                    <Dialog.Title className="text-xl font-black tracking-tight text-gray-900 dark:text-neutral-100">
                                        {title}
                                    </Dialog.Title>
                                    <button
                                        onClick={onClose}
                                        className="rounded-full p-2 text-gray-400 dark:text-neutral-400 transition-colors hover:bg-white dark:hover:bg-neutral-700 hover:text-gray-600 dark:hover:text-neutral-200"
                                    >
                                        <XMarkIcon className="w-6 h-6" />
                                    </button>
                                </div>

                                {/* Body */}
                                <div className="p-8">
                                    {children}
                                </div>
                            </motion.div>
                        </div>
                    </div>
                </Dialog>
            )}
        </AnimatePresence>
    );
}