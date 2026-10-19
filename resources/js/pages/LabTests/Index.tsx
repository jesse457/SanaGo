import {
    BeakerIcon,
    CheckBadgeIcon,
    CurrencyDollarIcon,
    PencilSquareIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import { Head, router, useForm } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import { useMemo, useState, useEffect } from 'react';
import type { FormEvent } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Alert,
    Badge,
    Button,
    Card,
    Dialog,
    EmptyState,
    Field,
    Input,
    PageHeader,
    SegmentedControl,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Textarea,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface LabTest {
    id: number;
    code: string;
    test_name: string;
    price: number | string;
    units: string;
    is_active: boolean;
    description: string;
}

interface Props {
    tests: {
        data: LabTest[];
        links: { url: string | null; label: string; active: boolean }[];
        total: number;
        from: number;
        to: number;
    };
    filters: { search?: string };
}

export default function LabTestManagement({ tests, filters }: Props) {
    const { t } = useTranslation();
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('grid');
    const [searchQuery, setSearchQuery] = useState(filters.search || '');
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [selectedId, setSelectedId] = useState<number | null>(null);

    const { data, setData, put, processing, errors, reset } = useForm({
        test_name: '',
        code: '',
        price: 0,
        units: '',
        description: '',
        is_active: true,
    });

    const requestSearch = useMemo(
        () =>
            debounce((query: string) => {
                router.get(
                    route('tests.index'),
                    { search: query },
                    { preserveState: true, replace: true },
                );
            }, 300),
        [],
    );

    const handleSearchChange = (value: string) => {
        setSearchQuery(value);
        requestSearch(value);
    };

    useEffect(() => {
        // Mirror a server-driven filter change (pagination / back-forward).
        // eslint-disable-next-line react-hooks/set-state-in-effect
        setSearchQuery(filters.search || '');
    }, [filters.search]);

    const [deleteModalOpen, setDeleteModalOpen] = useState(false);

    const openEditModal = (test: LabTest) => {
        setSelectedId(test.id);
        setData({
            test_name: test.test_name,
            code: test.code,
            price: Number(test.price),
            units: test.units,
            description: test.description || '',
            is_active: !!test.is_active,
        });
        setIsEditModalOpen(true);
    };

    const openDeleteModal = (id: number) => {
        setSelectedId(id);
        setDeleteModalOpen(true);
    };

    const handleUpdate = (event: FormEvent) => {
        event.preventDefault();
        put(route('tests.update', selectedId!), {
            onSuccess: () => {
                setIsEditModalOpen(false);
                reset();
            },
            preserveScroll: true,
        });
    };

    const handleDelete = () => {
        router.delete(route('tests.destroy', selectedId?.toString()), {
            onSuccess: () => setDeleteModalOpen(false),
            preserveScroll: true,
        });
    };

    const stats = useMemo(
        () => ({
            total: tests.total,
            active: tests.data.filter((test) => test.is_active).length,
            avgPrice:
                tests.data.reduce(
                    (accumulator, test) => accumulator + Number(test.price),
                    0,
                ) / (tests.data.length || 1),
        }),
        [tests],
    );

    const statusLabel = (test: LabTest) =>
        test.is_active
            ? t('lab_tests.index.active_status')
            : t('lab_tests.index.inactive_status');

    return (
        <AppLayout
            headerTitle={t('lab_tests.index.title')}
            headerSubtitle={t('lab_tests.index.subtitle')}
        >
            <Head title={t('lab_tests.index.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    eyebrow={t('lab_tests.index.test_definitions')}
                    title={t('lab_tests.index.title')}
                    description={t('lab_tests.index.subtitle')}
                    breadcrumbs={[
                        { label: t('common.home'), href: route('dashboard') },
                        { label: t('lab_tests.index.test_definitions') },
                    ]}
                    actions={
                        <Button
                            href={route('tests.create')}
                            icon={PlusIcon}
                            variant="accent"
                        >
                            {t('lab_tests.index.new_test')}
                        </Button>
                    }
                />

                <StatGrid columns={3}>
                    <StatCard
                        label={t('lab_tests.index.total_tests')}
                        value={stats.total}
                        icon={BeakerIcon}
                        tone="accent"
                    />
                    <StatCard
                        label={t('lab_tests.index.active')}
                        value={stats.active}
                        icon={CheckBadgeIcon}
                        tone="positive"
                    />
                    <StatCard
                        label={t('lab_tests.index.avg_price')}
                        value={`FCFA ${stats.avgPrice.toFixed(2)}`}
                        icon={CurrencyDollarIcon}
                        tone="warning"
                    />
                </StatGrid>

                <Toolbar>
                    <ToolbarSearch
                        value={searchQuery}
                        onChange={handleSearchChange}
                        placeholder={t('lab_tests.index.search_placeholder')}
                    />
                    <ToolbarSpacer />
                    <SegmentedControl
                        value={viewMode}
                        onChange={setViewMode}
                        options={[
                            {
                                value: 'grid',
                                label: t('common.grid', 'Grid'),
                                ariaLabel: 'Grid view',
                            },
                            {
                                value: 'table',
                                label: t('common.table', 'Table'),
                                ariaLabel: 'Table view',
                            },
                        ]}
                        size="sm"
                    />
                </Toolbar>

                {tests.data.length === 0 ? (
                    <EmptyState
                        icon={BeakerIcon}
                        title={t('lab_tests.index.no_tests')}
                        description={t('lab_tests.index.no_tests_description')}
                        action={{
                            label: t('lab_tests.index.create_first_test'),
                            href: route('tests.create'),
                        }}
                    />
                ) : viewMode === 'grid' ? (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {tests.data.map((test) => (
                            <Card
                                key={test.id}
                                className="group flex h-full flex-col p-5 transition-[border-color,transform] duration-200 ease-premium hover:-translate-y-0.5 hover:border-line-strong"
                            >
                                <div className="mb-4 flex items-start justify-between gap-3">
                                    <span
                                        className={
                                            test.is_active
                                                ? 'flex h-10 w-10 items-center justify-center rounded-lg bg-accent-soft text-accent-ink'
                                                : 'flex h-10 w-10 items-center justify-center rounded-lg bg-surface-inset text-ink-subtle'
                                        }
                                    >
                                        <BeakerIcon className="h-5 w-5" />
                                    </span>
                                    <Badge
                                        tone={
                                            test.is_active
                                                ? 'positive'
                                                : 'muted'
                                        }
                                        dot
                                    >
                                        {statusLabel(test)}
                                    </Badge>
                                </div>

                                <h3 className="truncate text-[14px] font-semibold tracking-tight text-ink">
                                    {test.test_name}
                                </h3>
                                <p className="mt-0.5 font-mono text-[10.5px] tracking-[0.08em] text-accent-ink uppercase">
                                    {test.code}
                                </p>
                                <p className="mt-2 line-clamp-2 h-9 text-[12.5px] leading-relaxed text-ink-muted">
                                    {test.description ||
                                        t('lab_tests.index.no_description')}
                                </p>

                                <div className="mt-4 flex items-center justify-between gap-2 border-t border-line pt-3.5">
                                    <span className="text-[14px] font-semibold text-ink tabular-nums">
                                        FCFA {Number(test.price).toFixed(2)}
                                    </span>
                                    <div className="flex items-center gap-1">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            icon={PencilSquareIcon}
                                            aria-label={t(
                                                'lab_tests.index.edit_test',
                                            )}
                                            onClick={() => openEditModal(test)}
                                        />
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            icon={TrashIcon}
                                            className="hover:text-critical-ink"
                                            aria-label={t(
                                                'lab_tests.index.delete_title',
                                            )}
                                            onClick={() =>
                                                openDeleteModal(test.id)
                                            }
                                        />
                                    </div>
                                </div>
                            </Card>
                        ))}
                    </div>
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHeaderCell>
                                    {t('lab_tests.index.test_name')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('lab_tests.index.price')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('lab_tests.index.status')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    <span className="sr-only">
                                        {t('common.actions', 'Actions')}
                                    </span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {tests.data.map((test) => (
                                <TableRow key={test.id}>
                                    <TableCell>
                                        <div className="text-[13px] font-semibold text-ink">
                                            {test.test_name}
                                        </div>
                                        <div className="font-mono text-[10.5px] text-ink-subtle">
                                            {test.code}
                                        </div>
                                    </TableCell>
                                    <TableCell numeric>
                                        FCFA {Number(test.price).toFixed(2)}
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            tone={
                                                test.is_active
                                                    ? 'positive'
                                                    : 'muted'
                                            }
                                            dot
                                        >
                                            {statusLabel(test)}
                                        </Badge>
                                    </TableCell>
                                    <TableCell numeric>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                icon={PencilSquareIcon}
                                                aria-label={t(
                                                    'lab_tests.index.edit_test',
                                                )}
                                                onClick={() =>
                                                    openEditModal(test)
                                                }
                                            />
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                icon={TrashIcon}
                                                className="hover:text-critical-ink"
                                                aria-label={t(
                                                    'lab_tests.index.delete_title',
                                                )}
                                                onClick={() =>
                                                    openDeleteModal(test.id)
                                                }
                                            />
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}

                <Pagination
                    links={tests.links}
                    meta={{
                        from: tests.from || 0,
                        to: tests.to || 0,
                        total: tests.total,
                    }}
                />
            </div>

            <Dialog
                open={isEditModalOpen}
                onClose={() => setIsEditModalOpen(false)}
                title={t('lab_tests.index.edit_test')}
                size="xl"
                footer={
                    <>
                        <Button
                            type="button"
                            variant="secondary"
                            onClick={() => setIsEditModalOpen(false)}
                        >
                            {t('common.cancel')}
                        </Button>
                        <Button
                            type="submit"
                            form="lab-test-edit-form"
                            loading={processing}
                        >
                            {t('common.save_changes')}
                        </Button>
                    </>
                }
            >
                <form
                    id="lab-test-edit-form"
                    onSubmit={handleUpdate}
                    className="space-y-4"
                >
                    <Field
                        label={t('lab_tests.index.test_name')}
                        htmlFor="edit-test-name"
                        required
                        error={errors.test_name}
                    >
                        <Input
                            id="edit-test-name"
                            value={data.test_name}
                            invalid={Boolean(errors.test_name)}
                            onChange={(event) =>
                                setData('test_name', event.target.value)
                            }
                        />
                    </Field>

                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <Field
                            label={t('lab_tests.index.code')}
                            htmlFor="edit-test-code"
                            error={errors.code}
                        >
                            <Input
                                id="edit-test-code"
                                value={data.code}
                                invalid={Boolean(errors.code)}
                                onChange={(event) =>
                                    setData('code', event.target.value)
                                }
                            />
                        </Field>
                        <Field
                            label={t('lab_tests.index.price')}
                            htmlFor="edit-test-price"
                            error={errors.price}
                        >
                            <Input
                                id="edit-test-price"
                                type="number"
                                step="0.01"
                                value={data.price}
                                invalid={Boolean(errors.price)}
                                onChange={(event) =>
                                    setData('price', Number(event.target.value))
                                }
                            />
                        </Field>
                    </div>

                    <Field
                        label={t('lab_tests.index.description')}
                        htmlFor="edit-test-description"
                        error={errors.description}
                    >
                        <Textarea
                            id="edit-test-description"
                            rows={3}
                            value={data.description}
                            invalid={Boolean(errors.description)}
                            onChange={(event) =>
                                setData('description', event.target.value)
                            }
                        />
                    </Field>
                </form>
            </Dialog>

            <Dialog
                open={deleteModalOpen}
                onClose={() => setDeleteModalOpen(false)}
                title={t('lab_tests.index.delete_title')}
                description={t('lab_tests.index.delete_confirm')}
                size="md"
                footer={
                    <>
                        <Button
                            type="button"
                            variant="secondary"
                            fullWidth
                            onClick={() => setDeleteModalOpen(false)}
                        >
                            {t('cancel')}
                        </Button>
                        <Button
                            type="button"
                            variant="danger"
                            fullWidth
                            onClick={handleDelete}
                        >
                            {t('deleteMedication')}
                        </Button>
                    </>
                }
            >
                <Alert tone="warning" title={t('lab_tests.index.delete_title')}>
                    {data?.test_name ? <b>{data.test_name}</b> : null}
                </Alert>
            </Dialog>
        </AppLayout>
    );
}
