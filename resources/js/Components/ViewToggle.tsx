import { ListBulletIcon, Squares2X2Icon } from '@heroicons/react/24/outline';
import { SegmentedControl } from '@/Components/ui/SegmentedControl';

interface ViewToggleProps {
    view: 'grid' | 'table';
    onChange: (view: 'grid' | 'table') => void;
    className?: string;
}

/**
 * ViewToggle — legacy grid/table switch kept for existing pages.
 *
 * New code should use `<SegmentedControl>` from `@/Components/ui`.
 */
export default function ViewToggle({
    view,
    onChange,
    className,
}: ViewToggleProps) {
    return (
        <SegmentedControl
            className={className}
            value={view}
            onChange={onChange}
            options={[
                {
                    value: 'grid',
                    icon: Squares2X2Icon,
                    ariaLabel: 'Grid view',
                },
                {
                    value: 'table',
                    icon: ListBulletIcon,
                    ariaLabel: 'Table view',
                },
            ]}
        />
    );
}
