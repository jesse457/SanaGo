import {
    CalendarDaysIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
} from '@heroicons/react/24/outline';
import {
    addDays,
    addMonths,
    addWeeks,
    eachDayOfInterval,
    endOfMonth,
    endOfWeek,
    format,
    isSameMonth,
    isToday,
    parseISO,
    startOfMonth,
    startOfWeek,
} from 'date-fns';
import { AnimatePresence, motion } from 'framer-motion';
import { useMemo, useState } from 'react';
import { Button, Dialog, SegmentedControl } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';
import {
    AppointmentIdentity,
    AppointmentStatusBadge,
} from '@/pages/Appointments/components/AppointmentIdentity';
import {
    statusChipClass,
    statusDotClass,
} from '@/pages/Appointments/components/status';

export interface CalendarAppointment {
    id: number;
    patient: {
        id: number;
        first_name: string;
        last_name: string;
        patient_uid?: string;
    };
    doctor?: { id: number; name: string } | null;
    appointment_date: string;
    appointment_time: string;
    status: string;
    consultation_fee?: number | string | null;
    reason_for_visit?: string | null;
}

interface Props {
    appointments: CalendarAppointment[];
}

type CalendarView = 'month' | 'week' | 'day';

const HOUR_HEIGHT = 56;
const SLOT_MINUTES = 30;
const DEFAULT_START_HOUR = 8;
const DEFAULT_END_HOUR = 18;
const WEEK_STARTS_ON = 1 as const;

interface LayoutItem {
    appointment: CalendarAppointment;
    start: number;
    end: number;
    column?: number;
}

interface PositionedItem extends LayoutItem {
    left: number;
    width: number;
}

function minutesOf(time: string): number {
    const [hours = 0, minutes = 0] = time.split(':').map(Number);

    return hours * 60 + minutes;
}

function timeLabel(time: string): string {
    return time.slice(0, 5);
}

function dayKey(day: Date): string {
    return format(day, 'yyyy-MM-dd');
}

function appointmentDayKey(appointment: CalendarAppointment): string {
    return appointment.appointment_date.slice(0, 10);
}

function patientName(appointment: CalendarAppointment): string {
    return `${appointment.patient.first_name} ${appointment.patient.last_name}`;
}

/** Column layout for overlapping events inside a single day. */
function layoutDay(appointments: CalendarAppointment[]): PositionedItem[] {
    const items: LayoutItem[] = appointments
        .map((appointment) => {
            const start = minutesOf(appointment.appointment_time);

            return {
                appointment,
                start,
                end: start + SLOT_MINUTES,
            };
        })
        .sort((a, b) => a.start - b.start);

    const positioned: PositionedItem[] = [];
    let group: LayoutItem[] = [];
    let groupEnd = -1;

    const flush = () => {
        if (group.length === 0) {
            return;
        }

        const columnEnds: number[] = [];

        group.forEach((item) => {
            let column = columnEnds.findIndex((end) => end <= item.start);

            if (column === -1) {
                column = columnEnds.length;
                columnEnds.push(item.end);
            } else {
                columnEnds[column] = item.end;
            }

            item.column = column;
        });

        const totalColumns = columnEnds.length;

        group.forEach((item) => {
            const column = item.column ?? 0;

            positioned.push({
                ...item,
                left: column / totalColumns,
                width: 1 / totalColumns,
            });
        });

        group = [];
        groupEnd = -1;
    };

    items.forEach((item) => {
        if (group.length > 0 && item.start >= groupEnd) {
            flush();
        }

        group.push(item);
        groupEnd = Math.max(groupEnd, item.end);
    });

    flush();

    return positioned;
}

/**
 * AppointmentCalendar — Google Calendar-style month/week/day views built from
 * the app's semantic tokens, so it follows the system theme automatically.
 */
export function AppointmentCalendar({ appointments }: Props) {
    const { t } = useTranslation();

    const [view, setView] = useState<CalendarView>('month');
    const [anchor, setAnchor] = useState(() => new Date());
    const [selected, setSelected] = useState<CalendarAppointment | null>(null);

    const appointmentsByDay = useMemo(() => {
        const map = new Map<string, CalendarAppointment[]>();

        appointments.forEach((appointment) => {
            const key = appointmentDayKey(appointment);
            const bucket = map.get(key);

            if (bucket) {
                bucket.push(appointment);
            } else {
                map.set(key, [appointment]);
            }
        });

        map.forEach((bucket) => {
            bucket.sort(
                (a, b) =>
                    minutesOf(a.appointment_time) -
                    minutesOf(b.appointment_time),
            );
        });

        return map;
    }, [appointments]);

    const timeBounds = useMemo(() => {
        if (appointments.length === 0) {
            return { start: DEFAULT_START_HOUR, end: DEFAULT_END_HOUR };
        }

        const starts = appointments.map((appointment) =>
            Math.floor(minutesOf(appointment.appointment_time) / 60),
        );
        const ends = appointments.map((appointment) =>
            Math.ceil(
                (minutesOf(appointment.appointment_time) + SLOT_MINUTES) / 60,
            ),
        );

        return {
            start: Math.max(0, Math.min(DEFAULT_START_HOUR, ...starts)),
            end: Math.min(24, Math.max(DEFAULT_END_HOUR, ...ends)),
        };
    }, [appointments]);

    const monthDays = useMemo(() => {
        const start = startOfWeek(startOfMonth(anchor), {
            weekStartsOn: WEEK_STARTS_ON,
        });
        const end = endOfWeek(endOfMonth(anchor), {
            weekStartsOn: WEEK_STARTS_ON,
        });

        return eachDayOfInterval({ start, end });
    }, [anchor]);

    const weekDays = useMemo(() => {
        const start = startOfWeek(anchor, { weekStartsOn: WEEK_STARTS_ON });
        const end = endOfWeek(anchor, { weekStartsOn: WEEK_STARTS_ON });

        return eachDayOfInterval({ start, end });
    }, [anchor]);

    const visibleDays = view === 'day' ? [anchor] : weekDays;

    const goToToday = () => setAnchor(new Date());

    const navigate = (direction: -1 | 1) => {
        if (view === 'month') {
            setAnchor(addMonths(anchor, direction));
        } else if (view === 'week') {
            setAnchor(addWeeks(anchor, direction));
        } else {
            setAnchor(addDays(anchor, direction));
        }
    };

    const title =
        view === 'month'
            ? format(anchor, 'MMMM yyyy')
            : view === 'week'
              ? `${format(weekDays[0], 'MMM d')} – ${format(
                    weekDays[weekDays.length - 1],
                    'MMM d, yyyy',
                )}`
              : format(anchor, 'EEEE, MMMM d, yyyy');

    const weekDaysLabels = [
        t('page.appointment_calendar.weekdays.mon'),
        t('page.appointment_calendar.weekdays.tue'),
        t('page.appointment_calendar.weekdays.wed'),
        t('page.appointment_calendar.weekdays.thu'),
        t('page.appointment_calendar.weekdays.fri'),
        t('page.appointment_calendar.weekdays.sat'),
        t('page.appointment_calendar.weekdays.sun'),
    ];

    const hours = Array.from(
        { length: timeBounds.end - timeBounds.start },
        (_, index) => timeBounds.start + index,
    );
    const gridHeight = (timeBounds.end - timeBounds.start) * HOUR_HEIGHT;

    const renderMonth = () => (
        <div className="overflow-x-auto rounded-xl border border-line bg-surface shadow-surface">
            <div className="min-w-[760px]">
                <div className="grid grid-cols-7 border-b border-line bg-surface-muted/60">
                    {weekDaysLabels.map((label) => (
                        <div
                            key={label}
                            className="px-2 py-2.5 text-center text-[10px] font-semibold tracking-[0.14em] text-ink-subtle uppercase"
                        >
                            {label}
                        </div>
                    ))}
                </div>

                <div className="grid grid-cols-7">
                    {monthDays.map((day) => {
                        const dayAppointments =
                            appointmentsByDay.get(dayKey(day)) ?? [];
                        const inMonth = isSameMonth(day, anchor);
                        const visible = dayAppointments.slice(0, 3);
                        const overflow =
                            dayAppointments.length - visible.length;

                        return (
                            <div
                                key={day.toISOString()}
                                className={cn(
                                    'min-h-[116px] border-r border-b border-line p-1.5 last:border-r-0',
                                    !inMonth && 'bg-surface-muted/40',
                                )}
                            >
                                <div className="mb-1 flex items-center justify-between">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setAnchor(day);
                                            setView('day');
                                        }}
                                        className={cn(
                                            'flex h-6 min-w-6 items-center justify-center rounded-full px-1 text-[11.5px] font-semibold tabular-nums transition-colors duration-150 ease-premium hover:bg-surface-hover',
                                            isToday(day)
                                                ? 'bg-accent text-ink-inverse hover:bg-accent-hover'
                                                : inMonth
                                                  ? 'text-ink'
                                                  : 'text-ink-subtle',
                                        )}
                                        aria-label={format(day, 'PPPP')}
                                    >
                                        {format(day, 'd')}
                                    </button>
                                </div>

                                <div className="space-y-0.5">
                                    {visible.map((appointment) => (
                                        <button
                                            key={appointment.id}
                                            type="button"
                                            onClick={() =>
                                                setSelected(appointment)
                                            }
                                            title={`${timeLabel(
                                                appointment.appointment_time,
                                            )} ${patientName(appointment)}`}
                                            className={cn(
                                                'flex w-full items-center gap-1 truncate rounded-[5px] border-l-2 px-1.5 py-0.5 text-left text-[11px] leading-4 font-medium transition-colors duration-150 ease-premium',
                                                statusChipClass(
                                                    appointment.status,
                                                ),
                                            )}
                                        >
                                            <span className="shrink-0 tabular-nums opacity-70">
                                                {timeLabel(
                                                    appointment.appointment_time,
                                                )}
                                            </span>
                                            <span className="truncate">
                                                {patientName(appointment)}
                                            </span>
                                        </button>
                                    ))}

                                    {overflow > 0 && (
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setAnchor(day);
                                                setView('day');
                                            }}
                                            className="w-full rounded-[5px] px-1.5 py-0.5 text-left text-[10.5px] font-semibold text-ink-subtle transition-colors duration-150 hover:bg-surface-hover hover:text-ink"
                                        >
                                            +{overflow}{' '}
                                            {t(
                                                'page.appointment_calendar.more',
                                            )}
                                        </button>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </div>
    );

    const renderTimeGrid = () => (
        <div className="overflow-hidden rounded-xl border border-line bg-surface shadow-surface">
            <div className="overflow-x-auto">
                <div className="min-w-[720px]">
                    <div
                        className="grid border-b border-line bg-surface-muted/60"
                        style={{
                            gridTemplateColumns: `56px repeat(${visibleDays.length}, minmax(0, 1fr))`,
                        }}
                    >
                        <div />
                        {visibleDays.map((day) => (
                            <div
                                key={day.toISOString()}
                                className="border-l border-line px-2 py-2 text-center"
                            >
                                <p className="text-[10px] font-semibold tracking-[0.14em] text-ink-subtle uppercase">
                                    {weekDaysLabels[(day.getDay() + 6) % 7]}
                                </p>
                                <button
                                    type="button"
                                    onClick={() => {
                                        setAnchor(day);
                                        setView('day');
                                    }}
                                    className={cn(
                                        'mx-auto mt-0.5 flex h-7 min-w-7 items-center justify-center rounded-full px-1 text-[13px] font-semibold tabular-nums transition-colors duration-150 ease-premium hover:bg-surface-hover',
                                        isToday(day)
                                            ? 'bg-accent text-ink-inverse hover:bg-accent-hover'
                                            : 'text-ink',
                                    )}
                                >
                                    {format(day, 'd')}
                                </button>
                            </div>
                        ))}
                    </div>

                    <div className="flex max-h-[620px] overflow-y-auto">
                        <div
                            className="relative w-14 shrink-0"
                            style={{ height: gridHeight }}
                        >
                            {hours.map((hour, index) => (
                                <span
                                    key={hour}
                                    className="absolute right-2 -translate-y-1/2 text-[10px] font-medium text-ink-subtle tabular-nums"
                                    style={{ top: index * HOUR_HEIGHT }}
                                >
                                    {String(hour).padStart(2, '0')}:00
                                </span>
                            ))}
                        </div>

                        <div
                            className="grid flex-1"
                            style={{
                                gridTemplateColumns: `repeat(${visibleDays.length}, minmax(0, 1fr))`,
                            }}
                        >
                            {visibleDays.map((day) => {
                                const dayAppointments =
                                    appointmentsByDay.get(dayKey(day)) ?? [];
                                const positioned = layoutDay(dayAppointments);
                                const now = new Date();
                                const nowMinutes =
                                    now.getHours() * 60 + now.getMinutes();
                                const showNow =
                                    isToday(day) &&
                                    nowMinutes >= timeBounds.start * 60 &&
                                    nowMinutes <= timeBounds.end * 60;

                                return (
                                    <div
                                        key={day.toISOString()}
                                        className={cn(
                                            'relative border-l border-line',
                                            isToday(day) && 'bg-accent-soft/20',
                                        )}
                                        style={{ height: gridHeight }}
                                    >
                                        {hours.map((hour, index) => (
                                            <div
                                                key={hour}
                                                className="pointer-events-none absolute inset-x-0 border-t border-line/70"
                                                style={{
                                                    top: index * HOUR_HEIGHT,
                                                }}
                                            />
                                        ))}

                                        {positioned.map(
                                            ({
                                                appointment,
                                                start,
                                                end,
                                                left,
                                                width,
                                            }) => {
                                                const top =
                                                    ((start -
                                                        timeBounds.start * 60) /
                                                        60) *
                                                    HOUR_HEIGHT;
                                                const height = Math.max(
                                                    24,
                                                    ((end - start) / 60) *
                                                        HOUR_HEIGHT -
                                                        2,
                                                );

                                                return (
                                                    <button
                                                        key={appointment.id}
                                                        type="button"
                                                        onClick={() =>
                                                            setSelected(
                                                                appointment,
                                                            )
                                                        }
                                                        className={cn(
                                                            'absolute z-10 overflow-hidden rounded-[5px] border-l-2 px-1.5 py-1 text-left text-[11px] leading-tight font-medium shadow-surface transition-[filter,box-shadow] duration-150 ease-premium hover:brightness-[0.98]',
                                                            statusChipClass(
                                                                appointment.status,
                                                            ),
                                                        )}
                                                        style={{
                                                            top,
                                                            height,
                                                            left: `calc(${left * 100}% + 2px)`,
                                                            width: `calc(${width * 100}% - 4px)`,
                                                        }}
                                                    >
                                                        <span className="block truncate tabular-nums opacity-70">
                                                            {timeLabel(
                                                                appointment.appointment_time,
                                                            )}
                                                        </span>
                                                        <span className="block truncate font-semibold">
                                                            {patientName(
                                                                appointment,
                                                            )}
                                                        </span>
                                                        {height > 46 && (
                                                            <span className="block truncate text-[10px] opacity-75">
                                                                {appointment
                                                                    .doctor
                                                                    ?.name ??
                                                                    ''}
                                                            </span>
                                                        )}
                                                    </button>
                                                );
                                            },
                                        )}

                                        {showNow && (
                                            <div
                                                className="pointer-events-none absolute inset-x-0 z-20"
                                                style={{
                                                    top:
                                                        ((nowMinutes -
                                                            timeBounds.start *
                                                                60) /
                                                            60) *
                                                        HOUR_HEIGHT,
                                                }}
                                            >
                                                <div className="relative h-px bg-critical">
                                                    <span className="absolute -top-[3px] -left-[3px] h-[7px] w-[7px] rounded-full bg-critical" />
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );

    return (
        <div className="space-y-4">
            <div className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div className="flex flex-wrap items-center gap-2">
                    <Button variant="secondary" size="sm" onClick={goToToday}>
                        {t('page.appointment_calendar.common.today')}
                    </Button>
                    <div className="flex items-center">
                        <Button
                            variant="ghost"
                            size="icon"
                            icon={ChevronLeftIcon}
                            onClick={() => navigate(-1)}
                            aria-label={t(
                                'page.appointment_calendar.common.previous',
                            )}
                        />
                        <Button
                            variant="ghost"
                            size="icon"
                            icon={ChevronRightIcon}
                            onClick={() => navigate(1)}
                            aria-label={t(
                                'page.appointment_calendar.common.next',
                            )}
                        />
                    </div>
                    <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                        {title}
                    </h2>
                </div>

                <div className="flex flex-wrap items-center gap-3">
                    <div className="hidden items-center gap-3 md:flex">
                        {[
                            'scheduled',
                            'in_consultation',
                            'completed',
                            'cancelled',
                        ].map((status) => (
                            <span
                                key={status}
                                className="flex items-center gap-1.5 text-[11px] font-medium text-ink-subtle"
                            >
                                <span
                                    className={cn(
                                        'h-1.5 w-1.5 rounded-full',
                                        statusDotClass(status),
                                    )}
                                />
                                {t(
                                    `page.appointment_calendar.status.${status}`,
                                )}
                            </span>
                        ))}
                    </div>
                    <SegmentedControl
                        value={view}
                        onChange={setView}
                        options={[
                            {
                                value: 'month',
                                label: t(
                                    'page.appointment_calendar.views.month',
                                ),
                            },
                            {
                                value: 'week',
                                label: t(
                                    'page.appointment_calendar.views.week',
                                ),
                            },
                            {
                                value: 'day',
                                label: t('page.appointment_calendar.views.day'),
                            },
                        ]}
                    />
                </div>
            </div>

            {appointments.length === 0 && (
                <div className="flex items-center gap-2 rounded-lg border border-line bg-surface-muted px-4 py-3 text-[12.5px] text-ink-muted">
                    <CalendarDaysIcon className="h-4 w-4 shrink-0 text-ink-subtle" />
                    {t('page.appointment_calendar.no_appointments_desc')}
                </div>
            )}

            <AnimatePresence mode="wait" initial={false}>
                <motion.div
                    key={view}
                    initial={{ opacity: 0, y: 6 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: -6 }}
                    transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
                >
                    {view === 'month' ? renderMonth() : renderTimeGrid()}
                </motion.div>
            </AnimatePresence>

            <Dialog
                open={selected !== null}
                onClose={() => setSelected(null)}
                title={t('page.appointment_calendar.details.title')}
                size="md"
                footer={
                    <Button
                        variant="secondary"
                        onClick={() => setSelected(null)}
                    >
                        {t('page.appointment_calendar.common.close')}
                    </Button>
                }
            >
                {selected && (
                    <div className="space-y-5">
                        <div className="flex items-start justify-between gap-3">
                            <AppointmentIdentity
                                firstName={selected.patient.first_name}
                                lastName={selected.patient.last_name}
                                patientUid={selected.patient.patient_uid}
                            />
                            <AppointmentStatusBadge
                                status={selected.status}
                                label={t(
                                    `page.appointment_calendar.status.${selected.status}`,
                                    selected.status,
                                )}
                            />
                        </div>

                        <dl className="grid grid-cols-2 gap-x-4 gap-y-3 rounded-lg border border-line bg-surface-muted px-4 py-3">
                            <div>
                                <dt className="text-[10px] font-semibold tracking-[0.12em] text-ink-subtle uppercase">
                                    {t(
                                        'page.appointment_calendar.details.doctor',
                                    )}
                                </dt>
                                <dd className="mt-0.5 text-[13px] font-medium text-ink">
                                    {selected.doctor?.name ?? '—'}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-[10px] font-semibold tracking-[0.12em] text-ink-subtle uppercase">
                                    {t(
                                        'page.appointment_calendar.details.date_time',
                                    )}
                                </dt>
                                <dd className="mt-0.5 text-[13px] font-medium text-ink tabular-nums">
                                    {format(
                                        parseISO(
                                            selected.appointment_date.slice(
                                                0,
                                                10,
                                            ),
                                        ),
                                        'MMM d, yyyy',
                                    )}{' '}
                                    · {timeLabel(selected.appointment_time)}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-[10px] font-semibold tracking-[0.12em] text-ink-subtle uppercase">
                                    {t(
                                        'page.appointment_calendar.details.consultation_fee',
                                    )}
                                </dt>
                                <dd className="mt-0.5 text-[13px] font-medium text-ink tabular-nums">
                                    {selected.consultation_fee ?? '—'}
                                </dd>
                            </div>
                        </dl>

                        {selected.reason_for_visit && (
                            <div>
                                <p className="text-[10px] font-semibold tracking-[0.12em] text-ink-subtle uppercase">
                                    {t(
                                        'page.appointment_calendar.details.reason',
                                    )}
                                </p>
                                <p className="mt-1 text-[13px] leading-relaxed text-ink-muted">
                                    {selected.reason_for_visit}
                                </p>
                            </div>
                        )}
                    </div>
                )}
            </Dialog>
        </div>
    );
}
