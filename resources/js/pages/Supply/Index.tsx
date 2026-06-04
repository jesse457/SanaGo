import {
    PlusIcon, PencilSquareIcon, TrashIcon,
    ChevronRightIcon, XMarkIcon, MagnifyingGlassIcon,
    HomeIcon, CubeIcon, ArrowUpIcon, ArrowDownIcon,
    Squares2X2Icon, ListBulletIcon,
    ExclamationTriangleIcon, CurrencyDollarIcon,
    FunnelIcon, BeakerIcon, ArrowPathIcon
} from '@heroicons/react/24/outline';
import { Head, Link, useForm, router } from '@inertiajs/react';
import React, { useState, useMemo, FormEvent } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Supply {
    id: number;
    name: string;
    image_url: string | null;
    unit_of_measure: string;
    current_stock: number;
    min_stock_level: number;
    cost_per_unit: number;
    category: string;
    last_restocked: string | null;
}

type ViewMode = 'grid' | 'table';
type ModalMode = 'restock' | 'delete' | null;
type StockStatus = 'all' | 'critical' | 'low' | 'good';

export default function SupplyManagement({ supplies }: { supplies: Supply[] }) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [statusFilter, setStatusFilter] = useState<StockStatus>('all');
    const [viewMode, setViewMode] = useState<ViewMode>('table');

    const [modalOpen, setModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState<ModalMode>(null);
    const [selectedItem, setSelectedItem] = useState<Supply | null>(null);

    const { data, setData, put, processing, reset, errors, clearErrors } = useForm({
        add_amount: '' as string | number,
    });

    const getStockStatus = (supply: Supply) => {
        if (supply.current_stock === 0) return 'out';
        if (supply.current_stock <= supply.min_stock_level) return 'low';
        return 'good';
    };

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'out': return 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800';
            case 'low': return 'bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800';
            case 'good': return 'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800';
            default: return 'bg-gray-100 dark:bg-neutral-700 text-gray-700 dark:text-neutral-300';
        }
    };

    const getStatusLabel = (status: string) => {
        switch (status) {
            case 'out': return t('page.supplies.out_of_stock');
            case 'low': return t('page.supplies.low_stock');
            case 'good': return t('page.supplies.in_stock');
            default: return t('page.supplies.unknown');
        }
    };

    const openModal = (mode: ModalMode, item: Supply) => {
        setModalMode(mode);
        setSelectedItem(item);
        if (mode === 'restock') {
            reset();
            clearErrors();
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
        if (!selectedItem) return;

        if (modalMode === 'delete') {
            router.delete(route('supplies.destroy', selectedItem.id), {
                onSuccess: closeModal,
            });
        } else if (modalMode === 'restock') {
            put(route('supplies.restock', selectedItem.id), {
                onSuccess: closeModal,
            });
        }
    };

    const filteredSupplies = useMemo(() => {
        return supplies.filter(s => {
            const matchesSearch = s.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                  s.category.toLowerCase().includes(searchQuery.toLowerCase());
            
            const status = getStockStatus(s);
            const matchesStatus = statusFilter === 'all' ||
                                  (statusFilter === 'critical' && status === 'out') ||
                                  (statusFilter === 'low' && status === 'low') ||
                                  (statusFilter === 'good' && status === 'good');
            
            return matchesSearch && matchesStatus;
        });
    }, [supplies, searchQuery, statusFilter]);

    const stats = useMemo(() => {
        const totalValue = supplies.reduce((acc, s) => acc + (s.current_stock * Number(s.cost_per_unit)), 0);
        const lowStockCount = supplies.filter(s => s.current_stock <= s.min_stock_level && s.current_stock > 0).length;
        const outOfStockCount = supplies.filter(s => s.current_stock === 0).length;

        return { totalItems: supplies.length, totalValue, lowStockCount, outOfStockCount };
    }, [supplies]);

    return (
        <AppLayout headerTitle={t('page.supplies.title')} headerSubtitle={t('page.supplies.subtitle')}>
            <Head title={t('page.supplies.head_title')} />

            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 py-6">
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer transition-colors"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li>{t('page.supplies.administration')}</li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-medium">{t('page.supplies.inventory')}</li>
                    </ol>
                </nav>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard label={t('page.supplies.total_items')} value={stats.totalItems} icon={<CubeIcon className="w-5 h-5" />} color="blue" />
                    <StatCard label={t('page.supplies.inventory_value')} value={`$${stats.totalValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`} icon={<CurrencyDollarIcon className="w-5 h-5" />} color="emerald" />
                    <StatCard label={t('page.supplies.low_stock_alerts')} value={stats.lowStockCount} icon={<ExclamationTriangleIcon className="w-5 h-5" />} color="amber" />
                    <StatCard label={t('page.supplies.out_of_stock_label')} value={stats.outOfStockCount} icon={<ArrowDownIcon className="w-5 h-5" />} color="red" />
                </div>

                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <div className="relative w-full sm:w-80">
                            <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input type="text" placeholder={t('page.supplies.search_placeholder')} value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)} className="pl-9 pr-4 py-2 w-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm dark:text-neutral-100" />
                        </div>

                        <div className="relative">
                            <select value={statusFilter} onChange={(e) => setStatusFilter(e.target.value as StockStatus)} className="appearance-none pl-4 pr-10 py-2 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm dark:text-neutral-100">
                                <option value="all">{t('page.supplies.all_status')}</option>
                                <option value="good">{t('page.supplies.in_stock')}</option>
                                <option value="low">{t('page.supplies.low_stock')}</option>
                                <option value="critical">{t('page.supplies.out_of_stock')}</option>
                            </select>
                            <FunnelIcon className="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                        </div>
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="flex bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-1 shadow-sm">
                            <button onClick={() => setViewMode('grid')} className={`p-1.5 rounded-md transition-all ${viewMode === 'grid' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600 dark:hover:text-neutral-300'}`}><Squares2X2Icon className="w-5 h-5" /></button>
                            <button onClick={() => setViewMode('table')} className={`p-1.5 rounded-md transition-all ${viewMode === 'table' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600 dark:hover:text-neutral-300'}`}><ListBulletIcon className="w-5 h-5" /></button>
                        </div>
                        <Link href={route('supplies.create')} className="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm">
                            <PlusIcon className="w-4 h-4" /> {t('page.supplies.add_supply')}
                        </Link>
                    </div>
                </div>

                {viewMode === 'grid' && (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {filteredSupplies.map((supply) => {
                            const status = getStockStatus(supply);
                            return (
                                <div key={supply.id} className="group bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 hover:shadow-md transition-all relative flex flex-col h-full">
                                    <div className="flex justify-between items-start mb-4">
                                        <div className={`p-3 rounded-lg overflow-hidden ${status === 'good' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'bg-gray-100 dark:bg-neutral-700 text-gray-500 dark:text-neutral-400'}`}>
                                            {supply.image_url ? (
                                                <img src={supply.image_url} alt="" className="w-6 h-6 object-cover rounded" />
                                            ) : (
                                                <BeakerIcon className="w-6 h-6" />
                                            )}
                                        </div>
                                        <span className={`px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide border ${getStatusColor(status)}`}>{getStatusLabel(status)}</span>
                                    </div>
                                    <div className="mb-4 flex-grow">
                                        <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100 truncate mb-1">{supply.name}</h3>
                                        <p className="text-xs text-gray-500 dark:text-neutral-400 font-bold uppercase tracking-wide mb-4">{supply.category}</p>
                                        <div className="space-y-3">
                                            <div>
                                                <div className="flex justify-between text-xs font-semibold mb-1">
                                                    <span className="text-gray-500 dark:text-neutral-400">{t('page.supplies.stock_level')}</span>
                                                    <span className={`font-bold ${status === 'low' || status === 'out' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-neutral-100'}`}>{supply.current_stock} / {supply.min_stock_level} min</span>
                                                </div>
                                                <div className="w-full bg-gray-100 dark:bg-neutral-700 rounded-full h-2">
                                                    <div className={`h-2 rounded-full ${status === 'good' ? 'bg-emerald-500' : status === 'low' ? 'bg-amber-500' : 'bg-red-500'}`} style={{ width: `${Math.min((supply.current_stock / (supply.min_stock_level * 2)) * 100, 100)}%` }}></div>
                                                </div>
                                            </div>
                                            <div className="grid grid-cols-2 gap-2 pt-2">
                                                <div className="bg-gray-50 dark:bg-neutral-700/50 p-2 rounded-lg border border-gray-100 dark:border-neutral-700">
                                                    <p className="text-[10px] text-gray-400 dark:text-neutral-500 uppercase font-bold">{t('page.supplies.unit_cost')}</p>
                                                    <p className="text-sm font-bold text-gray-700 dark:text-neutral-300">${Number(supply.cost_per_unit).toFixed(2)}</p>
                                                </div>
                                                <div className="bg-gray-50 dark:bg-neutral-700/50 p-2 rounded-lg border border-gray-100 dark:border-neutral-700">
                                                    <p className="text-[10px] text-gray-400 dark:text-neutral-500 uppercase font-bold">{t('page.supplies.total_value')}</p>
                                                    <p className="text-sm font-bold text-gray-700 dark:text-neutral-300">${(supply.current_stock * supply.cost_per_unit).toFixed(2)}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-neutral-700">
                                        <button onClick={() => openModal('restock', supply)} className="flex-1 flex items-center justify-center gap-2 py-2 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg text-xs font-bold transition-colors">
                                            <ArrowUpIcon className="w-4 h-4" /> {t('page.supplies.restock')}
                                        </button>
                                        <Link href={route('supplies.edit', supply.id)} className="p-2 border border-gray-200 dark:border-neutral-600 hover:bg-gray-50 dark:hover:bg-neutral-700 text-gray-600 dark:text-neutral-400 rounded-lg transition-colors">
                                            <PencilSquareIcon className="w-4 h-4" />
                                        </Link>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}

                {viewMode === 'table' && (
                    <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead className="bg-gray-50 dark:bg-neutral-800">
                                    <tr>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('page.supplies.col_supply_name')}</th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('page.supplies.col_category')}</th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('page.supplies.col_stock_level')}</th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('page.supplies.col_cost_value')}</th>
                                        <th scope="col" className="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('common.actions')}</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                                    {filteredSupplies.map((supply) => {
                                        const status = getStockStatus(supply);
                                        return (
                                            <tr key={supply.id} className="hover:bg-gray-50 dark:hover:bg-neutral-700/30 transition-colors">
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex items-center">
                                                        <div className="flex-shrink-0 h-10 w-10 rounded-lg overflow-hidden border border-blue-100 dark:border-blue-800">
                                                            {supply.image_url ? (
                                                                <img src={supply.image_url} alt="" className="h-full w-full object-cover" />
                                                            ) : (
                                                                <div className="h-full w-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400"><CubeIcon className="w-5 h-5" /></div>
                                                            )}
                                                        </div>
                                                        <div className="ml-4">
                                                            <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">{supply.name}</div>
                                                            <div className="text-xs font-semibold text-gray-500 dark:text-neutral-400">{t('page.supplies.alert_min')}: {supply.min_stock_level}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className="px-2.5 py-1 inline-flex text-xs font-bold rounded-full bg-gray-100 dark:bg-neutral-700 text-gray-700 dark:text-neutral-300">{supply.category}</span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex flex-col gap-1.5">
                                                        <div className="flex items-center gap-2">
                                                            <span className={`text-sm font-bold ${status === 'good' ? 'text-gray-900 dark:text-neutral-100' : 'text-red-600 dark:text-red-400'}`}>{supply.current_stock}</span>
                                                            <span className="text-[10px] font-bold text-gray-400 dark:text-neutral-500 uppercase">{supply.unit_of_measure}</span>
                                                        </div>
                                                        <div className="w-24 bg-gray-100 dark:bg-neutral-700 rounded-full h-1.5">
                                                            <div className={`h-1.5 rounded-full ${status === 'good' ? 'bg-emerald-500' : status === 'low' ? 'bg-amber-500' : 'bg-red-500'}`} style={{ width: `${Math.min((supply.current_stock / (supply.min_stock_level * 2)) * 100, 100)}%` }}></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">${Number(supply.cost_per_unit).toFixed(2)}</div>
                                                    <div className="text-xs text-gray-500 dark:text-neutral-400 font-medium">Total: ${(supply.current_stock * supply.cost_per_unit).toFixed(2)}</div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div className="flex justify-end gap-2">
                                                        <button onClick={() => openModal('restock', supply)} className="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 p-1.5 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors" title={t('page.supplies.restock')}><ArrowUpIcon className="w-4 h-4" /></button>
                                                        <Link href={route('supplies.edit', supply.id)} className="text-gray-500 dark:text-neutral-400 hover:text-gray-800 dark:hover:text-neutral-200 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-600 p-1.5 rounded-md hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors" title={t('common.edit')}><PencilSquareIcon className="w-4 h-4" /></Link>
                                                        <button onClick={() => openModal('delete', supply)} className="text-red-500 dark:text-red-400 hover:text-red-700 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 p-1.5 rounded-md hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors" title={t('common.delete')}><TrashIcon className="w-4 h-4" /></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}
            </div>

            {/* Modal for Restock/Delete */}
            {modalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onClick={closeModal}></div>
                    <div className="relative bg-white dark:bg-neutral-800 rounded-xl w-full max-w-sm shadow-xl border border-gray-200 dark:border-neutral-700 overflow-hidden">
                        <form onSubmit={handleSubmit} className="p-6">
                            
                            {modalMode === 'delete' ? (
                                <div className="text-center">
                                    <div className="mx-auto w-14 h-14 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center mb-4"><ExclamationTriangleIcon className="w-7 h-7 text-red-600 dark:text-red-400" /></div>
                                    <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100 mb-2">{t('page.supplies.delete_item')}</h3>
                                    <p className="text-gray-500 dark:text-neutral-400 text-sm mb-6">{t('page.supplies.delete_confirm', { name: selectedItem?.name })}</p>
                                </div>
                            ) : (
                                <div>
                                    <div className="flex justify-between items-center mb-4">
                                        <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">Restock Inventory</h3>
                                        <button type="button" onClick={closeModal} className="text-gray-400 hover:text-gray-600 dark:hover:text-neutral-300"><XMarkIcon className="w-5 h-5"/></button>
                                    </div>
                                    <div className="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800 flex items-center justify-between mb-4">
                                        <div>
                                            <p className="text-[10px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wide">Current</p>
                                            <p className="text-2xl font-bold text-blue-900 dark:text-blue-300">{selectedItem?.current_stock} <span className="text-sm font-semibold">{selectedItem?.unit_of_measure}</span></p>
                                        </div>
                                    </div>
                                    <div>
                                        <label className="block text-xs font-bold text-gray-600 dark:text-neutral-400 uppercase tracking-wide mb-1.5">Quantity to Add</label>
                                        <input
                                            type="number" min="1" autoFocus
                                            value={data.add_amount}
                                            onChange={(e) => setData('add_amount', e.target.value)}
                                            className="w-full px-4 py-2.5 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-neutral-100"
                                            placeholder="Enter amount..."
                                        />
                                        {errors.add_amount && <p className="text-red-500 text-xs mt-1 font-medium">{errors.add_amount}</p>}
                                    </div>
                                </div>
                            )}

                            <div className="flex gap-3 pt-6 mt-2">
                                <button type="button" onClick={closeModal} disabled={processing} className="flex-1 py-2 text-sm font-bold text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700">Cancel</button>
                                <button type="submit" disabled={processing} className={`flex-1 py-2 text-sm font-bold text-white rounded-lg shadow-sm flex items-center justify-center gap-2 ${modalMode === 'delete' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'}`}>
                                    {processing && <ArrowPathIcon className="animate-spin h-4 w-4" />}
                                    {modalMode === 'delete' ? 'Delete' : 'Update Stock'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}

function StatCard({ label, value, icon, color }: any) {
    const colors: any = {
        blue: 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400', emerald: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400',
        amber: 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400', red: 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400'
    };
    return (
        <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
            <div className={`p-3 rounded-lg ${colors[color]}`}>{icon}</div>
            <div>
                <p className="text-xl font-bold text-gray-900 dark:text-neutral-100 leading-none">{value}</p>
                <p className="text-xs font-bold text-gray-500 dark:text-neutral-400 mt-1 uppercase tracking-wide">{label}</p>
            </div>
        </div>
    );
}
