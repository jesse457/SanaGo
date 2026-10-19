import { CheckIcon, MinusIcon } from '@heroicons/react/24/outline';
import Button from '@/Components/marketing/Button';
import { Badge, Card } from '@/Components/ui';
import { cn } from '@/lib/utils';

interface PricingFeature {
    included: boolean;
    text: string;
}

interface PricingCardProps {
    name: string;
    price: string;
    period: string;
    description: string;
    features: PricingFeature[];
    highlighted?: boolean;
    ctaLabel?: string;
    ctaHref?: string;
    className?: string;
}

/**
 * PricingCard — one plan.
 *
 * The recommended plan is marked with a tinted hairline panel and an accent
 * badge instead of a scale bump or a drop shadow, so the three plans still sit
 * on one clean baseline. Included/absent features are stated, not shouted.
 */
export default function PricingCard({
    name,
    price,
    period,
    description,
    features,
    highlighted = false,
    ctaLabel = 'Get started',
    ctaHref = '/contact',
    className,
}: PricingCardProps) {
    return (
        <Card
            className={cn(
                'flex h-full flex-col p-6 sm:p-7',
                highlighted
                    ? 'border-accent/35 bg-accent-soft/40'
                    : 'bg-surface',
                className,
            )}
        >
            <div className="flex items-center justify-between gap-3">
                <h3 className="text-[15px] font-semibold tracking-tight text-ink">
                    {name}
                </h3>
                {highlighted && (
                    <Badge tone="accent" variant="soft" dot>
                        Recommended
                    </Badge>
                )}
            </div>

            <p className="mt-2 min-h-[40px] text-[13.5px] leading-relaxed text-ink-muted">
                {description}
            </p>

            <div className="mt-6 flex items-baseline gap-1.5 border-t border-line pt-6">
                <span
                    data-numeric
                    className="text-[38px] leading-none font-semibold tracking-[-0.035em] text-ink tabular-nums"
                >
                    {price}
                </span>
                {period && (
                    <span className="text-[12.5px] font-medium text-ink-subtle">
                        {period}
                    </span>
                )}
            </div>

            <div className="mt-6">
                <Button
                    href={ctaHref}
                    variant={highlighted ? 'primary' : 'secondary'}
                    size="md"
                    className="w-full justify-center"
                >
                    {ctaLabel}
                </Button>
            </div>

            <ul className="mt-7 flex-1 space-y-0 border-t border-line pt-5">
                {features.map((feature) => (
                    <li
                        key={feature.text}
                        className="flex items-start gap-2.5 py-1.5 text-[13px] leading-snug"
                    >
                        <span
                            aria-hidden="true"
                            className={cn(
                                'mt-px flex h-4 w-4 shrink-0 items-center justify-center rounded-sm',
                                feature.included
                                    ? 'text-accent-ink'
                                    : 'text-ink-subtle/60',
                            )}
                        >
                            {feature.included ? (
                                <CheckIcon className="h-3.5 w-3.5" />
                            ) : (
                                <MinusIcon className="h-3.5 w-3.5" />
                            )}
                        </span>
                        <span
                            className={cn(
                                feature.included
                                    ? 'text-ink-muted'
                                    : 'text-ink-subtle line-through decoration-line-strong',
                            )}
                        >
                            {!feature.included && (
                                <span className="sr-only">Not included: </span>
                            )}
                            {feature.text}
                        </span>
                    </li>
                ))}
            </ul>
        </Card>
    );
}
