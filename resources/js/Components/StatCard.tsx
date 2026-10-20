import type { ComponentType, ReactNode, SVGProps } from 'react';
import { StatCard as UiStatCard } from '@/Components/ui/StatCard';
import type { StatTone } from '@/Components/ui/StatCard';

type StatColor = 'blue' | 'emerald' | 'purple' | 'amber' | 'red';

interface StatCardProps {
    icon: ComponentType<SVGProps<SVGSVGElement>> | ReactNode;
    label: string;
    value: string | number;
    color?: StatColor;
    hint?: string;
}

const toneByColor: Record<StatColor, StatTone> = {
    blue: 'accent',
    emerald: 'positive',
    purple: 'neutral',
    amber: 'warning',
    red: 'critical',
};

/**
 * StatCard — legacy KPI tile kept for existing pages.
 *
 * New code should use `<StatCard>` from `@/Components/ui` which also supports
 * trends, hints and footers.
 */
export default function StatCard({
    icon,
    label,
    value,
    color = 'blue',
    hint,
}: StatCardProps) {
    return (
        <UiStatCard
            icon={icon}
            label={label}
            value={value}
            tone={toneByColor[color]}
            hint={hint}
        />
    );
}
