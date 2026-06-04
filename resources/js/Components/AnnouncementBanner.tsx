import { useState, useEffect } from 'react';
import { XMarkIcon, MegaphoneIcon, ExclamationTriangleIcon, CheckCircleIcon } from '@heroicons/react/20/solid';
import { usePage } from '@inertiajs/react';
import { PageProps } from '@inertiajs/core';

interface Announcement {
  id: number;
  title: string;
  body: string;
  type: 'info' | 'warning' | 'success';
  published_at: string;
  expires_at: string | null;
  link_url?: string;
  link_text?: string;
}

interface CustomPageProps extends PageProps {
  announcements?: Announcement[];
}

const STYLES: Record<
  string,
  {
    bg: string;
    border: string;
    icon: React.ReactNode;
    linkClass: string;
  }
> = {
  info: {
    bg: 'bg-blue-50/60 dark:bg-blue-950/20 backdrop-blur-md',
    border: 'border-b border-blue-100 dark:border-blue-900/30',
    icon: <MegaphoneIcon className="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" />,
    linkClass: 'text-blue-700 dark:text-blue-400 hover:underline',
  },
  warning: {
    bg: 'bg-amber-50/60 dark:bg-amber-950/20 backdrop-blur-md',
    border: 'border-b border-amber-100 dark:border-amber-900/30',
    icon: <ExclamationTriangleIcon className="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" />,
    linkClass: 'text-amber-700 dark:text-amber-400 hover:underline',
  },
  success: {
    bg: 'bg-emerald-50/60 dark:bg-emerald-950/20 backdrop-blur-md',
    border: 'border-b border-emerald-100 dark:border-emerald-900/30',
    icon: <CheckCircleIcon className="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />,
    linkClass: 'text-emerald-700 dark:text-emerald-400 hover:underline',
  },
};

export default function AnnouncementBanner() {
  const { props } = usePage<CustomPageProps>();
  const announcements = props.announcements;

  const [dismissed, setDismissed] = useState<Set<number>>(new Set());
  const [dismissing, setDismissing] = useState<Set<number>>(new Set());

  useEffect(() => {
    try {
      const stored = sessionStorage.getItem('dismissed_announcements');
      if (stored) setDismissed(new Set(JSON.parse(stored)));
    } catch {}
  }, []);

  if (!announcements || announcements.length === 0) return null;

  const visible = announcements.filter(a => !dismissed.has(a.id));
  if (visible.length === 0) return null;

  const handleDismiss = (id: number) => {
    setDismissing(prev => new Set([...prev, id]));
    setTimeout(() => {
      const next = new Set([...dismissed, id]);
      setDismissed(next);
      sessionStorage.setItem('dismissed_announcements', JSON.stringify([...next]));
      setDismissing(prev => {
        const updated = new Set(prev);
        updated.delete(id);
        return updated;
      });
    }, 200); // 200ms snappy page layout shift
  };

  return (
    <div className="w-full shrink-0 flex flex-col transition-all duration-200">
      {visible.map(a => {
        const s = STYLES[a.type] || STYLES.info;
        const isDismissing = dismissing.has(a.id);

        return (
          <div
            key={a.id}
            className={`
              w-full flex items-center justify-between gap-3 py-2 px-4 lg:px-6
              transition-all duration-200 ease-out origin-top
              ${s.bg} ${s.border}
              ${
                isDismissing
                  ? 'opacity-0 max-h-0 py-0 border-b-0 overflow-hidden pointer-events-none'
                  : 'opacity-100 max-h-12'
              }
            `}
            style={{
              ...(isDismissing && {
                paddingTop: 0,
                paddingBottom: 0,
                height: 0,
                borderBottomWidth: 0,
              }),
            }}
          >
            {/* Inline message alignment */}
            <div className="flex-1 min-w-0 flex items-center gap-2.5 text-xs sm:text-sm">
              {s.icon}
              <div className="flex flex-col sm:flex-row sm:items-center gap-x-2 gap-y-0.5 truncate">
                <span className="font-semibold text-zinc-900 dark:text-zinc-100 shrink-0">
                  {a.title}
                </span>
                <span className="text-zinc-600 dark:text-zinc-300 truncate">
                  {a.body}
                </span>
              </div>
              {a.link_url && (
                <a
                  href={a.link_url}
                  className={`font-semibold shrink-0 ml-2 text-xs uppercase tracking-wider underline underline-offset-4 ${s.linkClass}`}
                >
                  {a.link_text || 'Learn more'}
                </a>
              )}
            </div>

            {/* Tight dismissal trigger */}
            <button
              onClick={() => handleDismiss(a.id)}
              aria-label="Dismiss banner"
              className="p-1 rounded-md text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300 hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-150 active:scale-95"
            >
              <XMarkIcon className="w-4 h-4" />
            </button>
          </div>
        );
      })}
    </div>
  );
}
