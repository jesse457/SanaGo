import { PhotoIcon, PlusIcon, XMarkIcon } from '@heroicons/react/24/outline';
import { Field } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

interface PatientPhotoFieldProps {
    previewUrl: string | null;
    error?: string;
    onSelect: (file: File) => void;
    onClear: () => void;
}

/**
 * PatientPhotoField — drag-and-drop portrait picker for the registration form.
 * Owns its dashed drop zone, preview and clear affordance.
 */
export function PatientPhotoField({
    previewUrl,
    error,
    onSelect,
    onClear,
}: PatientPhotoFieldProps) {
    const { t } = useTranslation();

    const handleDrop = (event: React.DragEvent<HTMLDivElement>) => {
        event.preventDefault();
        const file = event.dataTransfer.files[0];

        if (file && file.type.startsWith('image/')) {
            onSelect(file);
        }
    };

    return (
        <Field
            label={t('page.patients_create.patient_photo')}
            error={error}
            hint={t('page.patients_create.max_photo_size')}
        >
            <div
                onDragOver={(event) => event.preventDefault()}
                onDrop={handleDrop}
                className={cn(
                    'flex flex-col items-start gap-4 rounded-lg border bg-surface-muted p-4 transition-colors duration-200 ease-premium sm:flex-row sm:items-center sm:gap-5',
                    error ? 'border-critical/70' : 'border-line',
                )}
            >
                <div className="relative shrink-0">
                    {previewUrl ? (
                        <div className="relative">
                            <img
                                src={previewUrl}
                                alt={t('page.patients_create.patient_photo')}
                                className="h-20 w-20 rounded-lg border border-line object-cover"
                            />
                            <button
                                type="button"
                                onClick={onClear}
                                aria-label={t('common.close')}
                                className="absolute -top-1.5 -right-1.5 rounded-full border border-line bg-surface p-1 text-ink-muted shadow-surface transition-colors duration-150 ease-premium hover:text-critical-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                            >
                                <XMarkIcon className="h-3.5 w-3.5" />
                            </button>
                        </div>
                    ) : (
                        <div className="flex h-20 w-20 items-center justify-center rounded-lg border border-dashed border-line-strong bg-surface text-ink-subtle">
                            <PhotoIcon className="h-7 w-7" />
                        </div>
                    )}
                </div>

                <div className="flex min-w-0 flex-col items-start gap-1.5">
                    <label className="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-line bg-surface px-3 text-[13px] font-semibold text-ink shadow-surface transition-colors duration-200 ease-premium hover:border-line-strong hover:bg-surface-hover">
                        <PlusIcon className="h-4 w-4 opacity-80" />
                        {t('page.patients_create.upload_photo')}
                        <input
                            type="file"
                            className="sr-only"
                            accept="image/*"
                            onChange={(event) => {
                                const file = event.target.files?.[0];

                                if (file) {
                                    onSelect(file);
                                }
                            }}
                        />
                    </label>
                    <p className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                        {t('page.patients_create.max_photo_size')}
                    </p>
                </div>
            </div>
        </Field>
    );
}
