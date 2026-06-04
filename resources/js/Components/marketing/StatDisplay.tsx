interface StatDisplayProps {
  value: string;
  label: string;
}

export default function StatDisplay({ value, label }: StatDisplayProps) {
  return (
    <div className="border-l-2 border-blue-600 pl-4">
      <p className="text-2xl font-black text-slate-900 dark:text-neutral-100">
        {value}
      </p>
      <p className="text-xs text-slate-400 dark:text-neutral-500 font-medium mt-1">
        {label}
      </p>
    </div>
  );
}
