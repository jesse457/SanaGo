import {
    EnvelopeIcon,
    PhoneIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import type { ComponentType, SVGProps } from 'react';
import { Button, Card, CardContent } from '@/Components/ui';
import { PayerTypeBadge } from '@/pages/Finance/components/FinanceStatusBadges';

export interface PayerCardData {
    id: number;
    name: string;
    type: string;
    contact_person?: string;
    phone?: string;
    email?: string;
    coverage_percentage: number;
    patient_insurances_count?: number;
    is_active: boolean;
}

interface PayerCardProps {
    payer: PayerCardData;
    onEdit: (payer: PayerCardData) => void;
    labels: {
        edit: string;
        coverage: string;
        insured: string;
        inactive: string;
    };
}

/**
 * PayerCard — one insurance payer: name, classification badge, contact rows and
 * a coverage/insured footer. Used by the payers grid.
 */
export function PayerCard({ payer, onEdit, labels }: PayerCardProps) {
    const contacts: {
        icon: ComponentType<SVGProps<SVGSVGElement>>;
        value: string;
    }[] = [];

    if (payer.contact_person) {
        contacts.push({ icon: UserGroupIcon, value: payer.contact_person });
    }

    if (payer.phone) {
        contacts.push({ icon: PhoneIcon, value: payer.phone });
    }

    if (payer.email) {
        contacts.push({ icon: EnvelopeIcon, value: payer.email });
    }

    return (
        <Card className="group flex flex-col transition-[border-color,box-shadow] duration-300 ease-premium hover:border-line-strong hover:shadow-surface">
            <CardContent className="flex flex-1 flex-col gap-3">
                <div className="flex items-start justify-between gap-3">
                    <div className="min-w-0">
                        <p className="truncate text-[15px] leading-tight font-semibold tracking-tight text-ink">
                            {payer.name}
                        </p>
                        <div className="mt-1.5 flex flex-wrap items-center gap-1.5">
                            <PayerTypeBadge status={payer.type} />
                            {!payer.is_active && (
                                <span className="text-[11px] text-ink-subtle">
                                    {labels.inactive}
                                </span>
                            )}
                        </div>
                    </div>
                    <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => onEdit(payer)}
                        className="opacity-0 transition-opacity duration-200 ease-premium group-hover:opacity-100 focus-visible:opacity-100"
                    >
                        {labels.edit}
                    </Button>
                </div>

                {contacts.length > 0 && (
                    <ul className="space-y-1.5">
                        {contacts.map((contact) => (
                            <li
                                key={contact.value}
                                className="flex items-center gap-2 text-[13px] text-ink-muted"
                            >
                                <contact.icon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                                <span className="truncate">
                                    {contact.value}
                                </span>
                            </li>
                        ))}
                    </ul>
                )}

                <div className="mt-auto flex items-center justify-between gap-2 border-t border-line pt-3 text-[12px] text-ink-subtle tabular-nums">
                    <span>
                        {labels.coverage} {payer.coverage_percentage}%
                    </span>
                    <span>
                        {payer.patient_insurances_count ?? 0} {labels.insured}
                    </span>
                </div>
            </CardContent>
        </Card>
    );
}
