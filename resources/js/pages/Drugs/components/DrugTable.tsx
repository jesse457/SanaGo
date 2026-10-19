import {
    ArchiveBoxXMarkIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import {
    Button,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import {
    ExpiryBadge,
    ExpiryDate,
    StockBadge,
    batchStateOf,
} from '@/pages/Drugs/components/DrugBadges';
import { DrugImage } from '@/pages/Drugs/components/DrugImage';
import type { BatchRow, Drug } from '@/pages/Drugs/components/types';

interface DrugTableProps {
    drugs: Drug[];
    onEdit: (drug: Drug) => void;
    onDelete: (drug: Drug) => void;
}

/** DrugTable — the table view of the catalogue, one row per formulation. */
export function DrugTable({ drugs, onEdit, onDelete }: DrugTableProps) {
    const { t } = useTranslation();

    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHeaderCell>{t('general.medication')}</TableHeaderCell>
                    <TableHeaderCell numeric>
                        {t('general.stock')}
                    </TableHeaderCell>
                    <TableHeaderCell>{t('general.status')}</TableHeaderCell>
                    <TableHeaderCell>
                        {t('general.earliestExpiry')}
                    </TableHeaderCell>
                    <TableHeaderCell className="text-right">
                        {t('common.actions')}
                    </TableHeaderCell>
                </TableRow>
            </TableHeader>
            <TableBody>
                {drugs.map((drug) => {
                    const earliestBatch = drug.batches?.find(
                        (b) => b.current_quantity > 0,
                    );

                    return (
                        <TableRow key={drug.id}>
                            <TableCell>
                                <div className="flex items-center gap-3">
                                    <DrugImage
                                        src={drug.image_url}
                                        name={drug.name}
                                        size="sm"
                                    />
                                    <div className="min-w-0">
                                        <p className="truncate text-[13px] font-semibold tracking-tight text-ink">
                                            {drug.name}
                                        </p>
                                        <p className="truncate text-[12px] text-ink-subtle">
                                            {drug.dosage_units}
                                        </p>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell numeric>
                                <span
                                    className={
                                        drug.total_stock === 0
                                            ? 'text-ink-subtle'
                                            : 'font-semibold text-ink'
                                    }
                                >
                                    {drug.total_stock}
                                </span>
                            </TableCell>
                            <TableCell>
                                <StockBadge drug={drug} />
                            </TableCell>
                            <TableCell muted>
                                {earliestBatch ? (
                                    <div className="flex flex-col">
                                        <ExpiryDate
                                            dateStr={earliestBatch.expiry_date}
                                        />
                                        <span className="text-[11.5px] text-ink-subtle tabular-nums">
                                            {earliestBatch.batch_number}
                                        </span>
                                    </div>
                                ) : (
                                    <span className="text-[12px] text-ink-subtle italic">
                                        {t('general.noActiveStock')}
                                    </span>
                                )}
                            </TableCell>
                            <TableCell>
                                <div className="flex justify-end gap-1">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        icon={PencilSquareIcon}
                                        aria-label={t('common.edit')}
                                        title={t('common.edit')}
                                        onClick={() => onEdit(drug)}
                                    />
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        icon={TrashIcon}
                                        aria-label={t('common.delete')}
                                        title={t('common.delete')}
                                        className="hover:text-critical-ink"
                                        onClick={() => onDelete(drug)}
                                    />
                                </div>
                            </TableCell>
                        </TableRow>
                    );
                })}
            </TableBody>
        </Table>
    );
}

interface BatchTableProps {
    batches: BatchRow[];
    onDeplete: (batch: BatchRow) => void;
}

/** BatchTable — every live batch across the catalogue, soonest expiry first. */
export function BatchTable({ batches, onDeplete }: BatchTableProps) {
    const { t } = useTranslation();

    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHeaderCell>{t('general.medication')}</TableHeaderCell>
                    <TableHeaderCell>
                        {t('general.batchNumberShort')}
                    </TableHeaderCell>
                    <TableHeaderCell>{t('general.expiry')}</TableHeaderCell>
                    <TableHeaderCell numeric>
                        {t('general.qty')}
                    </TableHeaderCell>
                    <TableHeaderCell>{t('general.status')}</TableHeaderCell>
                    <TableHeaderCell className="text-right">
                        {t('common.actions')}
                    </TableHeaderCell>
                </TableRow>
            </TableHeader>
            <TableBody>
                {batches.map((batch) => {
                    const depleted = batchStateOf(batch) === 'depleted';

                    return (
                        <TableRow key={`${batch.id}-${batch.drugId}`}>
                            <TableCell muted={depleted}>
                                <p className="text-[13px] font-medium">
                                    {batch.drugName}
                                </p>
                                <p className="text-[12px] text-ink-subtle">
                                    {batch.drugDosage}
                                </p>
                            </TableCell>
                            <TableCell muted className="font-mono text-[12px]">
                                {batch.batch_number}
                            </TableCell>
                            <TableCell
                                muted={depleted}
                                className="tabular-nums"
                            >
                                <ExpiryDate dateStr={batch.expiry_date} />
                            </TableCell>
                            <TableCell numeric muted={depleted}>
                                {batch.current_quantity}
                            </TableCell>
                            <TableCell>
                                <ExpiryBadge batch={batch} />
                            </TableCell>
                            <TableCell>
                                <div className="flex justify-end">
                                    {!depleted && (
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            icon={ArchiveBoxXMarkIcon}
                                            aria-label={t(
                                                'general.depleteStock',
                                            )}
                                            title={t('general.depleteStock')}
                                            className="hover:text-warning-ink"
                                            onClick={() => onDeplete(batch)}
                                        />
                                    )}
                                </div>
                            </TableCell>
                        </TableRow>
                    );
                })}
            </TableBody>
        </Table>
    );
}
