import { useState, useEffect, useCallback } from 'react';
import { swal } from '@/Components/ui/sweetAlert';
import type { Notification, LabRequestEvent } from '@/types';

export function useNotifications() {
    const [notifications, setNotifications] = useState<Notification[]>(() => {
        const stored = localStorage.getItem('lab_notifications');

        return stored ? JSON.parse(stored) : [];
    });
    const [showToast, setShowToast] = useState<boolean>(false);
    const [toastData, setToastData] = useState<Notification | null>(null);

    const unreadCount = notifications.filter((n) => !n.read).length;

    /* One writer for the persisted feed: the effect, never an updater body. */
    useEffect(() => {
        localStorage.setItem(
            'lab_notifications',
            JSON.stringify(notifications),
        );
    }, [notifications]);

    const addNotification = useCallback(
        (data: Omit<Notification, 'created_at' | 'read'>) => {
            /* Storage is written from the observable result, not from inside
               the updater — React runs updaters twice in development. */
            setNotifications((prev) => {
                if (prev.some((n) => n.id === data.id)) {
                    return prev;
                }

                return [
                    {
                        ...data,
                        created_at: new Date().toISOString(),
                        read: false,
                    },
                    ...prev,
                ].slice(0, 50);
            });

            const incoming: Notification = {
                ...data,
                created_at: new Date().toISOString(),
                read: false,
            };

            setToastData(incoming);
            setShowToast(true);
            setTimeout(() => setShowToast(false), 8000);
        },
        [],
    );

    const markAsRead = (id: string) => {
        setNotifications((prev) =>
            prev.map((n) => (n.id === id ? { ...n, read: true } : n)),
        );
    };

    const markAllRead = () => {
        setNotifications((prev) => prev.map((n) => ({ ...n, read: true })));
    };

    const clearAll = async () => {
        const confirmed = await swal.confirm({
            tone: 'warning',
            danger: true,
            title: 'Clear all notifications?',
            message: 'Every entry is removed from this device.',
            confirmLabel: 'Clear all',
            cancelLabel: 'Cancel',
        });

        if (!confirmed) {
            return;
        }

        setNotifications([]);
    };

    useEffect(() => {
        if (typeof window.Echo !== 'undefined') {
            const channel = window.Echo.private('lab.requests').listen(
                '.new.request',
                (e: LabRequestEvent) => {
                    addNotification({
                        id: `${e.consultation_id}-${Date.now()}`,
                        message: 'New Lab Request',
                        patient_name: e.patient_name,
                        doctor_name: e.doctor_name,
                        urgency: e.urgency,
                        type: 'lab',
                    });
                },
            );

            return () => channel.stopListening('.new.request');
        }
    }, [addNotification]);

    return {
        notifications,
        unreadCount,
        showToast,
        toastData,
        setShowToast,
        markAsRead,
        markAllRead,
        clearAll,
    };
}
