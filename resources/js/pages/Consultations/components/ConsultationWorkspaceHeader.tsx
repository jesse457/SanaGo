import { CheckIcon } from '@heroicons/react/24/outline';
import { Badge, Button, Tabs } from '@/Components/ui';
import type { TabItem } from '@/Components/ui';
import type { Translate } from './types';

interface ConsultationWorkspaceHeaderProps {
    tabs: TabItem[];
    activeTab: string;
    onTabChange: (value: string) => void;
    conflictCount: number;
    processing: boolean;
    t: Translate;
}

/**
 * ConsultationWorkspaceHeader — the tab rail plus the persistent save button
 * that tops the consultation workspace.
 */
export function ConsultationWorkspaceHeader({
    tabs,
    activeTab,
    onTabChange,
    conflictCount,
    processing,
    t,
}: ConsultationWorkspaceHeaderProps) {
    return (
        <div className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-b border-line bg-surface px-3 pt-2 pb-2 sm:px-6 sm:pt-3">
            <div className="min-w-0 flex-1">
                <Tabs
                    items={tabs.map((tab) =>
                        tab.value === 'rx' && conflictCount > 0
                            ? { ...tab, count: conflictCount }
                            : tab,
                    )}
                    value={activeTab}
                    onChange={onTabChange}
                />
            </div>

            <div className="flex shrink-0 items-center gap-2">
                {conflictCount > 0 && (
                    <Badge tone="critical" dot>
                        {conflictCount}
                    </Badge>
                )}
                <Button
                    type="submit"
                    variant="primary"
                    size="sm"
                    icon={CheckIcon}
                    loading={processing}
                >
                    {processing ? t('processing') : t('saveRecord')}
                </Button>
            </div>
        </div>
    );
}

interface ConsultationSubmitBarProps {
    processing: boolean;
    t: Translate;
}

/**
 * ConsultationSubmitBar — the mobile sticky save affordance.
 */
export function ConsultationSubmitBar({
    processing,
    t,
}: ConsultationSubmitBarProps) {
    return (
        <div className="shrink-0 border-t border-line bg-surface p-3 lg:hidden">
            <Button
                type="submit"
                variant="primary"
                fullWidth
                icon={CheckIcon}
                loading={processing}
            >
                {processing ? t('processing') : t('saveRecord')}
            </Button>
        </div>
    );
}
