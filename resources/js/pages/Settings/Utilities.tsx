import {
    AdjustmentsHorizontalIcon,
    BuildingOfficeIcon,
    InboxStackIcon,
    PlusIcon,
    TagIcon,
} from '@heroicons/react/24/outline';
import { Head, router, useForm } from '@inertiajs/react';
import React, { useMemo, useState } from 'react';
import {
    Button,
    PageHeader,
    Tabs,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import type { TabItem } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type {
    BedRecord,
    BedTypeRecord,
    UtilitiesEntityType,
    WardRecord,
} from '@/pages/Settings/components/types';
import { UtilitiesFormDialog } from '@/pages/Settings/components/UtilitiesFormDialog';
import type {
    UtilitiesFormData,
    UtilitiesModalMode,
} from '@/pages/Settings/components/UtilitiesFormDialog';
import {
    BedGrid,
    BedTypeList,
    UtilitiesEmpty,
    WardList,
} from '@/pages/Settings/components/UtilitiesLists';

interface Props {
    wards: WardRecord[];
    beds: BedRecord[];
    bedTypes: BedTypeRecord[];
}

type UtilitiesTab = 'wards' | 'beds' | 'types';

const ENTITY_BY_TAB: Record<UtilitiesTab, UtilitiesEntityType> = {
    wards: 'ward',
    beds: 'bed',
    types: 'type',
};

export default function WardManagement({ wards, beds, bedTypes }: Props) {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState<UtilitiesTab>('wards');
    const [searchQuery, setSearchQuery] = useState('');

    const [modalOpen, setModalOpen] = useState(false);
    const [modalEntity, setModalEntity] = useState<UtilitiesEntityType>('ward');
    const [modalMode, setModalMode] = useState<UtilitiesModalMode>(null);
    const [selectedItem, setSelectedItem] = useState<any>(null);

    const { data, setData, post, put, processing, errors, reset, clearErrors } =
        useForm<UtilitiesFormData>({
            entity_type: '',
            name: '',
            code: '',
            floor: '',
            capacity: '',
            description: '',
            ward_id: '',
            bed_type_id: '',
            status: 'Available',
        });

    const currentEntity = ENTITY_BY_TAB[activeTab];

    const tabs: TabItem[] = [
        {
            value: 'wards',
            label: t('page.utilities.wards_tab'),
            icon: BuildingOfficeIcon,
            count: wards.length,
        },
        {
            value: 'beds',
            label: t('page.utilities.beds_tab'),
            icon: InboxStackIcon,
            count: beds.length,
        },
        {
            value: 'types',
            label: t('page.utilities.bed_types_tab'),
            icon: TagIcon,
            count: bedTypes.length,
        },
    ];

    const openModal = (
        entity: UtilitiesEntityType,
        mode: UtilitiesModalMode,
        item?: any,
    ) => {
        setModalEntity(entity);
        setModalMode(mode);
        setSelectedItem(item ?? null);
        clearErrors();

        if (mode === 'edit' && item) {
            setData({
                entity_type: entity,
                name: item.name || '',
                code: item.code || '',
                floor: item.floor || '',
                capacity: item.capacity || '',
                description: item.description || '',
                ward_id: item.ward_id?.toString() || '',
                bed_type_id: item.bed_type_id?.toString() || '',
                status: item.status || 'Available',
            });
        } else {
            reset();
            setData('entity_type', entity);
        }

        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setModalMode(null);
        setSelectedItem(null);
        reset();
        clearErrors();
    };

    const handleSubmit = (event: React.FormEvent) => {
        event.preventDefault();

        if (modalMode === 'delete') {
            router.delete(route('infrastructure.destroy', selectedItem.id), {
                data: { entity_type: modalEntity },
                onSuccess: closeModal,
            });

            return;
        }

        if (modalMode === 'edit') {
            put(route('infrastructure.update', selectedItem.id), {
                onSuccess: closeModal,
            });

            return;
        }

        post(route('infrastructure.store'), { onSuccess: closeModal });
    };

    const openAddModal = () => openModal(currentEntity, 'add');

    const filtered = useMemo(() => {
        const query = searchQuery.toLowerCase();

        return {
            wards: wards.filter(
                (ward) =>
                    ward.name?.toLowerCase().includes(query) ||
                    ward.code?.toLowerCase().includes(query),
            ),
            beds: beds.filter((bed) => bed.code?.toLowerCase().includes(query)),
            types: bedTypes.filter((type) =>
                type.name?.toLowerCase().includes(query),
            ),
        };
    }, [wards, beds, bedTypes, searchQuery]);

    return (
        <AppLayout
            headerTitle={t('page.utilities.title')}
            headerSubtitle={t('page.utilities.subtitle')}
        >
            <Head title={t('page.utilities.head_title')} />

            <div className="mx-auto max-w-7xl space-y-5">
                <PageHeader
                    title={t('page.utilities.title')}
                    description={t('page.utilities.subtitle')}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/' },
                        { label: t('page.utilities.head_title') },
                    ]}
                    actions={
                        <Button icon={PlusIcon} onClick={openAddModal}>
                            {t('page.utilities.add')}{' '}
                            <span className="capitalize">
                                {activeTab.slice(0, -1)}
                            </span>
                        </Button>
                    }
                />

                <Tabs
                    items={tabs}
                    value={activeTab}
                    onChange={(value) => setActiveTab(value as UtilitiesTab)}
                />

                <Toolbar>
                    <ToolbarSearch
                        value={searchQuery}
                        onChange={setSearchQuery}
                        placeholder={t('page.utilities.search_placeholder', {
                            tab: activeTab,
                        })}
                        className="sm:max-w-xs"
                    />
                    <ToolbarSpacer />
                    <Button
                        variant="secondary"
                        size="sm"
                        icon={AdjustmentsHorizontalIcon}
                        aria-label={t('common.manage')}
                    >
                        {t('common.manage')}
                    </Button>
                </Toolbar>

                {activeTab === 'wards' &&
                    (filtered.wards.length > 0 ? (
                        <WardList
                            wards={filtered.wards}
                            onEdit={(ward) => openModal('ward', 'edit', ward)}
                            onDelete={(ward) =>
                                openModal('ward', 'delete', ward)
                            }
                        />
                    ) : (
                        <UtilitiesEmpty
                            title={t('page.utilities.no_records', {
                                tab: activeTab,
                            })}
                            description={t('page.utilities.get_started')}
                            actionLabel={`${t('page.utilities.add_first')} ward`}
                            onAction={openAddModal}
                        />
                    ))}

                {activeTab === 'beds' &&
                    (filtered.beds.length > 0 ? (
                        <BedGrid
                            beds={filtered.beds}
                            onEdit={(bed) => openModal('bed', 'edit', bed)}
                            onDelete={(bed) => openModal('bed', 'delete', bed)}
                        />
                    ) : (
                        <UtilitiesEmpty
                            title={t('page.utilities.no_records', {
                                tab: activeTab,
                            })}
                            description={t('page.utilities.get_started')}
                            actionLabel={`${t('page.utilities.add_first')} bed`}
                            onAction={openAddModal}
                        />
                    ))}

                {activeTab === 'types' &&
                    (filtered.types.length > 0 ? (
                        <BedTypeList
                            types={filtered.types}
                            onEdit={(type) => openModal('type', 'edit', type)}
                            onDelete={(type) =>
                                openModal('type', 'delete', type)
                            }
                        />
                    ) : (
                        <UtilitiesEmpty
                            title={t('page.utilities.no_records', {
                                tab: activeTab,
                            })}
                            description={t('page.utilities.get_started')}
                            actionLabel={`${t('page.utilities.add_first')} type`}
                            onAction={openAddModal}
                        />
                    ))}
            </div>

            <UtilitiesFormDialog
                open={modalOpen}
                mode={modalMode}
                entity={modalEntity}
                data={data}
                errors={errors}
                processing={processing}
                wards={wards}
                bedTypes={bedTypes}
                selectedLabel={selectedItem?.name || selectedItem?.code}
                onChange={setData}
                onSubmit={handleSubmit}
                onClose={closeModal}
            />
        </AppLayout>
    );
}
