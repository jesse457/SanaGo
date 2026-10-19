import { Badge } from '@/Components/ui';
import { Avatar } from '@/Components/ui/Avatar';
import { cn } from '@/lib/utils';

interface PatientIdentityProps {
    firstName: string;
    lastName: string;
    patientUid: string;
    photoUrl?: string | null;
    /** Already-localised demographics, e.g. "34 yrs · Male". */
    demographics?: string | null;
    size?: 'sm' | 'md' | 'lg';
    className?: string;
}

const avatarSize = {
    sm: 'sm',
    md: 'md',
    lg: 'lg',
} as const;

const nameClasses = {
    sm: 'text-[13px] font-semibold',
    md: 'text-[15px] font-semibold',
    lg: 'text-[17px] font-semibold',
} as const;

/**
 * PatientIdentity — avatar, full name, UID chip and demographics on one line.
 * Used by the index grid/table views and by every patient-facing header.
 */
export function PatientIdentity({
    firstName,
    lastName,
    patientUid,
    photoUrl,
    demographics,
    size = 'md',
    className,
}: PatientIdentityProps) {
    const fullName = `${firstName} ${lastName}`.trim();

    return (
        <div className={cn('flex min-w-0 items-center gap-3', className)}>
            <Avatar
                src={photoUrl}
                name={fullName}
                size={avatarSize[size]}
                shape="squircle"
            />
            <div className="min-w-0">
                <p
                    className={cn(
                        'truncate tracking-tight text-ink',
                        nameClasses[size],
                    )}
                >
                    {fullName}
                </p>
                <div className="mt-1 flex min-w-0 flex-wrap items-center gap-1.5">
                    <Badge
                        tone="accent"
                        variant="outline"
                        className="font-mono"
                    >
                        {patientUid}
                    </Badge>
                    {demographics && (
                        <span className="truncate text-[11px] font-medium text-ink-subtle uppercase">
                            {demographics}
                        </span>
                    )}
                </div>
            </div>
        </div>
    );
}
