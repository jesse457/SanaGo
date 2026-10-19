import { ExclamationTriangleIcon } from '@heroicons/react/24/outline';
import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Button, Dialog } from '@/Components/ui';

export interface DeletableUser {
    id: number;
    name: string;
}

interface DeleteUserDialogProps {
    /** The user queued for deletion; `null` closes the dialog. */
    user: DeletableUser | null;
    onClose: () => void;
}

/**
 * DeleteUserDialog — the confirmation step shared by the staff-user screens.
 */
export function DeleteUserDialog({ user, onClose }: DeleteUserDialogProps) {
    const { delete: destroy, processing } = useForm();

    const handleDelete = (event: FormEvent) => {
        event.preventDefault();

        if (user) {
            destroy(`/landlord-users/${user.id}`, {
                onSuccess: () => onClose(),
            });
        }
    };

    return (
        <Dialog
            open={user !== null}
            onClose={onClose}
            title="Delete user"
            size="md"
            footer={
                <>
                    <Button variant="secondary" onClick={onClose}>
                        Cancel
                    </Button>
                    <Button
                        variant="danger"
                        loading={processing}
                        onClick={handleDelete}
                    >
                        {processing ? 'Deleting…' : 'Delete'}
                    </Button>
                </>
            }
        >
            <div className="flex items-start gap-3">
                <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-critical-soft text-critical-ink">
                    <ExclamationTriangleIcon className="h-[18px] w-[18px]" />
                </span>
                <div className="min-w-0">
                    <p className="text-[13px] leading-relaxed text-ink">
                        Delete{' '}
                        <strong className="font-semibold">{user?.name}</strong>{' '}
                        permanently?
                    </p>
                    <p className="mt-1 text-[12.5px] leading-relaxed text-ink-muted">
                        This staff account loses access immediately. This cannot
                        be undone.
                    </p>
                </div>
            </div>
        </Dialog>
    );
}
