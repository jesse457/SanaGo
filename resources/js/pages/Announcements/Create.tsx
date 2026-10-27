import { MegaphoneIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Button,
    Card,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SectionCard,
    Select,
    Switch,
    Textarea,
} from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

export default function AnnouncementsCreate() {
    const { data, setData, post, processing, errors, reset } = useForm({
        title: '',
        body: '',
        type: 'info',
        is_published: false,
        expires_at: '',
    });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        post('/announcements', {
            onSuccess: () => reset(),
        });
    };

    return (
        <SuperAdminLayout
            headerTitle="New Announcement"
            headerSubtitle="Create a system-wide announcement"
        >
            <Head title="New Announcement" />

            <form onSubmit={handleSubmit} className="max-w-3xl space-y-6 pb-12">
                <PageHeader
                    title="New announcement"
                    description="Compose a message that appears on every tenant's dashboard."
                    breadcrumbs={[
                        { label: 'Announcements', href: '/announcements' },
                        { label: 'New announcement' },
                    ]}
                />

                <SectionCard
                    title="Announcement details"
                    description="Title, message and how long it stays visible."
                    icon={MegaphoneIcon}
                >
                    <div className="flex flex-col gap-5">
                        <Field
                            label="Title"
                            htmlFor="title"
                            required
                            error={errors.title}
                        >
                            <Input
                                id="title"
                                name="title"
                                value={data.title}
                                onChange={(event) =>
                                    setData('title', event.target.value)
                                }
                                invalid={Boolean(errors.title)}
                                placeholder="Scheduled maintenance this weekend"
                                required
                            />
                        </Field>

                        <Field
                            label="Body"
                            htmlFor="body"
                            required
                            error={errors.body}
                        >
                            <Textarea
                                id="body"
                                name="body"
                                rows={5}
                                value={data.body}
                                onChange={(event) =>
                                    setData('body', event.target.value)
                                }
                                invalid={Boolean(errors.body)}
                                placeholder="Explain what is changing and what tenants need to do."
                                required
                            />
                        </Field>

                        <FieldSet columns={2}>
                            <Field
                                label="Type"
                                htmlFor="type"
                                error={errors.type}
                            >
                                <Select
                                    id="type"
                                    name="type"
                                    value={data.type}
                                    onChange={(event) =>
                                        setData('type', event.target.value)
                                    }
                                    invalid={Boolean(errors.type)}
                                >
                                    <option value="info">Info</option>
                                    <option value="warning">Warning</option>
                                    <option value="success">Success</option>
                                </Select>
                            </Field>

                            <Field
                                label="Expires at"
                                htmlFor="expires_at"
                                error={errors.expires_at}
                                hint="Leave empty to keep it visible until removed."
                            >
                                <Input
                                    id="expires_at"
                                    name="expires_at"
                                    type="date"
                                    value={data.expires_at}
                                    onChange={(event) =>
                                        setData(
                                            'expires_at',
                                            event.target.value,
                                        )
                                    }
                                    invalid={Boolean(errors.expires_at)}
                                />
                            </Field>
                        </FieldSet>

                        <Card className="bg-surface-muted p-4">
                            <Switch
                                id="is_published"
                                label="Publish immediately"
                                description="Tenants see the announcement as soon as it is saved."
                                checked={data.is_published}
                                onChange={(event) =>
                                    setData(
                                        'is_published',
                                        event.target.checked,
                                    )
                                }
                            />
                        </Card>
                    </div>
                </SectionCard>

                <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-5">
                    <Button variant="secondary" href="/announcements">
                        Cancel
                    </Button>
                    <Button type="submit" loading={processing}>
                        {processing ? 'Creating…' : 'Create announcement'}
                    </Button>
                </div>
            </form>
        </SuperAdminLayout>
    );
}
