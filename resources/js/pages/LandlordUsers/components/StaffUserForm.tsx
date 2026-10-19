import {
    CheckIcon,
    EnvelopeIcon,
    LockClosedIcon,
    ShieldCheckIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import type { FormEvent, ReactNode } from 'react';
import {
    Button,
    Field,
    FieldSet,
    Input,
    SectionCard,
    Select,
} from '@/Components/ui';

export interface StaffUserFormData {
    name: string;
    email: string;
    password: string;
    role: string;
}

interface StaffUserFormProps {
    data: StaffUserFormData;
    errors: Partial<Record<keyof StaffUserFormData, string>>;
    /** Selectable role names (spatie role names). */
    roles: string[];
    processing: boolean;
    /** Create keeps the password required; edit treats it as "leave blank to keep". */
    mode: 'create' | 'edit';
    setData: <K extends keyof StaffUserFormData>(
        key: K,
        value: StaffUserFormData[K],
    ) => void;
    onSubmit: (event: FormEvent<HTMLFormElement>) => void;
    cancelHref: string;
    cancelLabel: string;
    submitLabel: string;
    submitIcon?: React.ComponentType<React.SVGProps<SVGSVGElement>>;
    /** Extra fields rendered above the standard ones (kept for future growth). */
    children?: ReactNode;
}

/**
 * StaffUserForm — the one identity form behind both "Add staff user" and "Edit
 * staff user". The two pages own their own `useForm` state, submit verb and
 * redirect; this only renders the fields and the action row.
 */
export function StaffUserForm({
    data,
    errors,
    roles,
    processing,
    mode,
    setData,
    onSubmit,
    cancelHref,
    cancelLabel,
    submitLabel,
    submitIcon,
    children,
}: StaffUserFormProps) {
    const passwordHint =
        mode === 'edit'
            ? 'Leave blank to keep the current password.'
            : 'At least 8 characters.';

    return (
        <form onSubmit={onSubmit} className="space-y-5">
            <SectionCard
                title="User details"
                description="Identity and access for this staff account."
                icon={UserIcon}
            >
                <div className="space-y-5">
                    {children}

                    <FieldSet columns={2}>
                        <Field
                            label="Full name"
                            htmlFor="name"
                            required
                            error={errors.name}
                            className="sm:col-span-2"
                        >
                            <Input
                                id="name"
                                name="name"
                                value={data.name}
                                onChange={(event) =>
                                    setData('name', event.target.value)
                                }
                                placeholder="e.g. John Doe"
                                invalid={Boolean(errors.name)}
                                autoComplete="name"
                            />
                        </Field>

                        <Field
                            label="Email"
                            htmlFor="email"
                            required
                            error={errors.email}
                            className="sm:col-span-2"
                        >
                            <Input
                                id="email"
                                name="email"
                                type="email"
                                icon={EnvelopeIcon}
                                value={data.email}
                                onChange={(event) =>
                                    setData('email', event.target.value)
                                }
                                placeholder="name@hospital.com"
                                invalid={Boolean(errors.email)}
                                autoComplete="email"
                            />
                        </Field>

                        <Field
                            label={
                                mode === 'edit' ? 'New password' : 'Password'
                            }
                            htmlFor="password"
                            required={mode === 'create'}
                            optional={mode === 'edit'}
                            hint={passwordHint}
                            error={errors.password}
                        >
                            <Input
                                id="password"
                                name="password"
                                type="password"
                                icon={LockClosedIcon}
                                value={data.password}
                                onChange={(event) =>
                                    setData('password', event.target.value)
                                }
                                placeholder="••••••••"
                                invalid={Boolean(errors.password)}
                                autoComplete="new-password"
                            />
                        </Field>

                        <Field
                            label="Role"
                            htmlFor="role"
                            required
                            error={errors.role}
                        >
                            <Select
                                id="role"
                                name="role"
                                value={data.role}
                                onChange={(event) =>
                                    setData('role', event.target.value)
                                }
                                invalid={Boolean(errors.role)}
                            >
                                {roles.map((role) => (
                                    <option key={role} value={role}>
                                        {role}
                                    </option>
                                ))}
                            </Select>
                        </Field>
                    </FieldSet>
                </div>
            </SectionCard>

            <div className="flex flex-wrap items-center justify-end gap-2">
                <Button variant="secondary" href={cancelHref}>
                    {cancelLabel}
                </Button>
                <Button
                    type="submit"
                    icon={submitIcon ?? ShieldCheckIcon}
                    loading={processing}
                >
                    {processing ? 'Saving…' : submitLabel}
                </Button>
            </div>
        </form>
    );
}

/** Icon re-export so pages share the same submit affordance. */
export const StaffUserSubmitIcon = CheckIcon;
