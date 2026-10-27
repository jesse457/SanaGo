import {
    PlusIcon,
    BuildingOffice2Icon,
    UserGroupIcon,
    CheckBadgeIcon,
    BeakerIcon,
    ExclamationTriangleIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import {
    Alert,
    Button,
    Checkbox,
    Dialog,
    EmptyState,
    Field,
    FieldSet,
    Input,
    PageHeader,
    Select,
    StatCard,
    StatGrid,
    Textarea,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import AppLayout from '@/Layouts/AppLayout';
import { DepartmentCard } from '@/pages/Departments/components';
import type { DepartmentCardData } from '@/pages/Departments/components';

type Department = DepartmentCardData;

interface Props {
    departments: Department[];
    users: { id: number; name: string; department_id: number | null }[];
}

export default function Departments({ departments, users }: Props) {
    const [searchQuery, setSearchQuery] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState<
        'add' | 'edit' | 'delete' | null
    >(null);
    const [selectedItem, setSelectedItem] = useState<Department | null>(null);

    const {
        data,
        setData,
        post,
        put,
        delete: destroy,
        processing,
        errors,
        reset,
        clearErrors,
    } = useForm({
        name: '',
        code: '',
        description: '',
        phone_extension: '',
        type: 'clinical' as 'clinical' | 'administrative' | 'support',
        is_active: true,
        head_of_department_id: '' as string | number,
    });

    const openModal = (mode: 'add' | 'edit' | 'delete', dept?: Department) => {
        setModalMode(mode);
        setSelectedItem(dept || null);
        clearErrors();

        if (mode === 'edit' && dept) {
            setData({
                name: dept.name,
                code: dept.code,
                description: dept.description || '',
                phone_extension: dept.phone_extension,
                type: dept.type,
                is_active: dept.is_active,
                head_of_department_id: dept.head_of_department?.id || '',
            });
        } else if (mode === 'add') {
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
            post(route('departments.store'), {
                onSuccess: () => closeModal(),
            });
        } else if (modalMode === 'edit' && selectedItem) {
            put(route('departments.update', selectedItem.id), {
                onSuccess: () => closeModal(),
            });
        } else if (modalMode === 'delete' && selectedItem) {
            destroy(route('departments.destroy', selectedItem.id), {
                onSuccess: () => closeModal(),
            });
        }
    };

    const filteredDepartments = useMemo(() => {
        if (!searchQuery) {
            return departments;
        }

        const q = searchQuery.toLowerCase();

        return departments.filter(
            (d) =>
                d.name.toLowerCase().includes(q) ||
                d.code.toLowerCase().includes(q) ||
                d.type.toLowerCase().includes(q),
        );
    }, [departments, searchQuery]);

    const stats = useMemo(
        () => ({
            total: departments.length,
            active: departments.filter((d) => d.is_active).length,
            clinical: departments.filter((d) => d.type === 'clinical').length,
            staff: departments.reduce((acc, d) => acc + d.staff_count, 0),
        }),
        [departments],
    );

    const modalTitle = `${
        modalMode === 'add'
            ? 'Add'
            : modalMode === 'edit'
              ? 'Edit'
              : 'Confirm Delete'
    } Department`;

    return (
        <AppLayout
            headerTitle="Hospital Departments"
            headerSubtitle="Configure hospital units and service categories"
        >
            <Head title="Departments" />

            <div className="space-y-6 pb-12">
                <PageHeader
                    title="Hospital departments"
                    description="Configure hospital units and service categories."
                    breadcrumbs={[
                        { label: 'Infrastructure' },
                        { label: 'Departments' },
                    ]}
                    actions={
                        <Button
                            icon={PlusIcon}
                            onClick={() => openModal('add')}
                        >
                            Add department
                        </Button>
                    }
                />

                <StatGrid columns={4}>
                    <StatCard
                        label="Total Units"
                        value={stats.total}
                        icon={BuildingOffice2Icon}
                        tone="accent"
                    />
                    <StatCard
                        label="Operational"
                        value={stats.active}
                        icon={CheckBadgeIcon}
                        tone="positive"
                    />
                    <StatCard
                        label="Clinical Depts"
                        value={stats.clinical}
                        icon={BeakerIcon}
                        tone="warning"
                    />
                    <StatCard
                        label="Total Staff"
                        value={stats.staff}
                        icon={UserGroupIcon}
                        tone="neutral"
                    />
                </StatGrid>

                <Toolbar>
                    <ToolbarSearch
                        value={searchQuery}
                        onChange={setSearchQuery}
                        placeholder="Search departments..."
                    />
                    <ToolbarSpacer />
                </Toolbar>

                {filteredDepartments.length === 0 ? (
                    <EmptyState
                        icon={BuildingOffice2Icon}
                        title="No departments found"
                        description="Try adjusting your search or add a new department."
                        action={
                            <Button
                                icon={PlusIcon}
                                onClick={() => openModal('add')}
                            >
                                Add department
                            </Button>
                        }
                    />
                ) : (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {filteredDepartments.map((dept) => (
                            <DepartmentCard
                                key={dept.id}
                                department={dept}
                                onEdit={(d) => openModal('edit', d)}
                                onDelete={(d) => openModal('delete', d)}
                                labels={{
                                    staff: 'Staff',
                                    wards: 'Wards',
                                    noHead: 'No HOD assigned',
                                    active: 'Active',
                                    inactive: 'Inactive',
                                    edit: 'Edit',
                                    delete: 'Delete department',
                                }}
                            />
                        ))}
                    </div>
                )}
            </div>

            <Dialog
                open={modalOpen}
                onClose={closeModal}
                title={modalTitle}
                size="lg"
            >
                {modalMode === 'delete' ? (
                    <div className="space-y-4">
                        <Alert
                            tone="critical"
                            title="Delete department"
                            action={
                                <ExclamationTriangleIcon className="h-[18px] w-[18px]" />
                            }
                        >
                            Are you sure you want to delete{' '}
                            <span className="font-semibold text-ink">
                                "{selectedItem?.name}"
                            </span>
                            ? This action cannot be undone.
                        </Alert>

                        {(errors as Record<string, string>).error && (
                            <p className="text-[13px] font-medium text-critical-ink">
                                {(errors as Record<string, string>).error}
                            </p>
                        )}

                        <div className="flex justify-end gap-2 border-t border-line pt-4">
                            <Button
                                type="button"
                                variant="secondary"
                                onClick={closeModal}
                                disabled={processing}
                            >
                                Cancel
                            </Button>
                            <Button
                                type="button"
                                variant="danger"
                                onClick={handleSubmit}
                                loading={processing}
                            >
                                Delete department
                            </Button>
                        </div>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <FieldSet columns={2}>
                            <Field
                                label="Department name"
                                htmlFor="name"
                                required
                                error={errors.name}
                                className="sm:col-span-2"
                            >
                                <Input
                                    id="name"
                                    type="text"
                                    value={data.name}
                                    onChange={(e) =>
                                        setData('name', e.target.value)
                                    }
                                    placeholder="e.g. Cardiology"
                                    invalid={Boolean(errors.name)}
                                />
                            </Field>

                            <Field
                                label="Code"
                                htmlFor="code"
                                required
                                error={errors.code}
                            >
                                <Input
                                    id="code"
                                    type="text"
                                    value={data.code}
                                    onChange={(e) =>
                                        setData('code', e.target.value)
                                    }
                                    placeholder="e.g. CARD-01"
                                    invalid={Boolean(errors.code)}
                                />
                            </Field>

                            <Field
                                label="Phone ext."
                                htmlFor="phone_extension"
                                required
                                error={errors.phone_extension}
                            >
                                <Input
                                    id="phone_extension"
                                    type="text"
                                    value={data.phone_extension}
                                    onChange={(e) =>
                                        setData(
                                            'phone_extension',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="e.g. 101"
                                    invalid={Boolean(errors.phone_extension)}
                                />
                            </Field>

                            <Field
                                label="Classification"
                                htmlFor="type"
                                error={errors.type}
                                className="sm:col-span-2"
                            >
                                <Select
                                    id="type"
                                    value={data.type}
                                    onChange={(e) =>
                                        setData(
                                            'type',
                                            e.target
                                                .value as Department['type'],
                                        )
                                    }
                                    invalid={Boolean(errors.type)}
                                >
                                    <option value="clinical">Clinical</option>
                                    <option value="administrative">
                                        Administrative
                                    </option>
                                    <option value="support">
                                        Support Services
                                    </option>
                                </Select>
                            </Field>

                            <Field
                                label="Head of department"
                                htmlFor="head_of_department_id"
                                error={errors.head_of_department_id}
                                className="sm:col-span-2"
                            >
                                <Select
                                    id="head_of_department_id"
                                    value={data.head_of_department_id}
                                    onChange={(e) =>
                                        setData(
                                            'head_of_department_id',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="None"
                                    invalid={Boolean(
                                        errors.head_of_department_id,
                                    )}
                                >
                                    {users.map((u) => (
                                        <option key={u.id} value={u.id}>
                                            {u.name}
                                        </option>
                                    ))}
                                </Select>
                            </Field>

                            <Field
                                label="Description"
                                htmlFor="description"
                                error={errors.description}
                                className="sm:col-span-2"
                            >
                                <Textarea
                                    id="description"
                                    rows={3}
                                    value={data.description}
                                    onChange={(e) =>
                                        setData('description', e.target.value)
                                    }
                                    placeholder="Brief description..."
                                    invalid={Boolean(errors.description)}
                                />
                            </Field>

                            <div className="sm:col-span-2">
                                <Checkbox
                                    id="is_active"
                                    checked={data.is_active}
                                    onChange={(e) =>
                                        setData('is_active', e.target.checked)
                                    }
                                    label="Active"
                                />
                            </div>
                        </FieldSet>

                        <div className="flex justify-end gap-2 border-t border-line pt-4">
                            <Button
                                type="button"
                                variant="secondary"
                                onClick={closeModal}
                                disabled={processing}
                            >
                                Cancel
                            </Button>
                            <Button
                                type="submit"
                                loading={processing}
                                disabled={processing}
                            >
                                {modalMode === 'add'
                                    ? 'Create department'
                                    : 'Save changes'}
                            </Button>
                        </div>
                    </form>
                )}
            </Dialog>
        </AppLayout>
    );
}
