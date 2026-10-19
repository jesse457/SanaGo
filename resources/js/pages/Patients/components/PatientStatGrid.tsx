import type { ComponentType, SVGProps } from 'react';
import { StatCard, StatGrid } from '@/Components/ui';

export interface PatientStat {
    label: string;
    value: number;
    icon: ComponentType<SVGProps<SVGSVGElement>>;
    tone?: 'neutral' | 'accent' | 'positive' | 'warning' | 'critical';
}

/**
 * PatientStatGrid — the four-KPI strip shared by the patients index and the
 * admissions registry so both screens read as one product.
 */
export function PatientStatGrid({ stats }: { stats: PatientStat[] }) {
    return (
        <StatGrid columns={4}>
            {stats.map((stat) => (
                <StatCard
                    key={stat.label}
                    label={stat.label}
                    value={stat.value}
                    icon={stat.icon}
                    tone={stat.tone ?? 'neutral'}
                />
            ))}
        </StatGrid>
    );
}
