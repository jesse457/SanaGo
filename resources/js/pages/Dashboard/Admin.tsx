import {
    ArrowDownRightIcon,
    ArrowUpRightIcon,
    BanknotesIcon,
    BeakerIcon,
    BuildingOfficeIcon,
    CalendarIcon,
    CameraIcon,
    ChartBarIcon,
    CheckCircleIcon,
    ClipboardDocumentCheckIcon,
    ClockIcon,
    ExclamationCircleIcon,
    HeartIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Filler,
    Legend,
} from 'chart.js';
import type { ChartOptions } from 'chart.js';
import { useEffect, useState, useRef } from 'react';
import { Line } from 'react-chartjs-2';
import {
    Badge,
    Button,
    Card,
    CardHeader,
    CardTitle,
    EmptyState,
    PageHeader,
    StatGrid,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { ActivityFeed } from '@/pages/Dashboard/components/ActivityFeed';
import {
    AdminMetricCard,
    BreakdownRow,
    ClinicalChip,
    DashboardTable,
    PanelLinkCard,
} from '@/pages/Dashboard/components/AdminPanels';
import type { AdminDashboardMetrics } from '@/types/dashboard';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Filler,
    Legend,
);

interface DashboardSection {
    metricCards?: { type: string; accent: string }[];
    clinicalChips?: string[];
    quickActions?: { label: string; href: string; icon: string }[];
    showRevenueChart?: boolean;
    showProfitLoss?: boolean;
    showOutstandingInvoices?: boolean;
    showRevenueBreakdown?: boolean;
    showRecentActivity?: boolean;
    showRevenueLink?: boolean;
    showPharmacyPanel?: boolean;
    showLabPanel?: boolean;
    showDoctorPanel?: boolean;
    showReceptionPanel?: boolean;
}

interface Props {
    metrics: AdminDashboardMetrics;
    sections?: DashboardSection;
    isRoleAgnostic?: boolean;
}

/* ─── Animated Counter Hook ─── */
function useAnimatedValue(target: number, duration = 1200) {
    const [value, setValue] = useState(0);
    const rafRef = useRef<number>(0);

    useEffect(() => {
        const start = performance.now();
        const animate = (now: number) => {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            // ease-out cubic
            const eased = 1 - Math.pow(1 - progress, 3);
            setValue(Math.round(eased * target));

            if (progress < 1) {
                rafRef.current = requestAnimationFrame(animate);
            }
        };
        rafRef.current = requestAnimationFrame(animate);

        return () => cancelAnimationFrame(rafRef.current);
    }, [target, duration]);

    return value;
}

/* ─── Trend Badge ─── */
function TrendBadge({ value, label }: { value: number; label?: string }) {
    const isUp = value >= 0;
    const Icon = isUp ? ArrowUpRightIcon : ArrowDownRightIcon;

    return (
        <span
            className={`inline-flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[10.5px] font-semibold tabular-nums ${
                isUp
                    ? 'bg-positive-soft text-positive-ink'
                    : 'bg-critical-soft text-critical-ink'
            }`}
        >
            <Icon className="h-3 w-3" />
            {Math.abs(value)}%{label ? ` ${label}` : ''}
        </span>
    );
}

/* ─── Main Dashboard ─── */
export default function Dashboard({
    metrics,
    sections,
    isRoleAgnostic,
}: Props) {
    const { t } = useTranslation();
    const [mounted, setMounted] = useState(false);

    // Helper: is a section visible? Admin sees everything; others go by sections config.
    const show = (key: keyof DashboardSection): boolean => {
        if (!isRoleAgnostic) {
            return true;
        } // admin sees all

        return (
            sections?.[key] === true ||
            (Array.isArray(sections?.[key]) &&
                (sections[key] as any[]).length > 0)
        );
    };
    const hasChip = (name: string): boolean => {
        if (!isRoleAgnostic) {
            return true;
        }

        return sections?.clinicalChips?.includes(name) ?? false;
    };
    const metricCards = isRoleAgnostic
        ? (sections?.metricCards ?? [])
        : [
              { type: 'patients', accent: 'blue' },
              { type: 'admissions', accent: 'violet' },
              { type: 'appointments', accent: 'amber' },
              { type: 'revenue', accent: 'emerald' },
          ];
    const quickActions = isRoleAgnostic
        ? (sections?.quickActions ?? [])
        : [
              {
                  label: t('page.admin_dashboard.new_patient'),
                  href: '/patients/create',
                  icon: 'users',
              },
              {
                  label: t('page.admin_dashboard.book_appointment'),
                  href: '/appointments/create',
                  icon: 'calendar',
              },
          ];

    // Icon map for dynamic quick-action icons
    const ICON_MAP: Record<
        string,
        React.ComponentType<{ className?: string }>
    > = {
        users: UserGroupIcon,
        calendar: CalendarIcon,
        clipboard: ClipboardDocumentCheckIcon,
        exclamation: ExclamationCircleIcon,
        camera: CameraIcon,
        beaker: BeakerIcon,
        banknotes: BanknotesIcon,
    };

    const MONTHS = [
        t('common.months.jan'),
        t('common.months.feb'),
        t('common.months.mar'),
        t('common.months.apr'),
        t('common.months.may'),
        t('common.months.jun'),
        t('common.months.jul'),
        t('common.months.aug'),
        t('common.months.sep'),
        t('common.months.oct'),
        t('common.months.nov'),
        t('common.months.dec'),
    ];

    useEffect(() => {
        // One-shot mount signal (client-only rendering after hydration).
        // eslint-disable-next-line react-hooks/set-state-in-effect
        setMounted(true);
    }, []);

    const {
        patient_stats,
        appointment_stats,
        clinical_stats,
        emergency_stats,
        revenue_summary,
        outstanding_invoices: outstandingInvoicesRaw,
        outstanding_invoices_count: outstandingCountRaw,
        bed_occupancy,
        monthly_data,
        recent_activity: recentActivityRaw,
    } = metrics;

    const totalPatients = useAnimatedValue(patient_stats?.total ?? 0);
    const activeAdmissions = useAnimatedValue(
        patient_stats?.active_admissions ?? 0,
    );
    const todaysAppointments = useAnimatedValue(appointment_stats?.total ?? 0);
    const revenueToday = useAnimatedValue(revenue_summary?.total_revenue || 0);
    const outstanding_invoices = outstandingInvoicesRaw ?? 0;
    const outstanding_invoices_count = outstandingCountRaw ?? 0;
    const recent_activity = recentActivityRaw ?? [];
    const outstandingCount = useAnimatedValue(outstanding_invoices_count);
    const activeEmergencies = useAnimatedValue(
        emergency_stats?.active_count ?? 0,
    );

    // Compute simple month-over-month trends
    const mData = monthly_data ?? [];
    const lastMonth = mData[mData.length - 2];
    const currentMonth = mData[mData.length - 1];
    const revenueTrend =
        lastMonth && lastMonth.revenue
            ? Math.round(
                  (((currentMonth?.revenue || 0) - lastMonth.revenue) /
                      lastMonth.revenue) *
                      100,
              )
            : 0;
    const expenditureTrend =
        lastMonth && lastMonth.expenditure
            ? Math.round(
                  (((currentMonth?.expenditure || 0) - lastMonth.expenditure) /
                      lastMonth.expenditure) *
                      100,
              )
            : 0;

    const chartData = {
        labels: mData.map((d) => MONTHS[parseInt(d.month) - 1] || d.month),
        datasets: [
            {
                label: t('page.admin_dashboard.revenue'),
                data: mData.map((d) => d.revenue),
                borderColor: '#10b981',
                backgroundColor: (ctx: any) => {
                    const chart = ctx.chart;
                    const { ctx: canvasCtx, chartArea } = chart;

                    if (!chartArea) {
                        return 'rgba(16,185,129,0.1)';
                    }

                    const gradient = canvasCtx.createLinearGradient(
                        0,
                        chartArea.top,
                        0,
                        chartArea.bottom,
                    );
                    gradient.addColorStop(0, 'rgba(16,185,129,0.2)');
                    gradient.addColorStop(1, 'rgba(16,185,129,0)');

                    return gradient;
                },
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#10b981',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2,
            },
            {
                label: t('page.admin_dashboard.expenditure'),
                data: mData.map((d) => d.expenditure),
                borderColor: '#f43f5e',
                backgroundColor: (ctx: any) => {
                    const chart = ctx.chart;
                    const { ctx: canvasCtx, chartArea } = chart;

                    if (!chartArea) {
                        return 'rgba(244,63,94,0.05)';
                    }

                    const gradient = canvasCtx.createLinearGradient(
                        0,
                        chartArea.top,
                        0,
                        chartArea.bottom,
                    );
                    gradient.addColorStop(0, 'rgba(244,63,94,0.12)');
                    gradient.addColorStop(1, 'rgba(244,63,94,0)');

                    return gradient;
                },
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#f43f5e',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2,
            },
        ],
    };

    const chartOptions: ChartOptions<'line'> = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                align: 'end',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    boxWidth: 6,
                    padding: 20,
                    font: { size: 12, weight: 500 },
                    color: '#6b7280',
                },
            },
            tooltip: {
                backgroundColor: '#1f2937',
                titleFont: { size: 12 },
                bodyFont: { size: 12 },
                padding: 12,
                cornerRadius: 8,
                displayColors: true,
                boxWidth: 8,
                boxHeight: 8,
                boxPadding: 4,
                callbacks: {
                    label: (ctx: any) =>
                        ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString()} FCFA`,
                },
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                border: { display: false },
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: {
                    callback: (v: any) => `${(v / 1000).toFixed(0)}k`,
                    font: { size: 11 },
                    color: '#9ca3af',
                    padding: 8,
                },
            },
            x: {
                border: { display: false },
                grid: { display: false },
                ticks: { font: { size: 11 }, color: '#9ca3af', padding: 4 },
            },
        },
    };

    const maxProfit = Math.max(...mData.map((d) => Math.abs(d.profit)), 1);

    // Revenue breakdown total
    const breakdownTotal =
        (revenue_summary?.invoices || 0) +
        (revenue_summary?.surgeries || 0) +
        (revenue_summary?.emergencies || 0) +
        (revenue_summary?.admissions || 0);

    // ── Dynamic metric card renderer ──
    const renderMetricCard = (type: string, accent: string) => {
        switch (type) {
            case 'patients':
                return (
                    <AdminMetricCard
                        label={t('page.admin_dashboard.total_patients')}
                        value={totalPatients.toLocaleString()}
                        subtitle={t('page.admin_dashboard.new_today', {
                            count: patient_stats?.new_today ?? 0,
                        })}
                        icon={UserGroupIcon}
                        accent={accent}
                        trend={{ value: '12%', direction: 'up' }}
                    />
                );
            case 'admissions':
                return (
                    <AdminMetricCard
                        label={t('page.admin_dashboard.active_admissions')}
                        value={activeAdmissions.toLocaleString()}
                        subtitle={t('page.admin_dashboard.bed_occupancy', {
                            occupied: bed_occupancy?.occupied ?? 0,
                            total: bed_occupancy?.total ?? 0,
                            percentage: bed_occupancy?.percentage ?? 0,
                        })}
                        icon={BuildingOfficeIcon}
                        accent={accent}
                        trend={{
                            value: `${Math.abs((bed_occupancy?.percentage ?? 0) > 80 ? -3 : 5)}%`,
                            direction:
                                (bed_occupancy?.percentage ?? 0) > 80
                                    ? 'down'
                                    : 'up',
                        }}
                        occupancy={{
                            percentage: bed_occupancy?.percentage ?? 0,
                        }}
                    />
                );
            case 'appointments':
                return (
                    <AdminMetricCard
                        label={t('page.admin_dashboard.todays_appointments')}
                        value={todaysAppointments.toLocaleString()}
                        subtitle={t('page.admin_dashboard.appointment_status', {
                            completed: appointment_stats?.completed ?? 0,
                            scheduled: appointment_stats?.scheduled ?? 0,
                        })}
                        icon={CalendarIcon}
                        accent={accent}
                        trend={{ value: '8%', direction: 'up' }}
                        composition={{
                            completed: appointment_stats?.completed ?? 0,
                            scheduled: appointment_stats?.scheduled ?? 0,
                            cancelled: appointment_stats?.cancelled ?? 0,
                            total: appointment_stats?.total || 1,
                        }}
                    />
                );
            case 'revenue':
                return (
                    <AdminMetricCard
                        label={t('page.admin_dashboard.revenue_today')}
                        value={`${revenueToday.toLocaleString()} FCFA`}
                        subtitle={t(
                            'page.admin_dashboard.outstanding_invoices',
                            { count: outstanding_invoices_count ?? 0 },
                        )}
                        icon={BanknotesIcon}
                        accent={accent}
                        trend={{
                            value: `${Math.abs(revenueTrend)}%`,
                            direction: revenueTrend >= 0 ? 'up' : 'down',
                        }}
                    />
                );
            case 'pharmacy_pending': {
                const ps = metrics.pharmacy_stats;

                return (
                    <AdminMetricCard
                        label={t('Pending Prescriptions')}
                        value={ps ? String(ps.pending_prescriptions) : '0'}
                        subtitle={
                            ps?.low_stock_count
                                ? t('{count} drugs low stock', {
                                      count: ps.low_stock_count,
                                  })
                                : ''
                        }
                        icon={ClockIcon}
                        accent={accent}
                    />
                );
            }
            case 'pharmacy_stock': {
                const ps = metrics.pharmacy_stats;

                return (
                    <AdminMetricCard
                        label={t('Low Stock Alert')}
                        value={ps ? String(ps.low_stock_count) : '0'}
                        subtitle={t('Drugs below minimum level')}
                        icon={ExclamationCircleIcon}
                        accent={accent}
                    />
                );
            }
            case 'pharmacy_dispensed': {
                const ps = metrics.pharmacy_stats;

                return (
                    <AdminMetricCard
                        label={t('Dispensed Today')}
                        value={ps ? String(ps.dispensed_today) : '0'}
                        subtitle={t('Prescriptions fulfilled')}
                        icon={CheckCircleIcon}
                        accent={accent}
                    />
                );
            }
            case 'lab_pending': {
                const ls = metrics.lab_stats;

                return (
                    <AdminMetricCard
                        label={t('Pending Lab Requests')}
                        value={ls ? String(ls.pending_requests) : '0'}
                        subtitle={t('Awaiting processing')}
                        icon={BeakerIcon}
                        accent={accent}
                    />
                );
            }
            case 'lab_completed': {
                const ls = metrics.lab_stats;

                return (
                    <AdminMetricCard
                        label={t('Completed Today')}
                        value={ls ? String(ls.completed_today) : '0'}
                        subtitle={t('Lab results finalized')}
                        icon={CheckCircleIcon}
                        accent={accent}
                    />
                );
            }
            case 'lab_in_progress': {
                const ls = metrics.lab_stats;

                return (
                    <AdminMetricCard
                        label={t('In Progress')}
                        value={ls ? String(ls.in_progress) : '0'}
                        subtitle={t('Tests currently running')}
                        icon={ClockIcon}
                        accent={accent}
                    />
                );
            }
            case 'doctor_consultations': {
                const ds = (metrics as any).doctor_stats;

                return (
                    <AdminMetricCard
                        label={t('My Consultations Today')}
                        value={ds ? String(ds.consultations_today) : '0'}
                        subtitle={t('Patients seen')}
                        icon={UserGroupIcon}
                        accent={accent}
                    />
                );
            }
            case 'doctor_lab_pending': {
                const ds = (metrics as any).doctor_stats;

                return (
                    <AdminMetricCard
                        label={t('Pending Lab Results')}
                        value={ds ? String(ds.my_pending_lab) : '0'}
                        subtitle={t('Awaiting my review')}
                        icon={BeakerIcon}
                        accent={accent}
                    />
                );
            }
            case 'doctor_surgeries': {
                const ds = (metrics as any).doctor_stats;

                return (
                    <AdminMetricCard
                        label={t('Active Surgeries')}
                        value={ds ? String(ds.my_active_surgeries) : '0'}
                        subtitle={
                            ds
                                ? t('{count} appointments today', {
                                      count: ds.my_appointments_today,
                                  })
                                : ''
                        }
                        icon={HeartIcon}
                        accent={accent}
                    />
                );
            }
            case 'reception_appointments': {
                const rs = (metrics as any).reception_stats;

                return (
                    <AdminMetricCard
                        label={t("Today's Appointments")}
                        value={rs ? String(rs.todays_appointments) : '0'}
                        subtitle={
                            rs
                                ? t('{count} checked in today', {
                                      count: rs.todays_check_ins,
                                  })
                                : ''
                        }
                        icon={ClockIcon}
                        accent={accent}
                    />
                );
            }
            case 'reception_new_patients': {
                const rs = (metrics as any).reception_stats;

                return (
                    <AdminMetricCard
                        label={t('New Patients Today')}
                        value={rs ? String(rs.new_patients_today) : '0'}
                        subtitle={t('Registered today')}
                        icon={UserGroupIcon}
                        accent={accent}
                    />
                );
            }
            case 'reception_beds': {
                const rs = (metrics as any).reception_stats;

                return (
                    <AdminMetricCard
                        label={t('Available Beds')}
                        value={
                            rs ? `${rs.available_beds}/${rs.total_beds}` : '0/0'
                        }
                        subtitle={t('Ready for admission')}
                        icon={BuildingOfficeIcon}
                        accent={accent}
                    />
                );
            }
            default:
                return null;
        }
    };

    // ── Role-specific short data tables ──
    const renderPanelTables = () => {
        const panels: React.ReactNode[] = [];

        // Pharmacy panel
        const pharmData = (metrics as any).pharmacy_panel;

        if (pharmData && show('showPharmacyPanel')) {
            panels.push(
                <div
                    key="pharmacy"
                    className="grid grid-cols-1 gap-5 lg:grid-cols-2"
                >
                    <DashboardTable
                        title={t('Low Stock Drugs')}
                        icon={ExclamationCircleIcon}
                        columns={[
                            { label: 'Drug' },
                            { label: 'Stock', numeric: true },
                            { label: 'Min', numeric: true },
                        ]}
                        rows={(pharmData.low_stock || []).map((d: any) => [
                            d.name,
                            <span className="font-semibold text-critical-ink tabular-nums">
                                {d.total_stock}
                            </span>,
                            <span className="tabular-nums">
                                {String(d.min_stock_level)}
                            </span>,
                        ])}
                        emptyTitle={t('All drugs are well stocked')}
                    />
                    <DashboardTable
                        title={t('Pending Prescriptions to Dispense')}
                        icon={ClockIcon}
                        columns={[
                            { label: 'Patient' },
                            { label: 'Date' },
                            { label: 'Items', numeric: true },
                        ]}
                        rows={(pharmData.pending_dispense || []).map(
                            (d: any) => [
                                d.patient_name,
                                d.date,
                                <span className="tabular-nums">
                                    {String(d.items_count)}
                                </span>,
                            ],
                        )}
                        emptyTitle={t('No pending prescriptions')}
                    />
                </div>,
            );
        }

        // Lab panel
        const labData = (metrics as any).lab_panel;

        if (labData && show('showLabPanel')) {
            panels.push(
                <div key="lab" className="grid grid-cols-1 gap-5">
                    <DashboardTable
                        title={t('Pending Lab Requests')}
                        icon={BeakerIcon}
                        columns={[
                            { label: 'Patient' },
                            { label: 'Test' },
                            { label: 'Urgency' },
                            { label: 'Date' },
                        ]}
                        rows={(labData.pending || []).map((d: any) => [
                            d.patient_name,
                            d.test_name,
                            <Badge
                                tone={
                                    d.urgency === 'stat'
                                        ? 'critical'
                                        : d.urgency === 'urgent'
                                          ? 'warning'
                                          : 'accent'
                                }
                                dot
                            >
                                {d.urgency}
                            </Badge>,
                            d.date,
                        ])}
                        emptyTitle={t('No pending lab requests')}
                    />
                </div>,
            );
        }

        // Doctor panel
        const docData = (metrics as any).doctor_panel;

        if (docData && show('showDoctorPanel')) {
            panels.push(
                <div
                    key="doctor"
                    className="grid grid-cols-1 gap-5 lg:grid-cols-2"
                >
                    <DashboardTable
                        title={t("Today's Appointments")}
                        icon={CalendarIcon}
                        columns={[
                            { label: 'Patient' },
                            { label: 'Time' },
                            { label: 'Status' },
                        ]}
                        rows={(docData.today_appointments || []).map(
                            (d: any) => [
                                d.patient_name,
                                d.time,
                                <Badge
                                    tone={
                                        d.status === 'completed'
                                            ? 'positive'
                                            : d.status === 'in_consultation'
                                              ? 'warning'
                                              : d.status === 'scheduled'
                                                ? 'accent'
                                                : 'neutral'
                                    }
                                    dot
                                >
                                    {d.status}
                                </Badge>,
                            ],
                        )}
                        emptyTitle={t('No appointments today')}
                    />
                    <DashboardTable
                        title={t('Recent Consultations')}
                        icon={ClipboardDocumentCheckIcon}
                        columns={[
                            { label: 'Patient' },
                            { label: 'Diagnosis' },
                            { label: 'Date' },
                        ]}
                        rows={(docData.recent_consults || []).map((d: any) => [
                            d.patient_name,
                            <span className="block max-w-[150px] truncate">
                                {d.diagnosis || '—'}
                            </span>,
                            d.date,
                        ])}
                        emptyTitle={t('No recent consultations')}
                    />
                </div>,
            );
        }

        // Reception panel
        const recData = (metrics as any).reception_panel;

        if (recData && show('showReceptionPanel')) {
            panels.push(
                <div
                    key="reception"
                    className="grid grid-cols-1 gap-5 lg:grid-cols-2"
                >
                    <DashboardTable
                        title={t("Today's Appointments")}
                        icon={ClockIcon}
                        columns={[
                            { label: 'Patient' },
                            { label: 'Time' },
                            { label: 'Doctor' },
                        ]}
                        rows={(recData.todays_appointments || []).map(
                            (d: any) => [
                                <span>
                                    <span className="font-medium">
                                        {d.patient_name}
                                    </span>
                                    <span className="ml-2 font-mono text-[11px] text-ink-subtle">
                                        {d.patient_uid}
                                    </span>
                                </span>,
                                d.time,
                                d.doctor_name,
                            ],
                        )}
                        emptyTitle={t('No appointments today')}
                    />
                    <DashboardTable
                        title={t('Recent Registrations')}
                        icon={UserGroupIcon}
                        columns={[
                            { label: 'Patient' },
                            { label: 'UID' },
                            { label: 'Phone' },
                        ]}
                        rows={(recData.recent_registrations || []).map(
                            (d: any) => [
                                `${d.first_name} ${d.last_name}`,
                                <span className="font-mono text-[11.5px]">
                                    {d.patient_uid}
                                </span>,
                                d.phone || '—',
                            ],
                        )}
                        emptyTitle={t('No recent registrations')}
                    />
                </div>,
            );
        }

        // Appointment panel (admin/doctor who also see appointments)
        const apptData = (metrics as any).appointment_panel;

        if (apptData && show('showRevenueChart')) {
            panels.push(
                <div key="appointments" className="grid grid-cols-1 gap-5">
                    <DashboardTable
                        title={t('Upcoming Appointments')}
                        icon={CalendarIcon}
                        columns={[
                            { label: 'Patient' },
                            { label: 'Doctor' },
                            { label: 'Date' },
                            { label: 'Time' },
                            { label: 'Status' },
                        ]}
                        rows={(apptData.upcoming || []).map((d: any) => [
                            d.patient_name,
                            d.doctor_name,
                            d.date,
                            d.time,
                            <Badge
                                tone={
                                    d.status === 'completed'
                                        ? 'positive'
                                        : d.status === 'scheduled'
                                          ? 'accent'
                                          : 'warning'
                                }
                                dot
                            >
                                {d.status}
                            </Badge>,
                        ])}
                        emptyTitle={t('No upcoming appointments')}
                    />
                </div>,
            );
        }

        if (panels.length === 0) {
            return null;
        }

        return <div className="space-y-5">{panels}</div>;
    };

    return (
        <AppLayout
            headerTitle={t('page.admin_dashboard.title')}
            headerSubtitle={t('page.admin_dashboard.subtitle')}
        >
            <Head title={t('page.admin_dashboard.title')} />

            <div
                className={`space-y-5 pb-12 transition-[transform,opacity] duration-500 ease-premium ${mounted ? 'translate-y-0 opacity-100' : 'translate-y-1 opacity-0'}`}
            >
                <PageHeader
                    eyebrow={t('page.admin_dashboard.good_day', { name: '' })}
                    title={t('page.admin_dashboard.title')}
                    description={t('page.admin_dashboard.subtitle')}
                    actions={
                        quickActions.length > 0 ? (
                            <>
                                {quickActions.map((action, i) => {
                                    const IconComponent =
                                        ICON_MAP[action.icon] || UserGroupIcon;

                                    return (
                                        <Button
                                            key={action.href}
                                            href={action.href}
                                            variant={
                                                i === 0
                                                    ? 'primary'
                                                    : 'secondary'
                                            }
                                            size="sm"
                                            icon={IconComponent}
                                        >
                                            {action.label}
                                        </Button>
                                    );
                                })}
                            </>
                        ) : undefined
                    }
                />

                {/* ── Primary Metric Cards (dynamic per role) ── */}
                <StatGrid
                    columns={
                        metricCards.length >= 4
                            ? 4
                            : metricCards.length === 3
                              ? 3
                              : 2
                    }
                >
                    {metricCards.map((card) =>
                        renderMetricCard(card.type, card.accent),
                    )}
                </StatGrid>

                {/* ── Clinical + Emergency Strip (conditional) ── */}
                {(hasChip('surgeries') ||
                    hasChip('lab') ||
                    hasChip('prescriptions') ||
                    hasChip('emergencies') ||
                    hasChip('radiology') ||
                    show('showRevenueLink')) && (
                    <div
                        className={`grid grid-cols-2 gap-3 sm:grid-cols-3 ${show('showRevenueLink') ? 'lg:grid-cols-6' : 'lg:grid-cols-4'}`}
                    >
                        {hasChip('surgeries') && (
                            <ClinicalChip
                                icon={HeartIcon}
                                label={t(
                                    'page.admin_dashboard.active_surgeries',
                                )}
                                value={clinical_stats?.active_surgeries ?? 0}
                                tone="critical"
                            />
                        )}
                        {hasChip('lab') && (
                            <ClinicalChip
                                icon={BeakerIcon}
                                label={t(
                                    'page.admin_dashboard.pending_lab_requests',
                                )}
                                value={
                                    clinical_stats?.pending_lab_requests ?? 0
                                }
                                tone="accent"
                            />
                        )}
                        {hasChip('prescriptions') && (
                            <ClinicalChip
                                icon={ClockIcon}
                                label={t(
                                    'page.admin_dashboard.pending_prescriptions',
                                )}
                                value={
                                    clinical_stats?.pending_prescriptions ?? 0
                                }
                                tone="warning"
                            />
                        )}
                        {hasChip('emergencies') && (
                            <ClinicalChip
                                icon={ExclamationCircleIcon}
                                label={t(
                                    'page.admin_dashboard.active_emergencies',
                                )}
                                value={activeEmergencies}
                                tone="critical"
                                pulse={(emergency_stats?.active_count ?? 0) > 0}
                            />
                        )}
                        {hasChip('radiology') && (
                            <ClinicalChip
                                icon={CameraIcon}
                                label={t('Pending Radiology')}
                                value={
                                    (metrics as any).radiology_stats?.pending ??
                                    0
                                }
                                tone="accent"
                            />
                        )}
                        {show('showRevenueLink') && (
                            <PanelLinkCard
                                href="/finance/revenue"
                                label={t(
                                    'page.admin_dashboard.revenue_analytics',
                                )}
                                value={t(
                                    'page.admin_dashboard.view_full_report',
                                )}
                                icon={ChartBarIcon}
                            />
                        )}
                    </div>
                )}

                {/* ── Role-Specific Panel Tables ── */}
                {renderPanelTables()}

                {/* ── Chart + Sidebar Column (finance roles only) ── */}
                {show('showRevenueChart') && (
                    <div className="grid grid-cols-1 gap-5 xl:grid-cols-3">
                        {/* Main Chart */}
                        <Card className="xl:col-span-2">
                            <CardHeader
                                actions={
                                    <div className="flex items-center gap-2">
                                        <TrendBadge
                                            value={revenueTrend}
                                            label="rev"
                                        />
                                        <TrendBadge
                                            value={expenditureTrend}
                                            label="exp"
                                        />
                                    </div>
                                }
                            >
                                <CardTitle>
                                    {t(
                                        'page.admin_dashboard.revenue_vs_expenditure',
                                    )}
                                </CardTitle>
                                <p className="mt-0.5 text-[12px] text-ink-subtle">
                                    {new Date().getFullYear()} —{' '}
                                    {t(
                                        'page.admin_dashboard.monthly_overview',
                                        'Monthly overview',
                                    )}
                                </p>
                            </CardHeader>
                            <div className="px-4 py-5 sm:px-6">
                                <div className="h-72 w-full">
                                    {mData.length > 0 ? (
                                        <Line
                                            data={chartData}
                                            options={chartOptions}
                                        />
                                    ) : (
                                        <EmptyState
                                            panel={false}
                                            icon={ChartBarIcon}
                                            title={t(
                                                'page.admin_dashboard.no_data_available',
                                            )}
                                        />
                                    )}
                                </div>
                            </div>
                        </Card>

                        {/* Sidebar: Profit/Loss + Outstanding */}
                        <div className="space-y-5">
                            {/* Monthly Profit/Loss */}
                            <Card className="p-5">
                                <h3 className="text-[15px] font-semibold tracking-tight text-ink">
                                    {t(
                                        'page.admin_dashboard.monthly_profit_loss',
                                    )}
                                </h3>
                                <div className="mt-4 space-y-3">
                                    {mData.length > 0 ? (
                                        mData
                                            .slice(-6)
                                            .reverse()
                                            .map((d, i) => {
                                                const monthLabel =
                                                    MONTHS[
                                                        parseInt(d.month) - 1
                                                    ] || d.month;
                                                const isProfit = d.profit >= 0;
                                                const barWidth = Math.min(
                                                    (Math.abs(d.profit) /
                                                        maxProfit) *
                                                        100,
                                                    100,
                                                );

                                                return (
                                                    <div key={d.month}>
                                                        <div className="mb-1 flex items-center justify-between text-[11.5px]">
                                                            <span className="font-medium text-ink-muted">
                                                                {monthLabel}
                                                            </span>
                                                            <span
                                                                className={`font-semibold tabular-nums ${
                                                                    isProfit
                                                                        ? 'text-positive-ink'
                                                                        : 'text-critical-ink'
                                                                }`}
                                                            >
                                                                {isProfit
                                                                    ? '+'
                                                                    : ''}
                                                                {d.profit.toLocaleString()}
                                                            </span>
                                                        </div>
                                                        <div className="h-1.5 w-full overflow-hidden rounded-full bg-surface-inset">
                                                            <div
                                                                className={`h-full origin-left rounded-full transition-transform duration-500 ease-premium ${
                                                                    isProfit
                                                                        ? 'bg-positive'
                                                                        : 'bg-critical'
                                                                }`}
                                                                style={{
                                                                    width: `${barWidth}%`,
                                                                    transform:
                                                                        mounted
                                                                            ? 'scaleX(1)'
                                                                            : 'scaleX(0)',
                                                                    transitionDelay: `${i * 60}ms`,
                                                                }}
                                                            />
                                                        </div>
                                                    </div>
                                                );
                                            })
                                    ) : (
                                        <EmptyState
                                            panel={false}
                                            icon={ClockIcon}
                                            title={t(
                                                'page.admin_dashboard.no_data_yet',
                                            )}
                                        />
                                    )}
                                </div>
                            </Card>

                            {/* Outstanding Invoices */}
                            <Card className="p-5">
                                <div className="flex items-start justify-between gap-3">
                                    <div>
                                        <p className="text-[10.5px] font-semibold tracking-[0.13em] text-ink-subtle uppercase">
                                            {t(
                                                'page.admin_dashboard.outstanding_invoices_title',
                                            )}
                                        </p>
                                        <p className="mt-2 text-[26px] leading-none font-semibold tracking-[-0.02em] text-ink tabular-nums">
                                            {outstandingCount}
                                        </p>
                                    </div>
                                    <Badge
                                        tone={
                                            outstanding_invoices_count > 0
                                                ? 'critical'
                                                : 'positive'
                                        }
                                        dot
                                    >
                                        {outstanding_invoices_count > 0
                                            ? 'Unpaid'
                                            : 'Clear'}
                                    </Badge>
                                </div>
                                <p className="mt-2 text-[11.5px] leading-relaxed text-ink-subtle tabular-nums">
                                    {t('page.admin_dashboard.unpaid_amount', {
                                        amount: outstanding_invoices.toLocaleString(),
                                    })}
                                    {' FCFA'}
                                </p>
                                <Button
                                    href="/finance/invoices"
                                    variant="secondary"
                                    size="sm"
                                    trailingIcon={ArrowUpRightIcon}
                                    className="mt-4"
                                >
                                    {t(
                                        'page.admin_dashboard.view_all_invoices',
                                    )}
                                </Button>
                            </Card>
                        </div>
                    </div>
                )}

                {/* ── Revenue Breakdown + Recent Activity ── */}
                <div
                    className={`grid grid-cols-1 gap-5 ${show('showRevenueBreakdown') ? 'xl:grid-cols-2' : ''}`}
                >
                    {/* Revenue Breakdown */}
                    {show('showRevenueBreakdown') && (
                        <Card className="p-5">
                            <h3 className="text-[15px] font-semibold tracking-tight text-ink">
                                {t('page.admin_dashboard.revenue_breakdown')}
                            </h3>
                            <div className="mt-4 space-y-2.5">
                                <BreakdownRow
                                    label={t(
                                        'page.admin_dashboard.paid_invoices',
                                    )}
                                    value={revenue_summary?.invoices || 0}
                                    count={revenue_summary?.invoice_count || 0}
                                    total={breakdownTotal}
                                    countLabel={t(
                                        'page.admin_dashboard.records_count',
                                        {
                                            count:
                                                revenue_summary?.invoice_count ||
                                                0,
                                        },
                                    )}
                                    tone="accent"
                                />
                                <BreakdownRow
                                    label={t('page.admin_dashboard.surgeries')}
                                    value={revenue_summary?.surgeries || 0}
                                    count={revenue_summary?.surgery_count || 0}
                                    total={breakdownTotal}
                                    countLabel={t(
                                        'page.admin_dashboard.records_count',
                                        {
                                            count:
                                                revenue_summary?.surgery_count ||
                                                0,
                                        },
                                    )}
                                    tone="positive"
                                />
                                <BreakdownRow
                                    label={t(
                                        'page.admin_dashboard.emergencies',
                                    )}
                                    value={revenue_summary?.emergencies || 0}
                                    count={
                                        revenue_summary?.emergency_count || 0
                                    }
                                    total={breakdownTotal}
                                    countLabel={t(
                                        'page.admin_dashboard.records_count',
                                        {
                                            count:
                                                revenue_summary?.emergency_count ||
                                                0,
                                        },
                                    )}
                                    tone="critical"
                                />
                                <BreakdownRow
                                    label={t('page.admin_dashboard.admissions')}
                                    value={revenue_summary?.admissions || 0}
                                    total={breakdownTotal}
                                    tone="warning"
                                />
                            </div>
                            <div className="mt-4 flex items-center justify-between rounded-lg bg-surface-muted px-4 py-3">
                                <span className="text-[12.5px] font-semibold text-ink-muted">
                                    {t(
                                        'page.admin_dashboard.total_revenue',
                                        'Total Revenue',
                                    )}
                                </span>
                                <span className="text-[13px] font-semibold text-ink tabular-nums">
                                    {breakdownTotal.toLocaleString()} FCFA
                                </span>
                            </div>
                        </Card>
                    )}

                    {/* Recent Activity */}
                    {show('showRecentActivity') && (
                        <Card>
                            <CardHeader
                                actions={
                                    <Badge tone="muted">
                                        {recent_activity.length}{' '}
                                        {t(
                                            'page.admin_dashboard.events',
                                            'events',
                                        )}
                                    </Badge>
                                }
                            >
                                <CardTitle>
                                    {t('page.admin_dashboard.recent_activity')}
                                </CardTitle>
                            </CardHeader>
                            <div className="px-5">
                                {recent_activity.length > 0 ? (
                                    <ActivityFeed items={recent_activity} />
                                ) : (
                                    <div className="py-6">
                                        <EmptyState
                                            panel={false}
                                            icon={ClockIcon}
                                            title={t(
                                                'page.admin_dashboard.no_activity',
                                            )}
                                        />
                                    </div>
                                )}
                            </div>
                        </Card>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
