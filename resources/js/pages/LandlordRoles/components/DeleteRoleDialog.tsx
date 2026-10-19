import { ExclamationTriangleIcon } from '@heroicons/react/24/outline';
import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Button, Dialog } from '@/Components/ui';

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
            <div className="flex items-start gap-3">
                <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-critical-soft text-critical-ink">
                    <ExclamationTriangleIcon className="h-[18px] w-[18px]" />
                </span>
                <div className="min-w-0">
                    <p className="text-[13px] leading-relaxed text-ink">
                        Delete role{' '}
                        <strong className="font-semibold">{role?.name}</strong>?
                    </p>
                    <p className="mt-1 text-[12.5px] leading-relaxed text-ink-muted">
                        {(role?.users_count ?? 0) > 0
                            ? `${role?.users_count} staff members currently hold this role.`
                            : 'This cannot be undone.'}
                    </p>
                </div>
            </div>
        </Dialog>
    );
}
