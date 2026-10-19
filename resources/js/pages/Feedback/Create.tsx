import {
    CheckCircleIcon,
    StarIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { StarIcon as StarSolidIcon } from '@heroicons/react/24/solid';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import {
    Alert,
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
import { RatingScale } from './components/RatingScale';

export default function CreateFeedback() {
    const { t } = useTranslation();

    const { data, setData, post, processing, errors, reset } = useForm({
        subject: '',
        category: 'dashboard',
        message: '',
    });

    // Client-side only: the rating is not part of the submitted payload.
    const [rating, setRating] = useState(0);

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const categoryOptions = [
        { value: 'dashboard', label: t('dashboard') },
        { value: 'shift-management', label: t('shiftManagement') },
        { value: 'revenue-report', label: t('revenueReport') },
        { value: 'settings', label: t('settings') },
        { value: 'other', label: t('otherSystemIssues') },
    ];

    const submit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post('/feedback/store', {
            onSuccess: () => {
                setSuccessMessage(t('feedbackSubmittedSuccessfully'));
                reset();
                setRating(0);
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('submitFeedback')}
            headerSubtitle={t('shareYourThoughtsOrReportIssues')}
        >
            <Head title={t('submitFeedback')} />

            <div className="mx-auto w-full max-w-3xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    title={t('submitFeedback')}
                    eyebrow={t('feedbacks')}
                    description={t('shareYourThoughtsOrReportIssues')}
                    icon={StarIcon}
                    breadcrumbs={[
                        { label: t('home'), href: '/dashboard' },
                        { label: t('feedbacks'), href: '/feedback' },
                        { label: t('submitFeedback') },
                    ]}
                />

                {successMessage && (
                    <Alert
                        tone="positive"
                        title={t('success')}
                        action={
                            <Button
                                variant="ghost"
                                size="icon"
                                aria-label="Dismiss"
                                onClick={() => setSuccessMessage(null)}
                            >
                                <XMarkIcon className="h-4 w-4" />
                            </Button>
                        }
                    >
                        {successMessage}
                    </Alert>
                )}

                {(errorMessage || Object.keys(errors).length > 0) && (
                    <Alert
                        tone="critical"
                        title={t('error')}
                        action={
                            <Button
                                variant="ghost"
                                size="icon"
                                aria-label="Dismiss"
                                onClick={() => setErrorMessage(null)}
                            >
                                <XMarkIcon className="h-4 w-4" />
                            </Button>
                        }
                    >
                        {errorMessage || t('someFieldsRequireAttention')}
                    </Alert>
                )}

                <Card>
                    <CardContent className="py-5">
                        <form onSubmit={submit} className="space-y-6">
                            <FieldSet title={t('newFeedback')} columns={2}>
                                <Field
                                    label={t('subject')}
                                    htmlFor="subject"
                                    required
                                    error={errors.subject}
                                    className="sm:col-span-2"
                                >
                                    <Input
                                        id="subject"
                                        value={data.subject}
                                        onChange={(e) =>
                                            setData('subject', e.target.value)
                                        }
                                        placeholder={t(
                                            'briefTitleOfIssueRequest',
                                        )}
                                        invalid={Boolean(errors.subject)}
                                    />
                                </Field>

                                <Field
                                    label={t('category')}
                                    htmlFor="category"
                                    required
                                    error={errors.category}
                                >
                                    <Select
                                        id="category"
                                        value={data.category}
                                        onChange={(e) =>
                                            setData('category', e.target.value)
                                        }
                                        invalid={Boolean(errors.category)}
                                    >
                                        {categoryOptions.map((option) => (
                                            <option
                                                key={option.value}
                                                value={option.value}
                                            >
                                                {option.label}
                                            </option>
                                        ))}
                                    </Select>
                                </Field>

                                <RatingScale
                                    value={rating}
                                    onChange={setRating}
                                />

                                <Field
                                    label={t('message')}
                                    htmlFor="message"
                                    required
                                    error={errors.message}
                                    className="sm:col-span-2"
                                >
                                    <Textarea
                                        id="message"
                                        value={data.message}
                                        onChange={(e) =>
                                            setData('message', e.target.value)
                                        }
                                        placeholder={t(
                                            'describeYourExperienceInDetail',
                                        )}
                                        rows={6}
                                        className="resize-none"
                                        invalid={Boolean(errors.message)}
                                    />
                                </Field>
                            </FieldSet>

                            <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                                <Button
                                    type="button"
                                    variant="secondary"
                                    size="sm"
                                    onClick={() => {
                                        reset();
                                        setRating(0);
                                    }}
                                >
                                    {t('startOver')}
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
                                        ? `${t('sending')}…`
                                        : t('sendFeedback')}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <p className="flex items-center justify-center gap-1.5 text-[12px] text-ink-subtle">
                    <StarSolidIcon className="h-3.5 w-3.5" aria-hidden="true" />
                    Thanks for helping us make SanaGo better.
                </p>
            </div>
        </AppLayout>
    );
}
