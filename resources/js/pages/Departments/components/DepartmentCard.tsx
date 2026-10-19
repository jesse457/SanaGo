import {
    BuildingOffice2Icon,
    PencilSquareIcon,
    PhoneIcon,
    TrashIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { Badge, Button, Card, CardContent } from '@/Components/ui';

export interface DepartmentCardData {
    id: number;
    name: string;
    code: string;
    description: string;
    phone_extension: string;
    is_active: boolean;
    type: 'clinical' | 'administrative' | 'support';
    staff_count: number;
    ward_count: number;
    head_of_department?: { id: number; name: string } | null;
}

interface DepartmentCardProps {
    department: DepartmentCardData;
    onEdit: (department: DepartmentCardData) => void;
    onDelete: (department: DepartmentCardData) => void;
    labels: {
        staff: string;
        wards: string;
        noHead: string;
        active: string;
        inactive: string;
        edit: string;
        delete: string;
    };
}

/**
 * DepartmentCard — one hospital unit in the departments grid: identity, code,
 * headcount/ward strip, head-of-department line and hover actions.
 */
export function DepartmentCard({
    department,
    onEdit,
    onDelete,
    labels,
}: DepartmentCardProps) {
    return (
        <Card className="group flex flex-col transition-[border-color,box-shadow] duration-300 ease-premium hover:border-line-strong hover:shadow-surface">
            <CardContent className="flex flex-1 flex-col gap-4">
                <div className="flex items-start justify-between gap-3">
                    <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-accent">
                        <BuildingOffice2Icon className="h-5 w-5" />
                    </span>
                    <Badge
                        tone={department.is_active ? 'positive' : 'muted'}
                        dot
                    >
                        {department.is_active ? labels.active : labels.inactive}
                    </Badge>
                </div>

                <div className="min-w-0">
                    <h3 className="truncate text-[15px] leading-tight font-semibold tracking-tight text-ink">
                        {department.name}
                    </h3>
                    <div className="mt-1.5 flex flex-wrap items-center gap-2">
                        <span className="rounded-md bg-accent-soft px-1.5 py-0.5 font-mono text-[11px] font-semibold text-accent-ink">
                            {department.code}
                        </span>
                        <span className="text-[11px] font-medium text-ink-subtle uppercase">
                            {department.type}
                        </span>
                    </div>
                </div>

                <p className="line-clamp-2 h-10 text-[13px] leading-relaxed text-ink-muted">
                    {department.description}
                </p>

                <div className="flex items-center justify-between gap-2 border-t border-line pt-3">
                    <div className="flex gap-5">
                        <div>
                            <p className="text-sm font-semibold text-ink tabular-nums">
                                {department.staff_count}
                            </p>
                            <p className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                {labels.staff}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm font-semibold text-ink tabular-nums">
                                {department.ward_count}
                            </p>
                            <p className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                {labels.wards}
                            </p>
                        </div>
                    </div>
                    <span className="inline-flex items-center gap-1.5 rounded-md bg-surface-muted px-2.5 py-1.5 text-[12px] font-medium text-ink-subtle tabular-nums">
                        <PhoneIcon className="h-3.5 w-3.5" />
                        {department.phone_extension}
                    </span>
                </div>

                <div className="flex items-center gap-2 text-[12px] text-ink-subtle">
                    <UserIcon className="h-3.5 w-3.5 shrink-0" />
                    <span className="truncate">
                        {department.head_of_department ? (
                            <span className="font-medium text-ink-muted">
                                {department.head_of_department.name}
                            </span>
                        ) : (
                            <span className="italic">{labels.noHead}</span>
                        )}
                    </span>
                </div>

                <div className="flex gap-2 opacity-0 transition-opacity duration-200 ease-premium group-hover:opacity-100 focus-within:opacity-100">
                    <Button
                        variant="secondary"
                        size="sm"
                        icon={PencilSquareIcon}
                        className="flex-1"
                        onClick={() => onEdit(department)}
                    >
                        {labels.edit}
                    </Button>
                    <Button
                        variant="ghost"
                        size="icon"
                        aria-label={labels.delete}
                        icon={TrashIcon}
                        onClick={() => onDelete(department)}
                    />
                </div>
            </CardContent>
        </Card>
    );
}
