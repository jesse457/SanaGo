import { Head, router, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Alert, Button, Field, Input } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { AuthShell } from '@/pages/Auth/components/AuthShell';

const ForgotPassword = () => {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, wasSuccessful } = useForm({
        email: '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        post('/forgot-password');
    };

    return (
        <>
            <Head title="Forgot Password | Yerli HMS" />

            <AuthShell
                title={t('auth.forgot_password.title')}
                description="Enter the email associated with your account and we'll send a reset link."
                backLink={{ label: 'Back to sign in', href: '/login' }}
            >
                {wasSuccessful ? (
                    <div className="flex flex-col gap-4">
                        <Alert tone="positive" title="Check your email">
                            We've sent a password reset link to{' '}
                            <span className="font-semibold text-ink">
                                {data.email}
                            </span>
                            .
                        </Alert>

                        <Button
                            variant="secondary"
                            fullWidth
                            onClick={() => router.reload()}
                        >
                            Didn't receive the email? Try again
                        </Button>
                    </div>
                ) : (
                    <form onSubmit={submit} className="flex flex-col gap-4">
                        {errors.email && (
                            <Alert tone="critical" title={errors.email} />
                        )}

                        <Field
                            label={t('auth.login.email_label')}
                            htmlFor="email"
                            required
                            error={errors.email}
                        >
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                autoComplete="username"
                                value={data.email}
                                onChange={(event) =>
                                    setData('email', event.target.value)
                                }
                                placeholder="name@example.com"
                                invalid={Boolean(errors.email)}
                                required
                            />
                        </Field>

                        <Button
                            type="submit"
                            size="lg"
                            fullWidth
                            loading={processing}
                        >
                            {processing ? 'Sending…' : 'Send reset link'}
                        </Button>
                    </form>
                )}
            </AuthShell>
        </>
    );
};

export default ForgotPassword;
