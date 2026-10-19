import { Avatar, Badge } from '@/Components/ui';
import { statusTone } from '@/pages/Appointments/components/status';

interface AppointmentIdentityProps {
    firstName: string;
    lastName: string;
    patientUid?: string;
    /** Doctor name shown under the patient name in the card grid. */
    meta?: string;
    size?: 'sm' | 'md';
}

/**
 * AppointmentIdentity — initials chip, patient name and UID. Shared by the
 * index table rows and the index cards so both views stay in sync.
 */
export function AppointmentIdentity({
    firstName,
    lastName,
    patientUid,
    meta,
    size = 'md',
}: AppointmentIdentityProps) {
    return (
        <div className="flex min-w-0 items-center gap-3">
            <Avatar
                name={`${firstName} ${lastName}`.trim()}
                size={size === 'sm' ? 'sm' : 'md'}
                shape="squircle"
            />
            <div className="min-w-0">
                <p className="truncate text-[13px] font-semibold tracking-tight text-ink">
                    {firstName} {lastName}
                </p>
                {patientUid && (
                    <p className="truncate font-mono text-[11px] text-ink-subtle">
                        {patientUid}
                    </p>
                )}
                {meta && (
                    <p className="truncate text-[11.5px] text-ink-subtle">
                        {meta}
                    </p>
                )}
            </div>
        </div>
    );
}

/** AppointmentStatusBadge — token-toned status chip with an optional pulse. */
export function AppointmentStatusBadge({
    status,
    label,
    pulse = false,
}: {
    status: string;
    label?: string;
    pulse?: boolean;
}) {
    return (
        <Badge
            tone={statusTone(status)}
            dot
            className={pulse ? 'animate-pulse' : undefined}
        >
            {label ?? status}
        </Badge>
    );
}
