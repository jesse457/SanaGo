import {
    BuildingOffice2Icon,
    EnvelopeIcon,
    PencilSquareIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Alert,
    Button as UiButton,
    Field,
    Input,
    Textarea,
} from '@/Components/ui';
import { cn } from '@/lib/utils';

interface DemoRequestFormProps {
    className?: string;
}

/**
 * DemoRequestForm — the contact page's one conversion form.
 *
 * Composed from `Field` / `Input` / `Textarea` / `Alert` so labels, control
 * geometry, focus rings and validation tone match the rest of the product.
 * The `useForm` payload, endpoint and reset-on-success behaviour are unchanged.
 */
export default function DemoRequestForm({ className }: DemoRequestFormProps) {
    const { data, setData, post, processing, errors, wasSuccessful, reset } =
        useForm({
            name: '',
            email: '',
            company: '',
            message: '',
        });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post('/contact', {
            onSuccess: () => reset(),
        });
    };

    return (
        <div
            className={cn(
                'rounded-2xl border border-line bg-surface p-6 sm:p-8',
                className,
            )}
        >
            {wasSuccessful && (
                <Alert tone="positive" className="mb-6">
                    Thank you for your interest. We will be in touch shortly.
                </Alert>
            )}

            <form onSubmit={handleSubmit} className="space-y-5">
                <div className="grid gap-5 sm:grid-cols-2">
                    <Field
                        label="Full name"
                        htmlFor="name"
                        required
                        error={errors.name}
                    >
                        <Input
                            id="name"
                            name="name"
                            type="text"
                            icon={UserIcon}
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder="Dr. Aïcha Ngo Bassong"
                            autoComplete="name"
                            invalid={Boolean(errors.name)}
                            required
                        />
                    </Field>

                    <Field
                        label="Email address"
                        htmlFor="email"
                        required
                        error={errors.email}
                    >
                        <Input
                            id="email"
                            name="email"
                            type="email"
                            icon={EnvelopeIcon}
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="a.ngo@clinique-bastos.cm"
                            autoComplete="email"
                            invalid={Boolean(errors.email)}
                            required
                        />
                    </Field>
                </div>

                <Field
                    label="Hospital or company"
                    htmlFor="company"
                    optional
                    error={errors.company}
                >
                    <Input
                        id="company"
                        name="company"
                        type="text"
                        icon={BuildingOffice2Icon}
                        value={data.company}
                        onChange={(e) => setData('company', e.target.value)}
                        placeholder="Clinique de la Cathédrale"
                        autoComplete="organization"
                        invalid={Boolean(errors.company)}
                    />
                </Field>

                <Field
                    label="Message"
                    htmlFor="message"
                    optional
                    error={errors.message}
                    hint="Tell us about your facility size and which modules matter most."
                >
                    <Textarea
                        id="message"
                        name="message"
                        rows={4}
                        value={data.message}
                        onChange={(e) => setData('message', e.target.value)}
                        placeholder="Wards, beds and which of the desks above hurt most today."
                        invalid={Boolean(errors.message)}
                    />
                </Field>

                <div className="flex flex-col gap-3 border-t border-line pt-5 sm:flex-row sm:items-center sm:justify-between">
                    <p className="flex items-center gap-1.5 text-[12px] text-ink-subtle">
                        <PencilSquareIcon className="h-3.5 w-3.5" />
                        We reply within one business day.
                    </p>
                    <UiButton
                        type="submit"
                        variant="accent"
                        size="lg"
                        loading={processing}
                        className="w-full justify-center sm:w-auto"
                    >
                        {processing ? 'Sending' : 'Request a demo'}
                    </UiButton>
                </div>
            </form>
        </div>
    );
}
