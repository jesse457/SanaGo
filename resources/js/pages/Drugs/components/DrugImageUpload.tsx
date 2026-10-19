import {
    ArrowUpTrayIcon,
    CubeIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Button, Card } from '@/Components/ui';

interface DrugImageUploadProps {
    preview: string | null;
    /** Copy for the empty well and the picker button. */
    uploadLabel: string;
    previewAlt: string;
    removeLabel: string;
    onSelect: (file: File) => void;
    onClear: () => void;
}

/**
 * DrugImageUpload — optional artwork for a medication. The file input stays a
 * real input (visually hidden behind the button) so the form-data upload path
 * is untouched.
 */
export function DrugImageUpload({
    preview,
    uploadLabel,
    previewAlt,
    removeLabel,
    onSelect,
    onClear,
}: DrugImageUploadProps) {
    return (
        <Card className="flex flex-col items-start gap-4 p-4 sm:flex-row sm:items-center">
            {preview ? (
                <div className="relative">
                    <img
                        src={preview}
                        alt={previewAlt}
                        className="h-24 w-24 rounded-lg border border-line object-cover"
                    />
                    <button
                        type="button"
                        onClick={onClear}
                        aria-label={removeLabel}
                        className="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full border border-line bg-surface text-ink-muted shadow-surface transition-colors duration-150 ease-premium hover:border-critical/40 hover:bg-critical-soft hover:text-critical-ink focus-visible:ring-2 focus-visible:ring-accent/45 focus-visible:ring-offset-1 focus-visible:ring-offset-surface focus-visible:outline-none"
                    >
                        <XMarkIcon className="h-3.5 w-3.5" />
                    </button>
                </div>
            ) : (
                <div className="flex h-24 w-24 items-center justify-center rounded-lg border border-dashed border-line-strong bg-surface-inset">
                    <CubeIcon className="h-7 w-7 text-ink-subtle" />
                </div>
            )}

            <div className="min-w-0">
                <Button
                    type="button"
                    variant="secondary"
                    icon={ArrowUpTrayIcon}
                    onClick={() => {
                        const input =
                            document.getElementById('drug-image-input');

                        if (input instanceof HTMLInputElement) {
                            input.click();
                        }
                    }}
                >
                    {uploadLabel}
                </Button>
                <input
                    id="drug-image-input"
                    type="file"
                    accept="image/*"
                    className="sr-only"
                    onChange={(event) => {
                        const file = event.target.files?.[0];

                        if (file) {
                            onSelect(file);
                        }

                        event.target.value = '';
                    }}
                />
            </div>
        </Card>
    );
}
