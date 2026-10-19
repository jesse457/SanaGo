/**
 * SanaGo UI kit — the only UI vocabulary pages should speak.
 *
 * Ground rules
 * ------------
 * - No page defines its own button, card, table, badge, input or modal markup.
 *   Compose from here.
 * - Use semantic tokens (`bg-surface`, `text-ink-muted`, `border-line`,
 *   `bg-accent-soft`…) instead of palette steps or `dark:` literals. Tokens are
 *   defined in `resources/css/app.css` and flip automatically in dark mode.
 * - Radii: `rounded-md` for controls, `rounded-lg`/`rounded-xl` for panels,
 *   `rounded-full` only for badges/dots. Never pill-shaped panels.
 * - Elevation: `shadow-surface` / `shadow-raised` / `shadow-float` only.
 * - Motion: 150–300ms with `ease-premium`; animate transform/opacity only.
 * - Numeric data gets `tabular-nums` (Table/StatCard already do).
 *
 * Typical index page
 * ------------------
 *   <PageHeader title="Patients" description="…" actions={<Button href={…}>New</Button>} />
 *   <Toolbar>
 *     <ToolbarSearch value={q} onChange={setQ} />
 *     <Select … />
 *     <ToolbarSpacer />
 *     <Button variant="secondary" icon={ArrowDownTrayIcon}>Export</Button>
 *   </Toolbar>
 *   <Table>
 *     <TableHeader><TableRow><TableHeaderCell>Name</TableHeaderCell></TableRow></TableHeader>
 *     <TableBody>{rows}</TableBody>
 *   </Table>
 *   <Pagination links={links} meta={meta} />
 */

export { Button } from '@/Components/ui/Button';
export type {
    ButtonProps,
    ButtonSize,
    ButtonVariant,
    IconType,
} from '@/Components/ui/Button';

export {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
    SectionCard,
} from '@/Components/ui/Card';

export { Badge } from '@/Components/ui/Badge';
export type {
    BadgeProps,
    BadgeTone,
    BadgeVariant,
} from '@/Components/ui/Badge';

export { Field, FieldSet } from '@/Components/ui/Field';
export type { FieldProps, FieldSetProps } from '@/Components/ui/Field';

export { Input, controlBase, controlState } from '@/Components/ui/Input';
export type { InputProps } from '@/Components/ui/Input';

export { Select } from '@/Components/ui/Select';
export type { SelectProps } from '@/Components/ui/Select';

export { Textarea } from '@/Components/ui/Textarea';
export type { TextareaProps } from '@/Components/ui/Textarea';

export { Checkbox, Switch } from '@/Components/ui/Checkbox';
export type { CheckboxProps, SwitchProps } from '@/Components/ui/Checkbox';

export {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui/Table';

export { Alert } from '@/Components/ui/Alert';
export type { AlertProps, AlertTone } from '@/Components/ui/Alert';

export {
    Skeleton,
    SkeletonCards,
    SkeletonRows,
    SkeletonText,
} from '@/Components/ui/Skeleton';

export { Dialog } from '@/Components/ui/Dialog';
export type { DialogProps, DialogSize } from '@/Components/ui/Dialog';

export { Tabs } from '@/Components/ui/Tabs';
export type { TabItem, TabsProps } from '@/Components/ui/Tabs';

export { SegmentedControl } from '@/Components/ui/SegmentedControl';
export type {
    SegmentedControlProps,
    SegmentedOption,
} from '@/Components/ui/SegmentedControl';

export { DropdownMenu } from '@/Components/ui/DropdownMenu';
export type {
    DropdownItem,
    DropdownMenuProps,
} from '@/Components/ui/DropdownMenu';

export { ProgressBar } from '@/Components/ui/ProgressBar';
export type { ProgressBarProps } from '@/Components/ui/ProgressBar';

export { Avatar } from '@/Components/ui/Avatar';
export type { AvatarProps } from '@/Components/ui/Avatar';

export { PageHeader } from '@/Components/ui/PageHeader';
export type { PageHeaderProps } from '@/Components/ui/PageHeader';

export {
    Toolbar,
    ToolbarDivider,
    ToolbarGroup,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui/Toolbar';
export type { ToolbarSearchProps } from '@/Components/ui/Toolbar';

export { StatCard, StatGrid } from '@/Components/ui/StatCard';
export type {
    StatCardProps,
    StatTone,
    StatTrend,
} from '@/Components/ui/StatCard';

export { EmptyState } from '@/Components/ui/EmptyState';
export type {
    EmptyStateAction,
    EmptyStateProps,
} from '@/Components/ui/EmptyState';

export { KeyValue, KeyValueGrid } from '@/Components/ui/KeyValue';
export type {
    KeyValueGridProps,
    KeyValueProps,
} from '@/Components/ui/KeyValue';

export { Breadcrumbs } from '@/Components/ui/Breadcrumbs';
export type {
    BreadcrumbItem,
    BreadcrumbsProps,
} from '@/Components/ui/Breadcrumbs';
