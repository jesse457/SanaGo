import { CheckCircleIcon, TicketIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Button,
    Card,
    CardContent,
    Field,
    FieldSet,
    Input,
    PageHeader,
    Select,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

export default function SupportCreate() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } = useForm({
        subject: '',
        priority: 'medium',
        message: '',
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post('/support', {
            onSuccess: () => {
                reset();
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('support.newTicket')}
            headerSubtitle={t('support.reportIssue')}
        >
            <Head title={t('support.newTicket')} />

            <div className="mx-auto w-full max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageHeader
                    title={t('support.newTicket')}
                    eyebrow={t('support.title')}
                    description={t('support.reportIssue')}
                    icon={TicketIcon}
                    breadcrumbs={[
                        { label: t('support.title'), href: '/support' },
                        { label: t('support.newTicket') },
                    ]}
                />

                <Card>
                    <CardContent className="py-5">
                        <form onSubmit={submit} className="space-y-6">
                            <FieldSet
                                title={t('support.ticketDetails')}
                                columns={1}
                            >
                                <Field
                                    label={t('support.subject')}
                                    htmlFor="subject"
                                    required
                                    error={errors.subject}
                                >
                                    <Input
                                        id="subject"
                                        value={data.subject}
                                        onChange={(e) =>
                                            setData('subject', e.target.value)
                                        }
                                        placeholder={t(
                                            'support.subjectPlaceholder',
                                        )}
                                        invalid={Boolean(errors.subject)}
                                    />
                                </Field>

                                <Field
                                    label={t('support.priority')}
                                    htmlFor="priority"
                                    required
                                    error={errors.priority}
                                >
                                    <Select
                                        id="priority"
                                        value={data.priority}
                                        onChange={(e) =>
                                            setData('priority', e.target.value)
                                        }
                                        invalid={Boolean(errors.priority)}
                                    >
                                        <option value="low">
                                            {t('support.low')}
                                        </option>
                                        <option value="medium">
                                            {t('support.medium')}
                                        </option>
                                        <option value="high">
                                            {t('support.high')}
                                        </option>
                                        <option value="urgent">
                                            {t('support.urgent')}
                                        </option>
                                    </Select>
                                </Field>

                                <Field
                                    label={t('support.message')}
                                    htmlFor="message"
                                    required
                                    error={errors.message}
                                >
                                    <Textarea
                                        id="message"
                                        value={data.message}
                                        onChange={(e) =>
                                            setData('message', e.target.value)
                                        }
                                        rows={6}
                                        placeholder={t(
                                            'support.messagePlaceholder',
                                        )}
                                        invalid={Boolean(errors.message)}
                                    />
                                </Field>
                            </FieldSet>

                            <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                                <Button
                                    href="/support"
                                    variant="secondary"
                                    size="sm"
                                >
                                    {t('common.cancel')}
                                </Button>
                                <Button
                                    type="submit"
                                    size="sm"
                                    icon={
                                        processing ? undefined : CheckCircleIcon
                                    }
                                    loading={processing}
                                >
                                    {processing
                                        ? `${t('support.submitting')}…`
                                        : t('support.submitTicket')}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
