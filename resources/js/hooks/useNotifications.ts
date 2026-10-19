import { useState, useEffect, useCallback } from 'react';
import type { Notification, LabRequestEvent } from '@/types';

export function useNotifications() {
    const [notifications, setNotifications] = useState<Notification[]>(() => {
        const stored = localStorage.getItem('lab_notifications');

        return stored ? JSON.parse(stored) : [];
    });
    const [showToast, setShowToast] = useState<boolean>(false);
    const [toastData, setToastData] = useState<Notification | null>(null);

    const unreadCount = notifications.filter((n) => !n.read).length;

    const saveToStorage = (data: Notification[]) => {
        localStorage.setItem('lab_notifications', JSON.stringify(data));
    };

    const addNotification = useCallback(
        (data: Omit<Notification, 'created_at' | 'read'>) => {
            setNotifications((prev) => {
                if (prev.some((n) => n.id === data.id)) {
                    return prev;
                }

                const newNotif: Notification = {
                    ...data,
                    created_at: new Date().toISOString(),
                    read: false,
                };
                const updated = [newNotif, ...prev].slice(0, 50);
                saveToStorage(updated);

                return updated;
            });

            setToastData({
                ...data,
                created_at: new Date().toISOString(),
                read: false,
            });
            setShowToast(true);
            setTimeout(() => setShowToast(false), 8000);
        },
        [],
    );

    const markAsRead = (id: string) => {
        setNotifications((prev) => {
            const updated = prev.map((n) =>
                n.id === id ? { ...n, read: true } : n,
            );
            saveToStorage(updated);

            return updated;
        });
    };

    const markAllRead = () => {
        setNotifications((prev) => {
            const updated = prev.map((n) => ({ ...n, read: true }));
            saveToStorage(updated);

            return updated;
        });
    };

    const clearAll = () => {
        if (confirm('Clear all notifications?')) {
            setNotifications([]);
            localStorage.removeItem('lab_notifications');
        }
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
