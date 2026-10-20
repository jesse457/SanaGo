import { Head, Link, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Alert, Button, Checkbox, Field, Input } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { AuthShell } from '@/pages/Auth/components/AuthShell';
import { PasswordInput } from '@/pages/Auth/components/PasswordInput';

interface LoginProps {
    /** Name of the hospital when the page is served from a hospital domain. */
    tenantName?: string | null;
    isCentralDomain?: boolean;
}

/**
 * One sign-in screen for both audiences.
 *
 * The host already says who you are: a hospital subdomain signs that
 * hospital's staff in, the platform domain signs SanaGo staff in. Nobody is
 * asked to type their own domain.
 */
const Login = ({ tenantName = null, isCentralDomain = false }: LoginProps) => {
    const { t } = useTranslation();
    const { props } = usePage<{ platform?: { name?: string } }>();
    const platformName = props.platform?.name ?? 'SanaGo';

    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        post('/login');
    };

    const errorMessage = errors.email ?? errors.password;

    return (
        <>
            <Head title={`Sign in · ${tenantName ?? platformName}`} />

            <AuthShell
                title={tenantName ?? `Sign in to ${platformName}`}
                description={
                    isCentralDomain
                        ? 'Platform staff sign in here. Hospital staff sign in from their own hospital address.'
                        : t('auth.login.subtitle')
                }
                footer={
                    <span className="text-ink-subtle">
                        Need help signing in? Contact your hospital
                        administrator.
                    </span>
                }
            >
                <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                    {errorMessage && (
                        <Alert tone="critical" title={errorMessage} />
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
                            autoFocus
                        />
                    </Field>

                    <Field
                        label={t('auth.login.password_label')}
                        htmlFor="password"
                        required
                        error={errors.password}
                        labelAction={
                            <Link
                                href="/forgot-password"
                                className="text-[11px] font-medium text-accent-ink transition-colors duration-150 hover:text-ink"
                            >
                                {t('auth.login.forgot_password')}
                            </Link>
                        }
                    >
                        <PasswordInput
                            id="password"
                            name="password"
                            autoComplete="current-password"
                            value={data.password}
                            onChange={(event) =>
                                setData('password', event.target.value)
                            }
                            placeholder="••••••••"
                            invalid={Boolean(errors.password)}
                            required
                        />
                    </Field>

                    <Checkbox
                        id="remember"
                        label={t('auth.login.remember_me')}
                        checked={data.remember}
                        onChange={(event) =>
                            setData('remember', event.target.checked)
                        }
                    />

                    <Button
                        type="submit"
                        size="lg"
                        fullWidth
                        loading={processing}
                        className="mt-1"
                    >
                        {processing
                            ? t('auth.login.submitting')
                            : t('auth.login.submit')}
                    </Button>
                </form>
            </AuthShell>
        </>
    );
};

export default Login;
