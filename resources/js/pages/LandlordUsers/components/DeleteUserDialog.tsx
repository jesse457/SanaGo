import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Alert, Button, Dialog } from '@/Components/ui';

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
            <Alert
                tone="critical"
                title={`Delete ${user?.name ?? ''} permanently?`}
            >
                This staff account loses access immediately. This cannot be
                undone.
            </Alert>
        </Dialog>
    );
}
