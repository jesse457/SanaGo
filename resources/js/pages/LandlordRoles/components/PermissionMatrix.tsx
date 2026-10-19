import { KeyIcon } from '@heroicons/react/24/outline';
import { Checkbox } from '@/Components/ui';

interface PermissionMatrixProps {
    /** Every permission the platform knows about. */
    permissions: string[];
    /** Currently granted permission names. */
    selected: string[];
    onToggle: (permission: string) => void;
    disabled?: boolean;
}

/**
 * PermissionMatrix — the permission picker shared by the role create and edit
 * screens. A responsive grid of checkboxes where the granted tiles read as
 * accent, so a role's surface area is legible at a glance.
 */
export function PermissionMatrix({
    permissions,
    selected,
    onToggle,
    disabled = false,
}: PermissionMatrixProps) {
    if (permissions.length === 0) {
        return (
            <div className="flex items-center gap-2.5 rounded-lg border border-dashed border-line-strong bg-surface-muted px-3.5 py-4 text-[13px] text-ink-muted">
                <KeyIcon className="h-4 w-4 shrink-0 text-ink-subtle" />
                No permissions are seeded yet.
            </div>
        );
    }

    return (
        <div className="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
            {permissions.map((permission) => {
                const granted = selected.includes(permission);
                const inputId = `permission-${permission}`;

                return (
                    <label
                        key={permission}
                        htmlFor={inputId}
                        className={[
                            'group flex cursor-pointer items-start gap-2.5 rounded-lg border px-3 py-2.5',
                            'transition-[background-color,border-color] duration-200 ease-premium',
                            'focus-within:ring-2 focus-within:ring-accent/35',
                            granted
                                ? 'border-accent/35 bg-accent-soft'
                                : 'border-line bg-surface hover:border-line-strong hover:bg-surface-hover',
                            disabled ? 'cursor-not-allowed opacity-60' : '',
                        ].join(' ')}
                    >
                        <Checkbox
                            id={inputId}
                            checked={granted}
                            onChange={() => onToggle(permission)}
                            disabled={disabled}
                            className="mt-0"
                        />
                        <span className="min-w-0">
                            <span
                                className={[
                                    'block truncate text-[13px] font-medium tracking-tight',
                                    granted ? 'text-accent-ink' : 'text-ink',
                                ].join(' ')}
                            >
                                {permission}
                            </span>
                        </span>
                    </label>
                );
            })}
        </div>
    );
}
