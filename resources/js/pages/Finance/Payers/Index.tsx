import { UserGroupIcon } from '@heroicons/react/20/solid';
import {
    PlusIcon,
    PencilSquareIcon,
    BuildingOffice2Icon,
    PhoneIcon,
    EnvelopeIcon,
    ShieldCheckIcon,
    MagnifyingGlassIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { Payer } from '@/types';

interface Props {
    payers: Payer[];
}

export default function PayersIndex({ payers }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState<'add' | 'edit' | null>(null);
    const [selectedItem, setSelectedItem] = useState<Payer | null>(null);

    const { data, setData, post, put, processing, errors, reset, clearErrors } =
        useForm({
            name: '',
            type: 'insurance' as string,
            contact_person: '',
            phone: '',
            email: '',
            coverage_percentage: '80',
            reimbursement_days: '30',
            is_active: true,
        });

    const openModal = (mode: 'add' | 'edit', payer?: Payer) => {
        setModalMode(mode);
        setSelectedItem(payer || null);
        clearErrors();

        if (mode === 'edit' && payer) {
            setData({
                name: payer.name,
                type: payer.type,
                contact_person: payer.contact_person || '',
                phone: payer.phone || '',
                email: payer.email || '',
                coverage_percentage: String(payer.coverage_percentage),
                reimbursement_days: String(payer.reimbursement_days),
                is_active: payer.is_active,
            });
        } else {
            reset();
        }

        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setModalMode(null);
        setSelectedItem(null);
        reset();
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (modalMode === 'add') {
            post(route('payers.store'), { onSuccess: () => closeModal() });
        } else if (modalMode === 'edit' && selectedItem) {
            put(route('payers.update', selectedItem.id), {
                onSuccess: () => closeModal(),
            });
        }
    };

    const filteredPayers = useMemo(() => {
        if (!searchQuery.trim()) {
            return payers;
        }

        const q = searchQuery.toLowerCase();

        return payers.filter(
            (p) =>
                p.name.toLowerCase().includes(q) ||
                p.type.toLowerCase().includes(q) ||
                (p.contact_person &&
                    p.contact_person.toLowerCase().includes(q)),
        );
    }, [payers, searchQuery]);

    const stats = {
        total: payers.length,
        active: payers.filter((p) => p.is_active).length,
        government: payers.filter((p) => p.type === 'government').length,
        insurance: payers.filter(
            (p) => p.type === 'insurance' || p.type === 'mutual',
        ).length,
    };

    const typeOptions = [
        { value: 'government', label: t('Government — CNPS / CNAMGS') },
        { value: 'insurance', label: t('Insurance Company') },
        { value: 'mutual', label: t('Mutuelle de Santé') },
        { value: 'employer', label: t('Employer / Corporate') },
    ];

    const typeColor = (type: string) => {
        switch (type) {
            case 'government':
                return 'blue';
            case 'insurance':
                return 'purple';
            case 'mutual':
                return 'emerald';
            case 'employer':
                return 'amber';
            default:
                return 'gray';
        }
    };

    return (
        <AppLayout
            headerTitle={t('Insurance Payers')}
            headerSubtitle={t(
                'Manage insurance companies, CNPS, mutuelles, and corporate payers',
            )}
        >
            <Head title={t('Payers')} />
            <PageBreadcrumb
                items={[{ label: t('Finance') }, { label: t('Payers') }]}
            />

            <div className="mt-6 mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
                <StatCard
                    icon={BuildingOffice2Icon}
                    label={t('Total Payers')}
                    value={stats.total}
                    color="blue"
                />
                <StatCard
                    icon={ShieldCheckIcon}
                    label={t('Active')}
                    value={stats.active}
                    color="emerald"
                />
                <StatCard
                    icon={BuildingOffice2Icon}
                    label={t('Government')}
                    value={stats.government}
                    color="purple"
                />
                <StatCard
                    icon={ShieldCheckIcon}
                    label={t('Insurance/Mutual')}
                    value={stats.insurance}
                    color="amber"
                />
            </div>

            <div className="mb-6 flex items-center justify-between">
                <div className="relative w-72">
                    <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink-subtle" />
                    <input
                        type="text"
                        placeholder={t('Search payers...')}
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        className="w-full rounded-lg border border-line bg-surface py-2 pr-4 pl-9 text-sm text-ink outline-none focus:border-accent focus:ring-2 focus:ring-blue-500/20"
                    />
                </div>
                <button
                    onClick={() => openModal('add')}
                    className="inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-accent px-4 py-2 text-sm font-medium text-ink-inverse transition-colors hover:bg-accent-hover"
                >
                    <PlusIcon className="h-4 w-4" />
                    {t('Add Payer')}
                </button>
            </div>

            {filteredPayers.length === 0 ? (
                <EmptyState
                    icon={BuildingOffice2Icon}
                    title={t('No Payers Found')}
                    description={t(
                        'Add insurance companies and payers to get started.',
                    )}
                />
            ) : (
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {filteredPayers.map((payer) => (
                        <div
                            key={payer.id}
                            className="group rounded-xl border border-line bg-surface p-5 transition-shadow hover:shadow-surface"
                        >
                            <div className="mb-3 flex items-start justify-between">
                                <div>
                                    <h3 className="font-semibold text-ink">
                                        {payer.name}
                                    </h3>
                                    <div className="mt-1">
                                        <StatusBadge
                                            label={payer.type}
                                            color={typeColor(payer.type)}
                                        />
                                    </div>
                                </div>
                                <div className="flex gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    <button
                                        onClick={() => openModal('edit', payer)}
                                        className="cursor-pointer rounded-lg p-1.5 text-ink-subtle hover:bg-accent-soft hover:text-accent"
                                    >
                                        <PencilSquareIcon className="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                            <div className="space-y-2 text-sm text-ink-subtle">
                                {payer.contact_person && (
                                    <div className="flex items-center gap-2">
                                        <UserGroupIcon className="h-4 w-4 shrink-0" />
                                        <span>{payer.contact_person}</span>
                                    </div>
                                )}
                                {payer.phone && (
                                    <div className="flex items-center gap-2">
                                        <PhoneIcon className="h-4 w-4 shrink-0" />
                                        <span>{payer.phone}</span>
                                    </div>
                                )}
                                {payer.email && (
                                    <div className="flex items-center gap-2">
                                        <EnvelopeIcon className="h-4 w-4 shrink-0" />
                                        <span className="truncate">
                                            {payer.email}
                                        </span>
                                    </div>
                                )}
                            </div>
                            <div className="mt-3 flex items-center justify-between border-t border-line pt-3 text-xs text-ink-subtle">
                                <span>
                                    {t('Coverage')}: {payer.coverage_percentage}
                                    %
                                </span>
                                <span>
                                    {payer.patient_insurances_count ?? 0}{' '}
                                    {t('insured')}
                                </span>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            <Modal
                isOpen={modalOpen}
                onClose={closeModal}
                title={modalMode === 'add' ? t('Add Payer') : t('Edit Payer')}
            >
                <form onSubmit={handleSubmit} className="space-y-4">
                    <FormInput
                        name="name"
                        label={t('Name')}
                        value={data.name}
                        onChange={(v) => setData('name', v)}
                        errors={errors}
                        required
                    />
                    <FormSelect
                        name="type"
                        label={t('Type')}
                        value={data.type}
                        onChange={(v) => setData('type', v)}
                        options={typeOptions}
                        errors={errors}
                        required
                    />
                    <div className="grid grid-cols-2 gap-4">
                        <FormInput
                            name="contact_person"
                            label={t('Contact Person')}
                            value={data.contact_person}
                            onChange={(v) => setData('contact_person', v)}
                            errors={errors}
                        />
                        <FormInput
                            name="phone"
                            label={t('Phone')}
                            value={data.phone}
                            onChange={(v) => setData('phone', v)}
                            errors={errors}
                        />
                    </div>
                    <FormInput
                        name="email"
                        label={t('Email')}
                        type="email"
                        value={data.email}
                        onChange={(v) => setData('email', v)}
                        errors={errors}
                    />
                    <div className="grid grid-cols-2 gap-4">
                        <FormInput
                            name="coverage_percentage"
                            label={t('Coverage %')}
                            type="number"
                            value={data.coverage_percentage}
                            onChange={(v) => setData('coverage_percentage', v)}
                            errors={errors}
                            min="0"
                            max="100"
                            step="0.01"
                        />
                        <FormInput
                            name="reimbursement_days"
                            label={t('Reimbursement (days)')}
                            type="number"
                            value={data.reimbursement_days}
                            onChange={(v) => setData('reimbursement_days', v)}
                            errors={errors}
                            min="0"
                        />
                    </div>
                    <div className="flex items-center gap-2">
                        <input
                            type="checkbox"
                            id="is_active"
                            checked={data.is_active}
                            onChange={(e) =>
                                setData('is_active', e.target.checked)
                            }
                            className="rounded border-line-strong text-accent-ink"
                        />
                        <label
                            htmlFor="is_active"
                            className="text-sm text-ink-muted"
                        >
                            {t('Active')}
                        </label>
                    </div>
                    <div className="flex justify-end gap-3 border-t border-line pt-4">
                        <button
                            type="button"
                            onClick={closeModal}
                            className="cursor-pointer rounded-lg px-4 py-2 text-sm text-ink-muted hover:bg-surface-hover"
                        >
                            {t('Cancel')}
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="cursor-pointer rounded-lg bg-accent px-4 py-2 text-sm text-ink-inverse hover:bg-accent-hover disabled:opacity-50"
                        >
                            {modalMode === 'add' ? t('Create') : t('Update')}
                        </button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
