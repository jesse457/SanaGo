import {
    BeakerIcon,
    ExclamationTriangleIcon,
    ShoppingCartIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import React from 'react';
import { route } from 'ziggy-js';
import {
    Alert,
    Badge,
    Button,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    PageHeader,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { QuickActionLink } from '@/pages/Dashboard/components/QuickActionLink';

const Dashboard = () => {
    const { t } = useTranslation();

    const stats = [
        {
            label: t('page.pharmacy_dashboard.drugs_in_stock'),
            count: 156,
            icon: BeakerIcon,
            tone: 'positive' as const,
        },
        {
            label: t('page.pharmacy_dashboard.low_stock_alerts'),
            count: 3,
            icon: ExclamationTriangleIcon,
            tone: 'critical' as const,
        },
        {
            label: t('page.pharmacy_dashboard.pending_dispensations'),
            count: 7,
            icon: ShoppingCartIcon,
            tone: 'warning' as const,
        },
    ];

    const lowStockItems = [
        { name: 'Amoxicillin 500mg', stock: 12, min: 50, unit: 'tablets' },
        { name: 'Paracetamol IV', stock: 8, min: 30, unit: 'vials' },
        { name: 'Insulin Regular', stock: 3, min: 20, unit: 'vials' },
    ];

    return (
        <AppLayout
            headerTitle={t('page.pharmacy_dashboard.title')}
            headerSubtitle={t('page.pharmacy_dashboard.subtitle')}
        >
            <Head title={t('page.pharmacy_dashboard.title')} />

            <div className="space-y-6">
                <PageHeader
                    title={t('page.pharmacy_dashboard.title')}
                    description={t('page.pharmacy_dashboard.subtitle')}
                    actions={
                        <>
                            <Button
                                variant="secondary"
                                size="sm"
                                href={route('drugs.index')}
                            >
                                {t('page.pharmacy_dashboard.manage_drugs')}
                            </Button>
                            <Button
                                size="sm"
                                href={route('dispensation.index')}
                            >
                                {t(
                                    'page.pharmacy_dashboard.view_dispensations',
                                )}
                            </Button>
                        </>
                    }
                />

                <StatGrid columns={3}>
                    {stats.map((stat) => (
                        <StatCard
                            key={stat.label}
                            label={stat.label}
                            value={stat.count.toLocaleString()}
                            icon={stat.icon}
                            tone={stat.tone}
                        />
                    ))}
                </StatGrid>

                <Alert
                    tone="warning"
                    title={t('page.pharmacy_dashboard.low_stock_alerts')}
                >
                    {lowStockItems.length} drug lines are below their minimum
                    stock level and need replenishment.
                </Alert>

                <Card>
                    <CardHeader
                        actions={
                            <Button
                                variant="ghost"
                                size="sm"
                                href={route('drugs.index')}
                            >
                                {t('page.pharmacy_dashboard.manage_drugs')}
                            </Button>
                        }
                    >
                        <CardTitle>
                            {t(
                                'page.pharmacy_dashboard.low_stock_alerts_title',
                            )}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="px-0 py-0 sm:px-0">
                        <Table bare>
                            <TableHeader>
                                <TableRow>
                                    <TableHeaderCell>
                                        {t('page.pharmacy_dashboard.drug')}
                                    </TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        {t('page.pharmacy_dashboard.current')}
                                    </TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        {t('page.pharmacy_dashboard.min')}
                                    </TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        {t('page.pharmacy_dashboard.unit')}
                                    </TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {lowStockItems.map((item) => (
                                    <TableRow key={item.name}>
                                        <TableCell>{item.name}</TableCell>
                                        <TableCell numeric>
                                            <Badge tone="critical" dot>
                                                {item.stock}
                                            </Badge>
                                        </TableCell>
                                        <TableCell numeric muted>
                                            {item.min}
                                        </TableCell>
                                        <TableCell numeric muted>
                                            {item.unit}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>
                            {t('page.pharmacy_dashboard.quick_actions')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="flex flex-wrap gap-2">
                        <QuickActionLink
                            href={route('drugs.index')}
                            label={t('page.pharmacy_dashboard.manage_drugs')}
                            icon={BeakerIcon}
                        />
                        <QuickActionLink
                            href={route('dispensation.index')}
                            label={t(
                                'page.pharmacy_dashboard.view_dispensations',
                            )}
                            icon={ShoppingCartIcon}
                        />
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
};

export default Dashboard;
