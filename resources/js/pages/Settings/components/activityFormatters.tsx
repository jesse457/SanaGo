/**
 * Formatting helpers for the activity log. They translate Spatie's raw
 * attribute names, model class names and user-agent strings into the plain
 * language the page shows.
 */

export type Translate = (
    key: string,
    replacements?: Record<string, string | number>,
) => string;

export function formatFieldName(key: string, translate: Translate): string {
    const friendlyNames: Record<string, string> = {
        id: 'System ID',
        name: translate('common.name'),
        first_name: translate('patient.first_name'),
        last_name: translate('patient.last_name'),
        email: translate('common.email'),
        phone: translate('common.phone'),
        is_active: translate('common.status'),
        status: translate('common.status'),
        dosage_units: translate('drug.dosage_units'),
        min_stock_level: translate('drug.min_stock_level'),
        total_stock: translate('drug.total_stock'),
        description: translate('common.description'),
        price: translate('common.price'),
        code: translate('common.code'),
        created_at: translate('common.created_at'),
        updated_at: translate('common.updated_at'),
        password: translate('common.password'),
    };

    if (friendlyNames[key]) {
        return friendlyNames[key];
    }

    return key
        .replace(/[_-]/g, ' ')
        .replace(/\b\w/g, (character) => character.toUpperCase());
}

export function formatModelName(modelName?: string | null): string {
    if (!modelName) {
        return 'System Record';
    }

    const map: Record<string, string> = {
        User: 'User Account',
        Patient: 'Patient Profile',
        Appointment: 'Appointment Booking',
        Drug: 'Medication Record',
        DrugBatch: 'Medication Batch',
        LabTest: 'Lab Test Definition',
        LabResult: 'Lab Test Result',
        LabRequest: 'Lab Request',
        Consultation: 'Patient Consultation',
    };

    return map[modelName] || modelName;
}

export function formatEventName(event?: string): string {
    if (!event) {
        return '—';
    }

    const map: Record<string, string> = {
        created: 'New Entry Created',
        updated: 'Information Modified',
        deleted: 'Entry Removed',
        login: 'User Logged In',
    };

    return map[event.toLowerCase()] || event;
}

export function getFriendlyUserAgent(userAgent?: string): string {
    if (!userAgent) {
        return '—';
    }

    const ua = userAgent.toLowerCase();
    let browser = 'Unknown Web Browser';
    let os = 'Device';

    if (ua.includes('chrome')) {
        browser = 'Google Chrome';
    } else if (ua.includes('firefox')) {
        browser = 'Mozilla Firefox';
    } else if (ua.includes('safari')) {
        browser = 'Apple Safari';
    } else if (ua.includes('edge')) {
        browser = 'Microsoft Edge';
    }

    if (ua.includes('windows')) {
        os = 'Windows PC';
    } else if (ua.includes('macintosh') || ua.includes('mac os')) {
        os = 'Apple Mac';
    } else if (ua.includes('linux')) {
        os = 'Linux Computer';
    } else if (ua.includes('android')) {
        os = 'Android Mobile';
    } else if (ua.includes('iphone') || ua.includes('ipad')) {
        os = 'Apple Mobile';
    }

    return `${browser} on ${os}`;
}

export function formatFriendlyValue(
    key: string,
    value: unknown,
): React.ReactNode {
    if (value === undefined || value === null || value === '') {
        return <span className="text-ink-subtle italic">Not Provided</span>;
    }

    if (typeof value === 'boolean') {
        return value ? (
            <span className="font-semibold text-positive-ink">Active</span>
        ) : (
            <span className="font-semibold text-critical-ink">Inactive</span>
        );
    }

    if (key.toLowerCase().includes('price')) {
        const amount = Number(value);

        if (!Number.isNaN(amount)) {
            return `$${amount.toFixed(2)}`;
        }
    }

    if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}/.test(value)) {
        const date = new Date(value);

        if (!Number.isNaN(date.getTime())) {
            return date.toLocaleDateString(undefined, {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                ...(value.includes('T') || value.includes(':')
                    ? { hour: '2-digit', minute: '2-digit' }
                    : {}),
            });
        }
    }

    if (typeof value === 'object') {
        return JSON.stringify(value);
    }

    return String(value);
}

export type ActivityTone =
    | 'positive'
    | 'accent'
    | 'warning'
    | 'critical'
    | 'neutral';

export function getActivityTone(type: string): ActivityTone {
    const value = type.toLowerCase();

    if (value.includes('created')) {
        return 'positive';
    }

    if (value.includes('login')) {
        return 'accent';
    }

    if (value.includes('updated')) {
        return 'warning';
    }

    if (value.includes('deleted')) {
        return 'critical';
    }

    return 'neutral';
}
