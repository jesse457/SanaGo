import {
    PlusIcon,
    TrashIcon,
    PencilSquareIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, router } from '@inertiajs/react';
import React, { useState, useMemo } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import {
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
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

    const { data, setData, post, put, processing, reset } = useForm({
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

    const handleSubmit = () => {
        if (editing) {
            put(route('expenditures.update', editing.id), {
                onSuccess: () => setModalOpen(false),
            });
        } else {
            post(route('expenditures.store'), {
                onSuccess: () => setModalOpen(false),
            });
        }
    };

    return (
        <AppLayout
            headerTitle={t('finance.expenditures.title')}
            headerSubtitle={t('finance.expenditures.subtitle')}
        >
            <Head title={t('finance.expenditures.head_title')} />
            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: route('dashboard') },
                        { label: t('finance.expenditures.breadcrumb') },
                    ]}
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
                                                onClick={() => openEdit(e)}
                                                className="rounded-lg p-2 text-ink-subtle transition-colors hover:bg-accent-soft hover:text-accent"
                                            >
                                                <PencilSquareIcon className="h-4 w-4" />
                                            </button>
                                            <button
                                                onClick={() => {
                                                    if (
                                                        confirm(
                                                            t(
                                                                'finance.expenditures.delete_confirm',
                                                            ),
                                                        )
                                                    ) {
                                                        router.delete(
                                                            route(
                                                                'expenditures.destroy',
                                                                e.id,
                                                            ),
                                                        );
                                                    }
                                                }}
                                                className="rounded-lg p-2 text-ink-subtle transition-colors hover:bg-critical-soft hover:text-red-600"
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

            <Modal
                isOpen={modalOpen}
                onClose={() => setModalOpen(false)}
                title={
                    editing
                        ? t('finance.expenditures.edit_expenditure')
                        : t('finance.expenditures.add_expenditure')
                }
            >
                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        handleSubmit();
                    }}
                    className="space-y-4"
                >
                    <FormSelect
                        name="category"
                        label={t('finance.expenditures.category')}
                        value={data.category}
                        onChange={(v) => setData('category', v)}
                        options={CATEGORY_OPTIONS}
                    />
                    <FormInput
                        name="description"
                        label={t('finance.expenditures.description')}
                        value={data.description}
                        onChange={(v) => setData('description', v)}
                        required
                    />
                    <div className="grid grid-cols-2 gap-4">
                        <FormInput
                            name="amount"
                            label={t('finance.expenditures.amount_fcfa')}
                            type="number"
                            step="0.01"
                            value={data.amount}
                            onChange={(v) => setData('amount', v)}
                            required
                        />
                        <FormInput
                            name="expenditure_date"
                            label={t('finance.expenditures.date')}
                            type="date"
                            value={data.expenditure_date}
                            onChange={(v) => setData('expenditure_date', v)}
                            required
                        />
                    </div>
                    <FormInput
                        name="vendor"
                        label={t('finance.expenditures.vendor')}
                        value={data.vendor}
                        onChange={(v) => setData('vendor', v)}
                    />
                    <div className="flex gap-4 pt-2">
                        <button
                            type="button"
                            onClick={() => setModalOpen(false)}
                            className="flex-1 rounded-xl border border-line bg-surface py-3 font-bold text-ink-subtle transition-colors hover:bg-surface-hover"
                        >
                            {t('common.cancel')}
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="flex-[2] rounded-xl bg-accent py-3 font-bold text-ink-inverse transition-colors hover:bg-accent-hover disabled:opacity-50"
                        >
                            {processing
                                ? t('common.saving')
                                : editing
                                  ? t('common.update')
                                  : t('finance.expenditures.record_expense')}
                        </button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
