import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Alert, Button, Field, Input } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { AuthShell } from '@/pages/Auth/components/AuthShell';
import { PasswordInput } from '@/pages/Auth/components/PasswordInput';

interface ResetProps {
    token: string;
    email: string;
}

const ResetPassword = ({ token, email }: ResetProps) => {
    const { t } = useTranslation();

    const { data, setData, post, processing, errors } = useForm({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        post('/reset-password');
    };

    const errorMessage =
        errors.password ?? errors.password_confirmation ?? errors.email;

    return (
        <>
            <Head title="Reset Password | Yerli HMS" />

            <AuthShell
                title={t('auth.confirm_password.title')}
                description="Create a new password for your account."
                backLink={{ label: 'Back to sign in', href: '/login' }}
            >
                <form onSubmit={submit} className="flex flex-col gap-4">
                    {errorMessage && (
                        <Alert tone="critical" title={errorMessage} />
                    )}

                    <Field label="Account email" htmlFor="account-email">
                        <Input
                            id="account-email"
                            type="email"
                            value={data.email}
                            readOnly
                            disabled
                        />
                    </Field>

                    <Field
                        label="New password"
                        htmlFor="password"
                        required
                        error={errors.password}
                        hint="Use at least 8 characters."
                    >
                        <PasswordInput
                            id="password"
                            name="password"
                            autoComplete="new-password"
                            value={data.password}
                            onChange={(event) =>
                                setData('password', event.target.value)
                            }
                            placeholder="••••••••"
                            invalid={Boolean(errors.password)}
                            required
                        />
                    </Field>

                    <Field
                        label="Confirm new password"
                        htmlFor="password-confirmation"
                        required
                        error={errors.password_confirmation}
                    >
                        <PasswordInput
                            id="password-confirmation"
                            name="password_confirmation"
                            autoComplete="new-password"
                            value={data.password_confirmation}
                            onChange={(event) =>
                                setData(
                                    'password_confirmation',
                                    event.target.value,
                                )
                            }
                            placeholder="••••••••"
                            invalid={Boolean(errors.password_confirmation)}
                            required
                        />
                    </Field>

                    <Button
                        type="submit"
                        size="lg"
                        fullWidth
                        loading={processing}
                        className="mt-1"
                    >
                        {processing ? 'Updating…' : 'Update password'}
                    </Button>
                </form>
            </AuthShell>
        </>
    );
};

export default ResetPassword;
