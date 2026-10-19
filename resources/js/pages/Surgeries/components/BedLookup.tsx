import { RectangleStackIcon } from '@heroicons/react/24/outline';
import { useMemo, useRef, useState } from 'react';
import { Button, Field, Input } from '@/Components/ui';
import type { Bed, Translate } from './types';

interface BedLookupProps {
    beds: Bed[];
    query: string;
    onQueryChange: (value: string) => void;
    onSelect: (bed: Bed) => void;
    onClearSelection: () => void;
    error?: string;
    t: Translate;
}

/**
 * BedLookup — assigns the recovery bed. The list is capped at ten matches, same
 * as the surrounding search fields.
 */
export function BedLookup({
    beds,
    query,
    onQueryChange,
    onSelect,
    onClearSelection,
    error,
    t,
}: BedLookupProps) {
    const [open, setOpen] = useState(false);
    const blurTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

    const results = useMemo(() => {
        if (!query.trim()) {
            return beds.slice(0, 10);
        }

        const q = query.toLowerCase();

        return beds
            .filter(
                (bed) =>
                    bed.code.toLowerCase().includes(q) ||
                    bed.ward_name.toLowerCase().includes(q) ||
                    bed.type_name.toLowerCase().includes(q),
            )
            .slice(0, 10);
    }, [beds, query]);

    return (
        <Field
            label={t('surgeries.create.recovery_bed_id')}
            htmlFor="bed_id"
            error={error}
        >
            <div className="relative">
                <Input
                    id="bed_id"
                    value={query}
                    icon={RectangleStackIcon}
                    invalid={Boolean(error)}
                    autoComplete="off"
                    placeholder={
                        t('surgeries.create.recovery_bed_id_placeholder') ||
                        'Search recovery beds…'
                    }
                    onChange={(event) => {
                        onQueryChange(event.target.value);
                        setOpen(true);

                        if (!event.target.value) {
                            onClearSelection();
                        }
                    }}
                    onFocus={() => setOpen(true)}
                    onBlur={() => {
                        blurTimer.current = setTimeout(
                            () => setOpen(false),
                            200,
                        );
                    }}
                />

                {open && results.length > 0 && (
                    <div className="absolute inset-x-0 top-full z-30 mt-1.5 max-h-48 overflow-y-auto rounded-lg border border-line bg-surface p-1 shadow-float">
                        {results.map((bed) => (
                            <Button
                                key={bed.id}
                                variant="ghost"
                                onClick={() => {
                                    if (blurTimer.current) {
                                        clearTimeout(blurTimer.current);
                                    }

                                    onSelect(bed);
                                    setOpen(false);
                                }}
                                className="h-auto w-full justify-start gap-2.5 px-2 py-2 text-left"
                            >
                                <span className="min-w-0">
                                    <span className="block truncate text-[13px] font-semibold text-ink">
                                        {bed.code}
                                    </span>
                                    <span className="block truncate text-[11px] text-ink-subtle">
                                        {bed.ward_name} · {bed.type_name}
                                    </span>
                                </span>
                            </Button>
                        ))}
                    </div>
                )}
            </div>
        </Field>
    );
}
