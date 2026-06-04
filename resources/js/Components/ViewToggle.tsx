import { Squares2X2Icon, ListBulletIcon } from '@heroicons/react/24/outline';

interface ViewToggleProps {
    view: 'grid' | 'table';
    onChange: (view: 'grid' | 'table') => void;
}

export default function ViewToggle({ view, onChange }: ViewToggleProps) {
    return (
        <div className="inline-flex rounded-lg border border-gray-200 bg-white p-1 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <button
                onClick={() => onChange('grid')}
                className={`rounded-md px-3 py-1.5 text-sm font-medium transition-colors ${view === 'grid' ? 'bg-blue-50 text-blue-600 shadow-sm dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200'}`}
                aria-label="Grid view"
            >
                <Squares2X2Icon className="h-5 w-5" />
            </button>
            <button
                onClick={() => onChange('table')}
                className={`rounded-md px-3 py-1.5 text-sm font-medium transition-colors ${view === 'table' ? 'bg-blue-50 text-blue-600 shadow-sm dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200'}`}
                aria-label="Table view"
            >
                <ListBulletIcon className="h-5 w-5" />
            </button>
        </div>
    );
}
