import {
    PlusIcon,
    MagnifyingGlassIcon,
    TrashIcon,
    PencilIcon,
    BeakerIcon,
    XRayIcon,
    HeartIcon,
    BuildingOfficeIcon,
    TagIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import React, { useState } from 'react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
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
    consultation: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    procedure: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
    lab: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    radiology: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
    imaging: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400',
    medication: 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400',
    bed: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    nursing: 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400',
    emergency: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    other: 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
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
                    <div className="flex overflow-x-auto rounded-lg bg-gray-100 p-1 dark:bg-neutral-800">
                        {categories.map((cat) => (
                            <button
                                key={cat}
                                onClick={() => setCategoryFilter(cat)}
                                className={`rounded-lg px-4 py-2 text-xs font-bold tracking-wide whitespace-nowrap uppercase transition-all ${categoryFilter === cat ? 'bg-white text-blue-600 shadow-sm dark:bg-neutral-700' : 'text-gray-500 hover:text-gray-900 dark:text-neutral-400 dark:hover:text-neutral-100'}`}
                            >
                                {cat === 'all'
                                    ? 'Tous'
                                    : CATEGORY_LABELS[cat] || cat}
                            </button>
                        ))}
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                placeholder="Rechercher un service..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>
                        <Link
                            href={route('finance.services.create')}
                            className="flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold whitespace-nowrap text-white shadow-sm transition-colors hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" /> Nouveau Service
                        </Link>
                    </div>
                </div>

                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <table className="w-full text-left">
                        <thead className="border-b border-gray-200 bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800">
                            <tr>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    Service
                                </th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    Code
                                </th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    Catégorie
                                </th>
                                <th className="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    Prix (XAF)
                                </th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    Paiement
                                </th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    Statut
                                </th>
                                <th className="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            {filteredServices.map((svc) => (
                                <tr
                                    key={svc.id}
                                    className="group transition-colors hover:bg-gray-50 dark:hover:bg-neutral-700/30"
                                >
                                    <td className="px-6 py-4">
                                        <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                            {svc.name}
                                        </p>
                                        {svc.description && (
                                            <p className="mt-0.5 text-xs text-gray-400 dark:text-neutral-500 line-clamp-1 max-w-xs">
                                                {svc.description}
                                            </p>
                                        )}
                                    </td>
                                    <td className="px-6 py-4">
                                        <code className="rounded bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-900 dark:bg-neutral-700 dark:text-neutral-100">
                                            {svc.code}
                                        </code>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span
                                            className={`inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ${CATEGORY_COLORS[svc.category] || CATEGORY_COLORS.other}`}
                                        >
                                            {CATEGORY_LABELS[svc.category] ||
                                                svc.category}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <span className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                            {Number(
                                                svc.default_price,
                                            ).toLocaleString()}{' '}
                                            FCFA
                                        </span>
                                    </td>
                                    <td className="px-6 py-4">
                                        {svc.requires_upfront_payment ? (
                                            <span className="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                                Avant service
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                Après service
                                            </span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4">
                                        {svc.is_active ? (
                                            <span className="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                Actif
                                            </span>
                                        ) : (
                                            <span className="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                Inactif
                                            </span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                            <Link
                                                href={route(
                                                    'finance.services.edit',
                                                    svc.id,
                                                )}
                                                className="rounded-lg p-2 text-gray-400 transition-colors hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-900/20"
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
                                                className="rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                                            >
                                                <TrashIcon className="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
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
