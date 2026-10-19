/**
 * Finance module components — pieces shared by two or more finance pages so
 * money formatting, status tones and invoice structure stay identical across
 * the module.
 */

export {
    ClaimStatusBadge,
    InvoiceStatusBadge,
    PayerTypeBadge,
    ServiceCategoryBadge,
} from '@/pages/Finance/components/FinanceStatusBadges';

export { InvoiceTotalsPanel } from '@/pages/Finance/components/InvoiceTotalsPanel';
export type { TotalsLine } from '@/pages/Finance/components/InvoiceTotalsPanel';

export { InvoicePaymentSummary } from '@/pages/Finance/components/InvoicePaymentSummary';
export type { PaymentSummaryClaim } from '@/pages/Finance/components/InvoicePaymentSummary';

export {
    InvoiceLineRow,
    INVOICE_ITEM_TYPE_OPTIONS,
} from '@/pages/Finance/components/InvoiceLineRow';
export type { InvoiceLineDraft } from '@/pages/Finance/components/InvoiceLineRow';

export { PayerCard } from '@/pages/Finance/components/PayerCard';
export type { PayerCardData } from '@/pages/Finance/components/PayerCard';

export {
    CURRENCY,
    formatDate,
    formatMoney,
    formatSignedMoney,
    humanize,
} from '@/pages/Finance/components/format';

export { financeBreadcrumbs } from '@/pages/Finance/components/breadcrumbs';
export type { FinanceBreadcrumbLabels } from '@/pages/Finance/components/breadcrumbs';
