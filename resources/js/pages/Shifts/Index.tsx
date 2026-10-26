import {
    CalendarDaysIcon,
    ClockIcon,
    MoonIcon,
    PencilSquareIcon,
    PlusIcon,
    SunIcon,
    TrashIcon,
    UserPlusIcon,
    XMarkIcon,
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
    swal,
    Toolbar,
    ToolbarSearch,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface StaffMember {
    id: number;
    name: string;
    department?: { name: string } | null;
}

interface StaffAssignment {
    id: number;
    user_id: number | null;
    name: string | null;
    user?: { name: string; department?: { name: string } | null } | null;
}

interface Shift {
    id: number;
    shift_date: string;
    shift_type: string;
    start_time: string;
    end_time: string;
    staff_assignments: StaffAssignment[];
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
    users: StaffMember[];
}

interface StaffEntry {
    user_id?: number;
    name?: string;
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

export default function ShiftsIndex({ shifts, users }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [filterType, setFilterType] = useState('all');
    const [modalOpen, setModalOpen] = useState(false);
    const [editingShift, setEditingShift] = useState<Shift | null>(null);
    const [staffQuery, setStaffQuery] = useState('');
    const [externalName, setExternalName] = useState('');

    const { data, setData, post, put, processing, errors, reset } = useForm({
        shift_date: '',
        shift_type: 'morning',
        start_time: '',
        end_time: '',
        staff: [] as StaffEntry[],
    });

    const staffError =
        (errors as Record<string, string | undefined>)['staff'] ??
        (errors as Record<string, string | undefined>)['staff.0.name'] ??
        (errors as Record<string, string | undefined>)['staff.0.user_id'];

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
                s.staff_assignments.some((a) =>
                    (a.user?.name ?? a.name ?? '').toLowerCase().includes(q),
                ),
            );
        }

        return result;
    }, [shifts.data, filterType, search]);

    const openEdit = (shift: Shift) => {
        setEditingShift(shift);
        setStaffQuery('');
        setExternalName('');
        setData({
            shift_date: shift.shift_date,
            shift_type: shift.shift_type,
            start_time: shift.start_time,
            end_time: shift.end_time,
            staff: shift.staff_assignments.map((a) =>
                a.user_id != null
                    ? { user_id: a.user_id }
                    : { name: a.name ?? '' },
            ),
        });
        setModalOpen(true);
    };

    const openCreate = () => {
        setEditingShift(null);
        setStaffQuery('');
        setExternalName('');
        reset();
        setModalOpen(true);
    };

    const selectedUserIds = useMemo(
        () => new Set(data.staff.map((s) => s.user_id).filter(Boolean)),
        [data.staff],
    );

    const filteredUsers = useMemo(() => {
        if (!staffQuery) {
            return users;
        }

        const q = staffQuery.toLowerCase();

        return users.filter((u) => u.name.toLowerCase().includes(q));
    }, [users, staffQuery]);

    const toggleUser = (userId: number) => {
        if (selectedUserIds.has(userId)) {
            setData(
                'staff',
                data.staff.filter((s) => s.user_id !== userId),
            );
        } else {
            setData('staff', [...data.staff, { user_id: userId }]);
        }
    };

    const addExternal = () => {
        const name = externalName.trim();

        if (!name || data.staff.some((s) => s.name === name)) {
            return;
        }

        setData('staff', [...data.staff, { name }]);
        setExternalName('');
    };

    const removeStaffEntry = (index: number) => {
        setData(
            'staff',
            data.staff.filter((_, i) => i !== index),
        );
    };

    const staffLabel = (entry: StaffEntry): string => {
        if (entry.user_id) {
            const found = users.find((u) => u.id === entry.user_id);

            return found ? found.name : `User #${entry.user_id}`;
        }

        return entry.name ?? '';
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

    const handleDelete = async (id: number) => {
        const confirmed = await swal.confirm({
            tone: 'warning',
            danger: true,
            title: t('page.shifts_index.delete_shift', 'Delete this shift?'),
            message: t(
                'page.shifts_index.delete_confirm',
                'This removes the shift from the roster. It cannot be undone.',
            ),
            confirmLabel: t('common.delete', 'Delete'),
            cancelLabel: t('common.cancel', 'Cancel'),
        });

        if (!confirmed) {
            return;
        }

        router.delete(route('shifts.destroy', id));
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
                                        <div className="flex max-w-md flex-wrap gap-1.5">
                                            {shift.staff_assignments.length ===
                                                0 && (
                                                <span className="text-[12px] text-ink-subtle italic">
                                                    Unassigned
                                                </span>
                                            )}
                                            {shift.staff_assignments.map(
                                                (a) => {
                                                    const name =
                                                        a.user?.name ??
                                                        a.name ??
                                                        '';

                                                    return (
                                                        <span
                                                            key={a.id}
                                                            className="inline-flex items-center gap-1 rounded-full border border-line bg-surface-muted px-2 py-0.5 text-[11.5px] font-medium text-ink-muted"
                                                            title={
                                                                a.user
                                                                    ?.department
                                                                    ?.name ??
                                                                a.name ??
                                                                undefined
                                                            }
                                                        >
                                                            {name}
                                                        </span>
                                                    );
                                                },
                                            )}
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
                                                aria-label="Edit staff"
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

                    <Field
                        label="Assigned staff"
                        error={staffError}
                        hint="Use the list to toggle system users, or record someone not in the system by name."
                    >
                        {data.staff.length > 0 ? (
                            <div className="mb-2 flex flex-wrap gap-1.5">
                                {data.staff.map((entry, idx) => (
                                    <span
                                        key={
                                            entry.user_id
                                                ? `user-${entry.user_id}`
                                                : `name-${entry.name}-${idx}`
                                        }
                                        className="inline-flex items-center gap-1 rounded-full border border-line bg-surface-muted py-0.5 pr-1 pl-2 text-[12px] font-medium text-ink-muted"
                                    >
                                        {staffLabel(entry)}
                                        <button
                                            type="button"
                                            onClick={() =>
                                                removeStaffEntry(idx)
                                            }
                                            className="rounded-full p-0.5 text-ink-subtle hover:bg-critical-soft hover:text-critical-ink"
                                            aria-label="Remove from shift"
                                        >
                                            <XMarkIcon className="h-3 w-3" />
                                        </button>
                                    </span>
                                ))}
                            </div>
                        ) : (
                            <p className="mb-2 text-xs text-ink-subtle italic">
                                No staff on this shift yet
                            </p>
                        )}

                        <div className="rounded-lg border border-line bg-surface-muted p-2">
                            <Input
                                id="staff-search"
                                type="search"
                                value={staffQuery}
                                onChange={(e) => setStaffQuery(e.target.value)}
                                placeholder="Search staff by name"
                            />
                            <div className="mt-1 max-h-44 divide-y divide-line overflow-y-auto">
                                {filteredUsers.length === 0 && (
                                    <p className="px-2 py-2 text-xs text-ink-subtle">
                                        No matching staff
                                    </p>
                                )}
                                {filteredUsers.map((user) => (
                                    <label
                                        key={user.id}
                                        className="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-[13px] text-ink hover:bg-surface-hover"
                                    >
                                        <input
                                            type="checkbox"
                                            className="h-4 w-4 rounded accent-accent"
                                            checked={selectedUserIds.has(
                                                user.id,
                                            )}
                                            onChange={() => toggleUser(user.id)}
                                        />
                                        <span className="font-medium">
                                            {user.name}
                                        </span>
                                        <span className="ml-auto text-[11px] text-ink-subtle">
                                            {user.department?.name ?? ''}
                                        </span>
                                    </label>
                                ))}
                            </div>
                            <div className="mt-2 flex gap-2 border-t border-line pt-2">
                                <Input
                                    id="external-name"
                                    type="text"
                                    value={externalName}
                                    onChange={(e) =>
                                        setExternalName(e.target.value)
                                    }
                                    icon={UserPlusIcon}
                                    placeholder="Someone not in the system"
                                    onKeyDown={(e) => {
                                        if (e.key === 'Enter') {
                                            e.preventDefault();
                                            addExternal();
                                        }
                                    }}
                                />
                                <Button
                                    type="button"
                                    variant="secondary"
                                    onClick={addExternal}
                                    disabled={!externalName.trim()}
                                >
                                    {t('Add')}
                                </Button>
                            </div>
                        </div>
                    </Field>
                </form>
            </Dialog>
        </AppLayout>
    );
}
