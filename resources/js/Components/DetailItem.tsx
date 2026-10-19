import type { ComponentType, ReactNode, SVGProps } from 'react';
import { KeyValue } from '@/Components/ui/KeyValue';

interface DetailItemProps {
    label: string;
    value: ReactNode;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    className?: string;
}

/**
 * DetailItem — legacy label/value pair kept for existing pages. Must be used
 * inside a `dl` (or `<KeyValueGrid>`) for correct semantics.
 *
 * New code should use `<KeyValue>` from `@/Components/ui`.
 */
export default function DetailItem({
    label,
    value,
    icon,
    className,
}: DetailItemProps) {
    return (
        <KeyValue
            label={label}
            value={value}
            icon={icon}
            className={className}
            tile
        />
    );
}
