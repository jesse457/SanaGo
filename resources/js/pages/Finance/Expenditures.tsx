import {
    PlusIcon,
    TrashIcon,
    PencilSquareIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import {
    Button,
    Dialog,
    Field,
    FieldSet,
    Input,
    Select,
    swal,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Expenditure {
    id: number;
    category: string;
    description: string;
    amount: number;
    expenditure_date: string;
    vendor?: string;
    recorded_by?: { id: number; name: string };
}

interface Props {
    expenditures: {
        data: Expenditure[];
        links: any[];
        current_page: number;
        last_page: number;
    };
}

const CATEGORIES = [
    'salaries',
    'supplies',
    'utilities',
    'maintenance',
    'equipment',
    'pharmaceuticals',
    'administrative',
    'other',
];
const CATEGORY_OPTIONS = CATEGORIES.map((c) => ({
    value: c,
    label: c.charAt(0).toUpperCase() + c.slice(1),
}));

export default function ExpendituresIndex({ expenditures }: Props) {
    const { t } = useTranslation();
    const [search] = useState('');
    const [filter, setFilter] = useState('all');
    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState<Expenditure | null>(null);

    const { data, setData, post, put, processing, reset, errors, clearErrors } =
        useForm({
            category: 'supplies',
            description: '',
            amount: '',
            expenditure_date: new Date().toISOString().slice(0, 10),
            vendor: '',
            reference_number: '',
            notes: '',
        });

    const filtered = useMemo(() => {
        let r = expenditures.data;

        if (filter !== 'all') {
            r = r.filter((e) => e.category === filter);
        }

        if (search) {
            r = r.filter((e) =>
                e.description.toLowerCase().includes(search.toLowerCase()),
            );
        }

        return r;
    }, [expenditures.data, filter, search]);

    const openCreate = () => {
        setEditing(null);
        reset();
        setModalOpen(true);
    };

    const openEdit = (e: Expenditure) => {
        setEditing(e);
        setData({
            category: e.category,
            description: e.description,
            amount: String(e.amount),
            expenditure_date: e.expenditure_date,
            vendor: e.vendor || '',
            reference_number: '',
            notes: '',
        });
        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setEditing(null);
        clearErrors();
    };

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();

        const options = { onSuccess: () => closeModal(), preserveScroll: true };

        if (editing) {
            put(route('expenditures.update', editing.id), options);
        } else {
            post(route('expenditures.store'), options);
        }
    };

    const handleDelete = async (expenditure: Expenditure) => {
        const confirmed = await swal.confirm({
            tone: 'warning',
            danger: true,
            title: t(
                'finance.expenditures.delete_expenditure',
                'Delete this expenditure?',
            ),
            message: t('finance.expenditures.delete_confirm'),
            confirmLabel: t('common.delete', 'Delete'),
            cancelLabel: t('common.cancel', 'Cancel'),
        });

        if (!confirmed) {
            return;
        }

        router.delete(route('expenditures.destroy', expenditure.id), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout
            headerTitle={t('finance.expenditures.title')}
            headerSubtitle={t('finance.expenditures.subtitle')}
        >
            <Head title={t('finance.expenditures.head_title')} />
            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[{ label: t('finance.expenditures.breadcrumb') }]}
                />

                <div className="flex items-center justify-between gap-4">
                    <div className="flex flex-wrap rounded-lg bg-surface-inset p-1">
                        {['all', ...CATEGORIES].map((c) => (
                            <button
                                key={c}
                                onClick={() => setFilter(c)}
                                className={`rounded-lg px-3 py-1.5 text-[10px] font-bold uppercase transition-all ${filter === c ? 'bg-surface text-accent-ink shadow-surface' : 'text-ink-subtle hover:text-ink'}`}
                            >
                                {t(`finance.expenditures.filter_${c}`)}
                            </button>
                        ))}
                    </div>
                    <button
                        onClick={openCreate}
                        className="flex items-center gap-2 rounded-lg bg-accent px-4 py-2 text-sm font-bold text-ink-inverse shadow-surface hover:bg-accent-hover"
                    >
                        <PlusIcon className="h-4 w-4" />{' '}
                        {t('finance.expenditures.add_expense')}
                    </button>
                </div>

                <div className="overflow-hidden rounded-xl border border-line bg-surface shadow-surface">
                    <Table bare>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHeaderCell>
                                    {t('finance.expenditures.description')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('finance.expenditures.category')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('finance.expenditures.amount')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('finance.expenditures.date')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    <span className="sr-only">Actions</span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filtered.map((e) => (
                                <TableRow key={e.id}>
                                    <TableCell>
                                        <p className="text-[13px] font-semibold text-ink">
                                            {e.description}
                                        </p>
                                        {e.vendor && (
                                            <p className="text-xs text-ink-subtle">
                                                {e.vendor}
                                            </p>
                                        )}
                                    </TableCell>
                                    <TableCell>
                                        <StatusBadge
                                            label={e.category}
                                            color="gray"
                                        />
                                    </TableCell>
                                    <TableCell
                                        numeric
                                        className="font-semibold text-critical-ink"
                                    >
                                        {e.amount.toLocaleString()} FCFA
                                    </TableCell>
                                    <TableCell muted className="tabular-nums">
                                        {e.expenditure_date}
                                    </TableCell>
                                    <TableCell numeric>
                                        <div className="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                onClick={() => openEdit(e)}
                                                aria-label={t('common.edit')}
                                                className="rounded-lg p-2 text-ink-subtle transition-colors hover:bg-accent-soft hover:text-accent"
                                            >
                                                <PencilSquareIcon className="h-4 w-4" />
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => handleDelete(e)}
                                                aria-label={t('common.delete')}
                                                className="rounded-lg p-2 text-ink-subtle transition-colors hover:bg-critical-soft hover:text-critical-ink focus-visible:ring-2 focus-visible:ring-critical/40 focus-visible:outline-none"
                                            >
                                                <TrashIcon className="h-4 w-4" />
                                            </button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>
            </div>

            <Dialog
                open={modalOpen}
                onClose={closeModal}
                size="lg"
                title={
                    editing
                        ? t('finance.expenditures.edit_expenditure')
                        : t('finance.expenditures.add_expenditure')
                }
                description={t('finance.expenditures.subtitle')}
                footer={
                    <>
                        <Button
                            type="button"
                            variant="secondary"
                            onClick={closeModal}
                            disabled={processing}
                        >
                            {t('common.cancel')}
                        </Button>
                        <Button
                            type="submit"
                            form="expenditure-form"
                            loading={processing}
                        >
                            {editing
                                ? t('common.update')
                                : t('finance.expenditures.record_expense')}
                        </Button>
                    </>
                }
            >
                <form
                    id="expenditure-form"
                    onSubmit={handleSubmit}
                    className="space-y-4"
                >
                    <FieldSet columns={2}>
                        <Field
                            label={t('finance.expenditures.description')}
                            htmlFor="expenditure-description"
                            required
                            error={errors.description}
                            className="sm:col-span-2"
                        >
                            <Input
                                id="expenditure-description"
                                value={data.description}
                                onChange={(event) =>
                                    setData('description', event.target.value)
                                }
                                invalid={Boolean(errors.description)}
                                autoFocus
                            />
                        </Field>

                        <Field
                            label={t('finance.expenditures.category')}
                            htmlFor="expenditure-category"
                            error={errors.category}
                        >
                            <Select
                                id="expenditure-category"
                                value={data.category}
                                onChange={(event) =>
                                    setData('category', event.target.value)
                                }
                                invalid={Boolean(errors.category)}
                            >
                                {CATEGORY_OPTIONS.map((option) => (
                                    <option
                                        key={option.value}
                                        value={option.value}
                                    >
                                        {option.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>

                        <Field
                            label={t('finance.expenditures.amount_fcfa')}
                            htmlFor="expenditure-amount"
                            required
                            error={errors.amount}
                        >
                            <Input
                                id="expenditure-amount"
                                type="number"
                                step="0.01"
                                value={data.amount}
                                onChange={(event) =>
                                    setData('amount', event.target.value)
                                }
                                invalid={Boolean(errors.amount)}
                            />
                        </Field>

                        <Field
                            label={t('finance.expenditures.date')}
                            htmlFor="expenditure-date"
                            required
                            error={errors.expenditure_date}
                        >
                            <Input
                                id="expenditure-date"
                                type="date"
                                value={data.expenditure_date}
                                onChange={(event) =>
                                    setData(
                                        'expenditure_date',
                                        event.target.value,
                                    )
                                }
                                invalid={Boolean(errors.expenditure_date)}
                            />
                        </Field>

                        <Field
                            label={t('finance.expenditures.vendor')}
                            htmlFor="expenditure-vendor"
                            error={errors.vendor}
                        >
                            <Input
                                id="expenditure-vendor"
                                value={data.vendor}
                                onChange={(event) =>
                                    setData('vendor', event.target.value)
                                }
                                invalid={Boolean(errors.vendor)}
                            />
                        </Field>

                        <Field
                            label={t('finance.expenditures.notes', 'Notes')}
                            htmlFor="expenditure-notes"
                            error={errors.notes}
                            className="sm:col-span-2"
                        >
                            <Textarea
                                id="expenditure-notes"
                                value={data.notes}
                                onChange={(event) =>
                                    setData('notes', event.target.value)
                                }
                                invalid={Boolean(errors.notes)}
                            />
                        </Field>
                    </FieldSet>
                </form>
            </Dialog>
        </AppLayout>
    );
}
