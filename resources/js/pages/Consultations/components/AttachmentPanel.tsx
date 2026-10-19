import {
    ArrowUpTrayIcon,
    DocumentTextIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { useRef } from 'react';
import { Badge, Button, EmptyState, SectionCard } from '@/Components/ui';
import type { Translate } from './types';

interface AttachmentPanelProps {
    files: File[];
    onAdd: (files: File[]) => void;
    onRemove: (index: number) => void;
    t: Translate;
}

/**
 * AttachmentPanel — the files tab: a click-or-drop target plus the queue of
 * uploads waiting to be posted with the consultation.
 */
export function AttachmentPanel({
    files,
    onAdd,
    onRemove,
    t,
}: AttachmentPanelProps) {
    const inputRef = useRef<HTMLInputElement>(null);

    return (
        <>
            <SectionCard icon={ArrowUpTrayIcon} title={t('attachments')}>
                <div
                    role="button"
                    tabIndex={0}
                    onClick={() => inputRef.current?.click()}
                    onKeyDown={(event) => {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            inputRef.current?.click();
                        }
                    }}
                    onDragOver={(event) => event.preventDefault()}
                    onDrop={(event) => {
                        event.preventDefault();

                        if (event.dataTransfer.files) {
                            onAdd(Array.from(event.dataTransfer.files));
                        }
                    }}
                    className="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-line-strong bg-surface-muted/60 px-6 py-10 text-center transition-[border-color,background-color] duration-200 ease-premium hover:border-accent/50 hover:bg-accent-soft/40 focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                >
                    <span className="mb-3 flex h-10 w-10 items-center justify-center rounded-lg border border-line bg-surface text-ink-subtle">
                        <ArrowUpTrayIcon className="h-5 w-5" />
                    </span>
                    <p className="text-[13px] font-semibold text-ink">
                        {t('dropFilesOrClick')}
                    </p>
                    <p className="mt-1 text-[11.5px] text-ink-subtle">
                        PDF, JPG, PNG, DOC — {t('upTo')} 10MB
                    </p>
                </div>
                <input
                    ref={inputRef}
                    type="file"
                    multiple
                    className="hidden"
                    accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.txt"
                    onChange={(event) => {
                        const selected = event.target.files;

                        if (selected) {
                            onAdd(Array.from(selected));
                        }

                        event.target.value = '';
                    }}
                />
            </SectionCard>

            {files.length > 0 ? (
                <SectionCard
                    icon={DocumentTextIcon}
                    title={t('uploadedFiles')}
                    actions={<Badge tone="neutral">{files.length}</Badge>}
                >
                    <ul className="space-y-2">
                        {files.map((file, index) => (
                            <li
                                key={`${file.name}-${index}`}
                                className="flex items-center gap-3 rounded-lg bg-surface-muted px-3 py-2.5"
                            >
                                <DocumentTextIcon className="h-4 w-4 shrink-0 text-ink-subtle" />
                                <div className="min-w-0 flex-1">
                                    <p className="truncate text-[13px] font-medium text-ink">
                                        {file.name}
                                    </p>
                                    <p className="text-[11px] text-ink-subtle tabular-nums">
                                        {(file.size / 1024).toFixed(1)} KB
                                    </p>
                                </div>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    icon={XMarkIcon}
                                    aria-label={`${t('remove')} ${file.name}`}
                                    onClick={() => onRemove(index)}
                                    className="shrink-0"
                                />
                            </li>
                        ))}
                    </ul>
                </SectionCard>
            ) : (
                <EmptyState
                    icon={ArrowUpTrayIcon}
                    title={t('noAttachmentsYet')}
                    description={t('dropFilesOrClick')}
                />
            )}
        </>
    );
}
