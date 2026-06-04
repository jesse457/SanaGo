import { cn } from '@/lib/utils';

interface FeatureCardProps {
  icon: React.ReactNode;
  title: string;
  description: string;
  color: string;
  className?: string;
}

export default function FeatureCard({
  icon,
  title,
  description,
  color,
  className,
}: FeatureCardProps) {
  return (
    <div
      className={cn(
        'bg-white dark:bg-neutral-900 p-8 rounded-xl border border-slate-100 dark:border-neutral-700 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group',
        className,
      )}
    >
      <div
        className={cn(
          'w-12 h-12 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300',
          color,
        )}
      >
        {icon}
      </div>
      <h3 className="text-lg font-bold mb-3 text-slate-900 dark:text-neutral-100 group-hover:text-blue-600 transition">
        {title}
      </h3>
      <p className="text-sm text-slate-500 dark:text-neutral-400 leading-relaxed">
        {description}
      </p>
    </div>
  );
}
