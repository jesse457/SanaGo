import { Check } from 'lucide-react';
import Button from '@/Components/marketing/Button';
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

export default function PricingCard({
    name,
    price,
    period,
    description,
    features,
    highlighted = false,
    ctaLabel = 'Get Started',
    ctaHref = '/contact',
    className,
}: PricingCardProps) {
    return (
        <div
            className={cn(
                'relative rounded-3xl border bg-white p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl dark:bg-neutral-900',
                highlighted
                    ? 'scale-105 border-blue-600 shadow-2xl shadow-blue-100 dark:border-blue-500 dark:shadow-blue-900/20'
                    : 'border-slate-100 shadow-sm dark:border-neutral-700',
                className,
            )}
        >
            {highlighted && (
                <div className="absolute -top-4 left-1/2 -translate-x-1/2 rounded-full bg-blue-600 px-4 py-1.5 text-xs font-bold tracking-wider text-white uppercase">
                    Most Popular
                </div>
            )}

            <div className="mb-6">
                <h3 className="mb-2 text-xl font-bold text-slate-900 dark:text-neutral-100">
                    {name}
                </h3>
                <p className="text-sm text-slate-500 dark:text-neutral-400">
                    {description}
                </p>
            </div>

            <div className="mb-8">
                <span className="text-5xl font-black text-slate-900 dark:text-neutral-100">
                    {price}
                </span>
                <span className="ml-2 text-sm text-slate-400 dark:text-neutral-500">
                    {period}
                </span>
            </div>

            <Button
                href={ctaHref}
                variant={highlighted ? 'primary' : 'secondary'}
                size="lg"
                className="mb-8 w-full"
            >
                {ctaLabel}
            </Button>

            <ul className="space-y-3">
                {features.map((feature, index) => (
                    <li key={index} className="flex items-start gap-3 text-sm">
                        <span
                            className={cn(
                                'mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full',
                                feature.included
                                    ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30'
                                    : 'bg-slate-100 text-slate-300 dark:bg-neutral-800 dark:text-neutral-600',
                            )}
                        >
                            <Check size={12} />
                        </span>
                        <span
                            className={cn(
                                feature.included
                                    ? 'text-slate-700 dark:text-neutral-300'
                                    : 'text-slate-300 line-through dark:text-neutral-600',
                            )}
                        >
                            {feature.text}
                        </span>
                    </li>
                ))}
            </ul>
        </div>
    );
}
