import {
    CheckIcon,
    KeyIcon,
    ShieldCheckIcon,
} from '@heroicons/react/24/outline';
import type { FormEvent } from 'react';
import {
    Badge,
    Button,
    Field,
    FieldSet,
    Input,
    SectionCard,
} from '@/Components/ui';
import { PermissionMatrix } from './PermissionMatrix';

export interface RoleFormData {
    name: string;
    permissions: string[];
}

interface RoleFormProps {
    data: RoleFormData;
    errors: Partial<Record<'name' | 'permissions', string>>;
    /** Every permission available on the platform. */
    permissions: string[];
    processing: boolean;
    setData: <K extends keyof RoleFormData>(
        key: K,
        value: RoleFormData[K],
    ) => void;
    onSubmit: (event: FormEvent<HTMLFormElement>) => void;
    cancelHref: string;
    cancelLabel: string;
    submitLabel: string;
    /** Disables the fields while an edit is in flight. */
    disabled?: boolean;
}

/**
 * RoleForm — the permission-matrix form behind both "Create role" and "Edit
 * role". Pages keep their own submit verb and redirect; this owns the fields,
 * the matrix and the sticky action row.
 */
export function RoleForm({
    data,
    errors,
    permissions,
    processing,
    setData,
    onSubmit,
    cancelHref,
    cancelLabel,
    submitLabel,
    disabled = false,
}: RoleFormProps) {
    const togglePermission = (permission: string) => {
        setData(
            'permissions',
            data.permissions.includes(permission)
                ? data.permissions.filter((name) => name !== permission)
                : [...data.permissions, permission],
        );
    };

    return (
        <form onSubmit={onSubmit} className="space-y-5">
            <SectionCard
                title="Role details"
                description="A short, stable name people will recognise in the UI."
                icon={ShieldCheckIcon}
            >
                <FieldSet columns={1}>
                    <Field
                        label="Role name"
                        htmlFor="role-name"
                        required
                        error={errors.name}
                        hint="Lowercase with dashes reads best, e.g. billing-manager."
                    >
                        <Input
                            id="role-name"
                            name="name"
                            value={data.name}
                            onChange={(event) =>
                                setData('name', event.target.value)
                            }
                            placeholder="e.g. Manager"
                            invalid={Boolean(errors.name)}
                            disabled={disabled}
                        />
                    </Field>
                </FieldSet>
            </SectionCard>

            <SectionCard
                title="Permissions"
                description="Select everything this role may do. Changes apply to its members immediately."
                icon={KeyIcon}
                actions={
                    <Badge
                        tone={data.permissions.length > 0 ? 'accent' : 'muted'}
                    >
                        {data.permissions.length} of {permissions.length}
                    </Badge>
                }
            >
                <Field error={errors.permissions}>
                    <PermissionMatrix
                        permissions={permissions}
                        selected={data.permissions}
                        onToggle={togglePermission}
                        disabled={disabled}
                    />
                </Field>
            </SectionCard>

            <div className="sticky bottom-0 z-10 -mx-1 flex flex-wrap items-center justify-end gap-2 rounded-lg border border-line bg-surface/95 px-3 py-2.5 backdrop-blur-sm">
                <Badge tone="muted" variant="outline">
                    {data.permissions.length} selected
                </Badge>
                <span className="hidden flex-1 sm:block" />
                <Button variant="secondary" href={cancelHref}>
                    {cancelLabel}
                </Button>
                <Button
                    type="submit"
                    icon={CheckIcon}
                    loading={processing}
                    disabled={disabled && !processing}
                >
                    {processing ? 'Saving…' : submitLabel}
                </Button>
            </div>
        </form>
    );
}
