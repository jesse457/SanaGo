import {
    PlusIcon,
    MagnifyingGlassIcon,
    TrashIcon,
    PencilIcon,
    TagIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import React, { useState } from 'react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import {
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import AppLayout from '@/Layouts/AppLayout';

interface ServiceItem {
    id: number;
    name: string;
    code: string;
    category: string;
    default_price: number;
    is_active: boolean;
    requires_upfront_payment: boolean;
    description: string | null;
}

interface Props {
    services: {
        data: ServiceItem[];
        links: { url: string | null; label: string; active: boolean }[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
    };
}

const CATEGORY_LABELS: Record<string, string> = {
    consultation: 'Consultation',
    procedure: 'Procedure',
    lab: 'Laboratoire',
    radiology: 'Radiologie',
    imaging: 'Imagerie',
    medication: 'Médicament',
    bed: 'Lit',
    nursing: 'Soins Infirmiers',
    emergency: 'Urgence',
    other: 'Autre',
};

const CATEGORY_COLORS: Record<string, string> = {
    consultation: 'bg-accent-soft text-accent-ink ',
    procedure: 'bg-accent-soft text-accent-ink ',
    lab: 'bg-positive-soft text-positive-ink ',
    radiology: 'bg-warning-soft text-warning-ink ',
    imaging: 'bg-accent-soft text-accent-ink ',
    medication: 'bg-accent-soft text-accent-ink ',
    bed: 'bg-warning-soft text-warning-ink ',
    nursing: 'bg-accent-soft text-accent-ink ',
    emergency: 'bg-critical-soft text-critical-ink ',
    other: 'bg-surface-inset text-ink-muted ',
};

export default function ServicesIndex({ services }: Props) {
    const [search, setSearch] = useState('');
    const [categoryFilter, setCategoryFilter] = useState('all');

    const filteredServices = services.data.filter((s) => {
        const matchesSearch =
            !search ||
            s.name.toLowerCase().includes(search.toLowerCase()) ||
            s.code.toLowerCase().includes(search.toLowerCase());
        const matchesCategory =
            categoryFilter === 'all' || s.category === categoryFilter;

        return matchesSearch && matchesCategory;
    });

    const categories = [
        'all',
        ...Array.from(new Set(services.data.map((s) => s.category))),
    ];

    const paginationLinks = services.links?.map((link) => ({
        url: link.url,
        label: link.label,
        active: link.active,
    }));

    return (
        <AppLayout
            headerTitle="Catalogue des Services"
            headerSubtitle="Gérer les prix et la configuration des services médicaux"
        >
            <Head title="Catalogue des Services" />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: 'Accueil', href: route('dashboard') },
                        { label: 'Finance' },
                        { label: 'Services' },
                    ]}
                />

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="flex overflow-x-auto rounded-lg bg-surface-inset p-1">
                        {categories.map((cat) => (
                            <button
                                key={cat}
                                onClick={() => setCategoryFilter(cat)}
                                className={`rounded-lg px-4 py-2 text-xs font-bold tracking-wide whitespace-nowrap uppercase transition-all ${categoryFilter === cat ? 'bg-surface text-accent-ink shadow-surface' : 'text-ink-subtle hover:text-ink'}`}
                            >
                                {cat === 'all'
                                    ? 'Tous'
                                    : CATEGORY_LABELS[cat] || cat}
                            </button>
                        ))}
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink-subtle" />
                            <input
                                type="text"
                                placeholder="Rechercher un service..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="rounded-lg border border-line bg-surface py-2 pr-4 pl-9 text-ink outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <Link
                            href={route('finance.services.create')}
                            className="flex items-center justify-center gap-2 rounded-lg bg-accent px-4 py-2 text-sm font-semibold whitespace-nowrap text-ink-inverse shadow-surface transition-colors hover:bg-accent-hover"
                        >
                            <PlusIcon className="h-4 w-4" /> Nouveau Service
                        </Link>
                    </div>
                </div>

                <div className="overflow-hidden rounded-xl border border-line bg-surface shadow-surface">
                    <Table bare>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHeaderCell>Service</TableHeaderCell>
                                <TableHeaderCell>Code</TableHeaderCell>
                                <TableHeaderCell>Catégorie</TableHeaderCell>
                                <TableHeaderCell numeric>
                                    Prix (XAF)
                                </TableHeaderCell>
                                <TableHeaderCell>Paiement</TableHeaderCell>
                                <TableHeaderCell>Statut</TableHeaderCell>
                                <TableHeaderCell numeric>
                                    <span className="sr-only">Actions</span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filteredServices.map((svc) => (
                                <TableRow key={svc.id} className="group">
                                    <TableCell>
                                        <p className="text-[13px] font-semibold text-ink">
                                            {svc.name}
                                        </p>
                                        {svc.description && (
                                            <p className="mt-0.5 line-clamp-1 max-w-xs text-xs text-ink-subtle">
                                                {svc.description}
                                            </p>
                                        )}
                                    </TableCell>
                                    <TableCell>
                                        <code className="rounded bg-surface-inset px-2 py-0.5 text-xs font-bold text-ink">
                                            {svc.code}
                                        </code>
                                    </TableCell>
                                    <TableCell>
                                        <span
                                            className={`inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ${CATEGORY_COLORS[svc.category] || CATEGORY_COLORS.other}`}
                                        >
                                            {CATEGORY_LABELS[svc.category] ||
                                                svc.category}
                                        </span>
                                    </TableCell>
                                    <TableCell
                                        numeric
                                        className="text-[13px] font-semibold text-ink"
                                    >
                                        {Number(
                                            svc.default_price,
                                        ).toLocaleString()}{' '}
                                        FCFA
                                    </TableCell>
                                    <TableCell>
                                        {svc.requires_upfront_payment ? (
                                            <span className="inline-flex items-center gap-1 rounded-full bg-warning-soft px-2.5 py-0.5 text-xs font-semibold text-warning-ink">
                                                Avant service
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center gap-1 rounded-full bg-positive-soft px-2.5 py-0.5 text-xs font-semibold text-positive-ink">
                                                Après service
                                            </span>
                                        )}
                                    </TableCell>
                                    <TableCell>
                                        {svc.is_active ? (
                                            <span className="inline-flex rounded-full bg-positive-soft px-2.5 py-0.5 text-xs font-semibold text-positive-ink">
                                                Actif
                                            </span>
                                        ) : (
                                            <span className="inline-flex rounded-full bg-critical-soft px-2.5 py-0.5 text-xs font-semibold text-critical-ink">
                                                Inactif
                                            </span>
                                        )}
                                    </TableCell>
                                    <TableCell numeric>
                                        <div className="flex justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100 focus-within:opacity-100">
                                            <Link
                                                href={route(
                                                    'finance.services.edit',
                                                    svc.id,
                                                )}
                                                className="rounded-lg p-2 text-ink-subtle transition-colors hover:bg-warning-soft hover:text-warning-ink"
                                            >
                                                <PencilIcon className="h-4 w-4" />
                                            </Link>
                                            <button
                                                onClick={() => {
                                                    if (
                                                        confirm(
                                                            'Supprimer ce service ?',
                                                        )
                                                    ) {
                                                        router.delete(
                                                            route(
                                                                'finance.services.destroy',
                                                                svc.id,
                                                            ),
                                                            {
                                                                preserveScroll: true,
                                                            },
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
                    {filteredServices.length === 0 && (
                        <div className="p-6">
                            <EmptyState
                                icon={TagIcon}
                                title="Aucun service trouvé"
                                action={{
                                    label: 'Créer le premier service',
                                    href: route('finance.services.create'),
                                }}
                            />
                        </div>
                    )}
                </div>

                <Pagination
                    links={paginationLinks}
                    meta={{
                        from: services.from,
                        to: services.to,
                        total: services.total,
                    }}
                />
            </div>
        </AppLayout>
    );
}
