import {
    ChatBubbleLeftEllipsisIcon,
    PaperAirplaneIcon,
} from '@heroicons/react/24/outline';
import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Button,
    EmptyState,
    Field,
    SectionCard,
    Textarea,
} from '@/Components/ui';
import { formatDateTime } from '@/pages/Platform/components/format';
import type { TenantNote } from '@/pages/Platform/components/types';

interface NotesPanelProps {
    tenantId: string;
    notes: TenantNote[];
    canManage: boolean;
}

/**
 * Internal notes — the operator's memory about a hospital (why it was
 * discounted, who to call, what was promised). Posted to `tenants.add-note`.
 */
export function NotesPanel({ tenantId, notes, canManage }: NotesPanelProps) {
    const { data, setData, post, processing, errors, reset } = useForm({
        note: '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();

        if (data.note.trim() === '') {
            return;
        }

        post(`/tenants/${tenantId}/notes`, {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    return (
        <SectionCard
            title="Internal notes"
            description="Visible only to platform staff — never to the hospital."
            icon={ChatBubbleLeftEllipsisIcon}
        >
            <div className="space-y-4">
                {notes.length === 0 ? (
                    <EmptyState
                        panel={false}
                        icon={ChatBubbleLeftEllipsisIcon}
                        title="No notes yet"
                        description="Record context that the next operator will need."
                    />
                ) : (
                    <ol className="space-y-3">
                        {notes.map((note) => (
                            <li
                                key={note.id}
                                className="rounded-lg border border-line bg-surface-muted px-3.5 py-3"
                            >
                                <p className="text-[13px] leading-relaxed whitespace-pre-wrap text-ink">
                                    {note.note}
                                </p>
                                <p className="mt-1.5 flex flex-wrap items-center gap-x-2 text-[11.5px] text-ink-subtle">
                                    <span className="font-medium text-ink-muted">
                                        {note.user_name ?? 'Platform staff'}
                                    </span>
                                    <span aria-hidden="true">·</span>
                                    <span className="tabular-nums">
                                        {formatDateTime(note.created_at)}
                                    </span>
                                </p>
                            </li>
                        ))}
                    </ol>
                )}

                {canManage && (
                    <form
                        onSubmit={submit}
                        className="space-y-3 border-t border-line pt-4"
                    >
                        <Field
                            label="Add a note"
                            htmlFor="tenant-note"
                            error={errors.note}
                        >
                            <Textarea
                                id="tenant-note"
                                name="note"
                                rows={3}
                                value={data.note}
                                maxLength={2000}
                                placeholder="Called the administrator about the unpaid invoice…"
                                onChange={(event) =>
                                    setData('note', event.target.value)
                                }
                                invalid={Boolean(errors.note)}
                            />
                        </Field>

                        <div className="flex items-center justify-between gap-3">
                            <span className="text-[11.5px] text-ink-subtle tabular-nums">
                                {data.note.length} / 2000
                            </span>
                            <Button
                                type="submit"
                                size="sm"
                                icon={PaperAirplaneIcon}
                                loading={processing}
                                disabled={data.note.trim() === ''}
                            >
                                Add note
                            </Button>
                        </div>
                    </form>
                )}
            </div>
        </SectionCard>
    );
}
