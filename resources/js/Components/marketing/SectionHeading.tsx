import { cn } from '@/lib/utils';

interface SectionHeadingProps {
  title: string;
  subtitle?: string;
  align?: 'center' | 'left';
  className?: string;
}

export default function SectionHeading({
  title,
  subtitle,
  align = 'center',
  className,
}: SectionHeadingProps) {
  return (
    <div
      className={cn(
        'max-w-2xl mb-16',
        align === 'center' && 'mx-auto text-center',
        className,
      )}
    >
      <h2 className="text-4xl font-bold text-slate-900 dark:text-neutral-100">
        {title}
      </h2>
      <div
        className={cn(
          'w-12 h-1 bg-blue-600 rounded-full my-6',
          align === 'center' && 'mx-auto',
        )}
      />
      {subtitle && (
        <p className="text-slate-500 dark:text-neutral-400">{subtitle}</p>
      )}
    </div>
  );
}
