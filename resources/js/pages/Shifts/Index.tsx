import {
    CalendarDaysIcon,
    ClockIcon,
    MoonIcon,
    PencilSquareIcon,
    PlusIcon,
    SunIcon,
    TrashIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, router } from '@inertiajs/react';
import { useState, useMemo } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Badge,
    Button,
    Dialog,
    EmptyState,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SegmentedControl,
    Select,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Toolbar,
    ToolbarSearch,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Shift {
    id: number;
    user_id: number;
    shift_date: string;
    shift_type: string;
    start_time: string;
    end_time: string;
    user?: { id: number; name: string; department?: { name: string } | null };
}

interface Props {
    shifts: {
        data: Shift[];
        links: any[];
        meta?: {
            from: number;
            to: number;
            total: number;
        };
        current_page: number;
        last_page: number;
    };
}

const SHIFT_LABELS: Record<string, string> = {
    morning: 'Morning (6AM-2PM)',
    afternoon: 'Afternoon (2PM-10PM)',
    night: 'Night (10PM-6AM)',
};

const SHIFT_TONES: Record<string, BadgeTone> = {
    morning: 'positive',
    afternoon: 'warning',
    night: 'accent',
};

export default function ShiftsIndex({ shifts }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [filterType, setFilterType] = useState('all');
    const [modalOpen, setModalOpen] = useState(false);
    const [editingShift, setEditingShift] = useState<Shift | null>(null);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        user_id: '',
        shift_date: '',
        shift_type: 'morning',
        start_time: '',
        end_time: '',
    });

    const stats = useMemo(() => {
        const total = shifts.data.length;
        const morning = shifts.data.filter(
            (s) => s.shift_type === 'morning',
        ).length;
        const afternoon = shifts.data.filter(
            (s) => s.shift_type === 'afternoon',
        ).length;
        const night = shifts.data.filter(
            (s) => s.shift_type === 'night',
        ).length;

        return { total, morning, afternoon, night };
    }, [shifts.data]);

    const filtered = useMemo(() => {
        let result = shifts.data;

        if (filterType !== 'all') {
            result = result.filter((s) => s.shift_type === filterType);
        }

        if (search) {
            const q = search.toLowerCase();
            result = result.filter((s) =>
                s.user?.name.toLowerCase().includes(q),
            );
        }

        return result;
    }, [shifts.data, filterType, search]);

    const openEdit = (shift: Shift) => {
        setEditingShift(shift);
        setData({
            user_id: String(shift.user_id),
            shift_date: shift.shift_date,
            shift_type: shift.shift_type,
            start_time: shift.start_time,
            end_time: shift.end_time,
        });
        setModalOpen(true);
    };

    const openCreate = () => {
        setEditingShift(null);
        reset();
        setModalOpen(true);
    };

    const handleSubmit = () => {
        if (editingShift) {
            put(route('shifts.update', editingShift.id), {
                onSuccess: () => setModalOpen(false),
            });
        } else {
            post(route('shifts.store'), {
                onSuccess: () => setModalOpen(false),
            });
        }
    };

    const handleDelete = (id: number) => {
        if (confirm('Delete this shift?')) {
            router.delete(route('shifts.destroy', id));
        }
    };

    return (
        <AppLayout
            headerTitle={t('page.shifts_index.title')}
            headerSubtitle={t('page.shifts_index.subtitle')}
        >
            <Head title={t('page.shifts_index.head_title')} />

            <div className="mx-auto max-w-7xl space-y-5 pb-12">
                <PageHeader
                    title={t('page.shifts_index.title')}
                    description={t('page.shifts_index.subtitle')}
                    breadcrumbs={[
                        { label: t('page.shifts_index.breadcrumb_shifts') },
                    ]}
                    actions={
                        <Button icon={PlusIcon} onClick={openCreate}>
                            {t('page.shifts_index.add_shift')}
                        </Button>
                    }
                />

                <StatGrid>
                    <StatCard
                        icon={CalendarDaysIcon}
                        label="Total shifts"
                        value={stats.total}
                        tone="accent"
                        hint="On this page"
                    />
                    <StatCard
                        icon={SunIcon}
                        label="Morning"
                        value={stats.morning}
                        tone="positive"
                        hint="6AM – 2PM"
                    />
                    <StatCard
                        icon={ClockIcon}
                        label="Afternoon"
                        value={stats.afternoon}
                        tone="warning"
                        hint="2PM – 10PM"
                    />
                    <StatCard
                        icon={MoonIcon}
                        label="Night"
                        value={stats.night}
                        tone="neutral"
                        hint="10PM – 6AM"
                    />
                </StatGrid>

                <Toolbar>
                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder={t('page.shifts_index.search_placeholder')}
                    />
                    <SegmentedControl
                        value={filterType}
                        onChange={setFilterType}
                        size="sm"
                        options={[
                            {
                                value: 'all',
                                label: t('page.shifts_index.all'),
                            },
                            {
                                value: 'morning',
                                label: t('page.shifts_index.morning'),
                            },
                            {
                                value: 'afternoon',
                                label: t('page.shifts_index.afternoon'),
                            },
                            {
                                value: 'night',
                                label: t('page.shifts_index.night'),
                            },
                        ]}
                    />
                </Toolbar>

                {filtered.length > 0 ? (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>
                                    {t('page.shifts_index.table_staff')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('page.shifts_index.table_date_type')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('page.shifts_index.table_hours')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    <span className="sr-only">Actions</span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filtered.map((shift) => (
                                <TableRow key={shift.id}>
                                    <TableCell>
                                        <div className="flex items-center gap-3">
                                            <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-[11px] font-semibold text-ink-muted">
                                                {shift.user?.name?.charAt(0) ||
                                                    '?'}
                                            </span>
                                            <div className="min-w-0">
                                                <p className="truncate text-[13px] font-medium text-ink">
                                                    {shift.user?.name}
                                                </p>
                                                <p className="truncate text-[11.5px] text-ink-subtle">
                                                    {shift.user?.department
                                                        ?.name ||
                                                        t(
                                                            'page.shifts_index.general',
                                                        )}
                                                </p>
                                            </div>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex flex-col items-start gap-1">
                                            <span className="text-[13px] font-medium text-ink tabular-nums">
                                                {shift.shift_date}
                                            </span>
                                            <Badge
                                                tone={
                                                    SHIFT_TONES[
                                                        shift.shift_type
                                                    ] ?? 'neutral'
                                                }
                                            >
                                                {SHIFT_LABELS[
                                                    shift.shift_type
                                                ] ?? shift.shift_type}
                                            </Badge>
                                        </div>
                                    </TableCell>
                                    <TableCell muted>
                                        <span className="inline-flex items-center gap-1.5 tabular-nums">
                                            <ClockIcon className="h-3.5 w-3.5 text-ink-subtle" />
                                            {shift.start_time} —{' '}
                                            {shift.end_time}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex justify-end gap-1.5">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                icon={PencilSquareIcon}
                                                aria-label="Edit shift"
                                                onClick={() => openEdit(shift)}
                                            />
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                icon={TrashIcon}
                                                aria-label="Delete shift"
                                                className="hover:bg-critical-soft hover:text-critical-ink"
                                                onClick={() =>
                                                    handleDelete(shift.id)
                                                }
                                            />
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                ) : (
                    <EmptyState
                        icon={ClockIcon}
                        title={t('page.shifts_index.no_shifts')}
                        action={
                            <Button icon={PlusIcon} onClick={openCreate}>
                                {t('page.shifts_index.add_shift')}
                            </Button>
                        }
                    />
                )}

                {shifts.last_page > 1 && (
                    <Pagination links={shifts.links} meta={shifts.meta} />
                )}
            </div>

            <Dialog
                open={modalOpen}
                onClose={() => setModalOpen(false)}
                size="lg"
                title={
                    editingShift
                        ? t('page.shifts_index.edit_shift')
                        : t('page.shifts_index.assign_shift')
                }
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setModalOpen(false)}
                        >
                            {t('common.cancel')}
                        </Button>
                        <Button
                            type="submit"
                            form="shift-form"
                            loading={processing}
                        >
                            {processing
                                ? t('common.processing')
                                : editingShift
                                  ? t('page.shifts_index.update_shift')
                                  : t('page.shifts_index.assign_shift')}
                        </Button>
                    </>
                }
            >
                <form
                    id="shift-form"
                    onSubmit={(e) => {
                        e.preventDefault();
                        handleSubmit();
                    }}
                    className="space-y-5"
                >
                    <FieldSet columns={2}>
                        <Field
                            label={t('page.shifts_index.staff_id')}
                            htmlFor="user_id"
                            required
                            error={errors.user_id}
                        >
                            <Input
                                id="user_id"
                                name="user_id"
                                type="number"
                                icon={UserIcon}
                                value={data.user_id}
                                onChange={(e) =>
                                    setData('user_id', e.target.value)
                                }
                                invalid={Boolean(errors.user_id)}
                                required
                            />
                        </Field>
                        <Field
                            label={t('page.shifts_index.date')}
                            htmlFor="shift_date"
                            required
                            error={errors.shift_date}
                        >
                            <Input
                                id="shift_date"
                                name="shift_date"
                                type="date"
                                value={data.shift_date}
                                onChange={(e) =>
                                    setData('shift_date', e.target.value)
                                }
                                invalid={Boolean(errors.shift_date)}
                                required
                            />
                        </Field>
                        <Field
                            label={t('page.shifts_index.shift_type')}
                            htmlFor="shift_type"
                            error={errors.shift_type}
                        >
                            <Select
                                id="shift_type"
                                name="shift_type"
                                value={data.shift_type}
                                onChange={(e) =>
                                    setData('shift_type', e.target.value)
                                }
                                invalid={Boolean(errors.shift_type)}
                            >
                                <option value="morning">
                                    {t('page.shifts_index.morning_option')}
                                </option>
                                <option value="afternoon">
                                    {t('page.shifts_index.afternoon_option')}
                                </option>
                                <option value="night">
                                    {t('page.shifts_index.night_option')}
                                </option>
                            </Select>
                        </Field>
                        <Field
                            label={t('page.shifts_index.start')}
                            htmlFor="start_time"
                            required
                            error={errors.start_time}
                        >
                            <Input
                                id="start_time"
                                name="start_time"
                                type="time"
                                value={data.start_time}
                                onChange={(e) =>
                                    setData('start_time', e.target.value)
                                }
                                invalid={Boolean(errors.start_time)}
                                required
                            />
                        </Field>
                        <Field
                            label={t('page.shifts_index.end')}
                            htmlFor="end_time"
                            required
                            error={errors.end_time}
                        >
                            <Input
                                id="end_time"
                                name="end_time"
                                type="time"
                                value={data.end_time}
                                onChange={(e) =>
                                    setData('end_time', e.target.value)
                                }
                                invalid={Boolean(errors.end_time)}
                                required
                            />
                        </Field>
                    </FieldSet>
                </form>
            </Dialog>
        </AppLayout>
    );
}
