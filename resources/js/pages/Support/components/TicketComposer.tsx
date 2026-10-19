import { PaperAirplaneIcon } from '@heroicons/react/24/outline';
import type { FormEvent, ReactNode } from 'react';
import { Button, Textarea } from '@/Components/ui';

interface TicketComposerProps {
    value: string;
    onChange: (value: string) => void;
    onSubmit: (event: FormEvent) => void;
    processing?: boolean;
    placeholder?: string;
    /** Optional left-hand control row (e.g. an internal-note toggle). */
    options?: ReactNode;
    submitLabel?: string;
    required?: boolean;
}

/**
 * TicketComposer — the reply box at the foot of a conversation. Textarea plus
 * one primary send action, matching the form footer across the app.
 */
export function TicketComposer({
    value,
    onChange,
    onSubmit,
    processing = false,
    placeholder = 'Write a reply…',
    options,
    submitLabel = 'Send',
    required = false,
}: TicketComposerProps) {
    return (
        <form onSubmit={onSubmit} className="space-y-2.5">
            <Textarea
                value={value}
                onChange={(event) => onChange(event.target.value)}
                rows={3}
                placeholder={placeholder}
                required={required}
                className="resize-none"
            />
            <div className="flex items-center justify-between gap-3">
                <div className="min-w-0">{options}</div>
                <Button
                    type="submit"
                    size="sm"
                    icon={PaperAirplaneIcon}
                    loading={processing}
                >
                    {submitLabel}
                </Button>
            </div>
        </form>
    );
}
