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
        'relative bg-white dark:bg-neutral-900 p-8 rounded-3xl border transition-all duration-300 hover:-translate-y-2 hover:shadow-xl',
        highlighted
          ? 'border-blue-600 dark:border-blue-500 shadow-2xl shadow-blue-100 dark:shadow-blue-900/20 scale-105'
          : 'border-slate-100 dark:border-neutral-700 shadow-sm',
        className,
      )}
    >
      {highlighted && (
        <div className="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wider">
          Most Popular
        </div>
      )}

      <div className="mb-6">
        <h3 className="text-xl font-bold text-slate-900 dark:text-neutral-100 mb-2">
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
        <span className="text-sm text-slate-400 dark:text-neutral-500 ml-2">
          {period}
        </span>
      </div>

      <Button
        href={ctaHref}
        variant={highlighted ? 'primary' : 'secondary'}
        size="lg"
        className="w-full mb-8"
      >
        {ctaLabel}
      </Button>

      <ul className="space-y-3">
        {features.map((feature, index) => (
          <li key={index} className="flex items-start gap-3 text-sm">
            <span
              className={cn(
                'w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5',
                feature.included
                  ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600'
                  : 'bg-slate-100 dark:bg-neutral-800 text-slate-300 dark:text-neutral-600',
              )}
            >
              <Check size={12} />
            </span>
            <span
              className={cn(
                feature.included
                  ? 'text-slate-700 dark:text-neutral-300'
                  : 'text-slate-300 dark:text-neutral-600 line-through',
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
