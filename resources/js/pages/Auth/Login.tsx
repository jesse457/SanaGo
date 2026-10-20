import { Head, Link, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Alert, Button, Checkbox, Field, Input } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { AuthShell } from '@/pages/Auth/components/AuthShell';
import { PasswordInput } from '@/pages/Auth/components/PasswordInput';

interface LoginProps {
    /**
     * True when the login page is served from the platform domain rather than
     * a hospital subdomain. Platform visitors must say which hospital they
     * belong to (or sign in as SanaGo staff).
     */
    isCentralDomain: boolean;
    tenantName?: string | null;
}

const Login = ({ isCentralDomain = false, tenantName = null }: LoginProps) => {
    const { t } = useTranslation();
    const { props } = usePage<{ platform?: { name?: string } }>();
    const platformName = props.platform?.name ?? 'SanaGo';

    const { data, setData, post, processing, errors, transform } = useForm({
        email: '',
        password: '',
        remember: false,
        type: isCentralDomain ? 'landlord' : 'tenant',
        subdomain: '',
    });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();

        // A subdomain typed on the platform domain switches the request to the
        // hospital flow; hospital subdomains are always tenant sign-ins.
        transform((form) => ({
            ...form,
            type:
                isCentralDomain && form.subdomain.trim() === ''
                    ? 'landlord'
                    : 'tenant',
            subdomain: isCentralDomain ? form.subdomain.trim() : '',
        }));

        post('/login');
    };

    const errorMessage =
        errors.email ?? errors.password ?? errors.subdomain ?? errors.type;

    return (
        <>
            <Head title={`Sign in · ${platformName}`} />

            <AuthShell
                title={
                    isCentralDomain
                        ? `Sign in to ${platformName}`
                        : (tenantName ?? t('auth.login.title'))
                }
                description={
                    isCentralDomain
                        ? 'Hospital staff enter their subdomain. SanaGo staff sign in without one.'
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

                    {isCentralDomain && (
                        <Field
                            label="Hospital subdomain"
                            htmlFor="subdomain"
                            hint="Leave empty to sign in as SanaGo staff."
                            error={errors.subdomain}
                        >
                            <Input
                                id="subdomain"
                                name="subdomain"
                                autoComplete="organization"
                                value={data.subdomain}
                                onChange={(event) =>
                                    setData('subdomain', event.target.value)
                                }
                                placeholder="city-hospital"
                                invalid={Boolean(errors.subdomain)}
                            />
                        </Field>
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
