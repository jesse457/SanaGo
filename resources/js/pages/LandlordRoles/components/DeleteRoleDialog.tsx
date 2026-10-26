import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Alert, Button, Dialog } from '@/Components/ui';

export interface DeletableRole {
    id: number;
    name: string;
    users_count?: number;
}

interface DeleteRoleDialogProps {
    /** The role queued for deletion; `null` closes the dialog. */
    role: DeletableRole | null;
    onClose: () => void;
}

/**
 * DeleteRoleDialog — the confirmation step shared by the landlord role screens.
 */
export function DeleteRoleDialog({ role, onClose }: DeleteRoleDialogProps) {
    const { delete: destroy, processing } = useForm();

    const handleDelete = (event: FormEvent) => {
        event.preventDefault();

        if (role) {
            destroy(`/landlord-roles/${role.id}`, {
                onSuccess: () => onClose(),
            });
        }
    };

    return (
        <Dialog
            open={role !== null}
            onClose={onClose}
            title="Delete role"
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
            {/* Alert owns the danger treatment; the dialog only supplies copy. */}
            <Alert tone="critical" title={`Delete role ${role?.name ?? ''}?`}>
                {(role?.users_count ?? 0) > 0
                    ? `${role?.users_count} staff members currently hold this role.`
                    : 'This cannot be undone.'}
            </Alert>
        </Dialog>
    );
}
