<?php

namespace App\Services\Billing;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('ym');
        $latest = Invoice::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $seq = (int) substr($latest->invoice_number, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function createInvoiceWithItems(array $invoiceData, array $items): Invoice
    {
        return DB::transaction(function () use ($invoiceData, $items) {
            $invoiceData['invoice_number'] = $this->generateInvoiceNumber();
            $invoiceData['status'] = $invoiceData['status'] ?? InvoiceStatus::DRAFT->value;
            $invoiceData['subtotal'] = 0;
            $invoiceData['tax_amount'] = 0;
            $invoiceData['total_amount'] = 0;

            $invoice = Invoice::create($invoiceData);

            foreach ($items as $item) {
                $item['total_price'] = $item['quantity'] * $item['unit_price'];
                $invoice->items()->create($item);
            }

            return $this->recalculateTotals($invoice);
        });
    }

    public function addItem(Invoice $invoice, array $itemData): InvoiceItem
    {
        $itemData['total_price'] = $itemData['quantity'] * $itemData['unit_price'];
        $item = $invoice->items()->create($itemData);
        $this->recalculateTotals($invoice);

        return $item;
    }

    public function removeItem(Invoice $invoice, InvoiceItem $item): void
    {
        $item->delete();
        $this->recalculateTotals($invoice);
    }

    public function updateStatus(Invoice $invoice, InvoiceStatus $status): Invoice
    {
        $invoice->status = $status;

        if ($status === InvoiceStatus::PAID) {
            $invoice->paid_at = now();
        }

        $invoice->save();

        return $invoice;
    }

    public function recalculateTotals(Invoice $invoice): Invoice
    {
        $subtotal = $invoice->items()->sum('total_price');
        $taxAmount = 0;
        $totalAmount = $subtotal + $taxAmount;

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);

        return $invoice->fresh();
    }

    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            return Invoice::create($data);
        });
    }

    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);

        return $invoice;
    }

    public function findOrCreateDraftInvoice(int $patientId): Invoice
    {
        return DB::transaction(function () use ($patientId) {
            $existing = Invoice::where('patient_id', $patientId)
                ->where('status', InvoiceStatus::DRAFT)
                ->first();

            if ($existing) {
                return $existing;
            }

            return Invoice::create([
                'patient_id' => $patientId,
                'invoice_number' => $this->generateInvoiceNumber(),
                'invoice_date' => now()->format('Y-m-d'),
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
                'status' => InvoiceStatus::DRAFT,
            ]);
        });
    }

    public function addBillableItem(
        Invoice $invoice,
        string $itemType,
        ?int $itemId,
        string $description,
        float $unitPrice,
        int $quantity = 1,
    ): ?InvoiceItem {
        if ($itemId !== null) {
            $existing = $invoice->items()
                ->where('item_type', $itemType)
                ->where('item_id', $itemId)
                ->first();

            if ($existing) {
                return null;
            }
        }

        return $this->addItem($invoice, [
            'item_type' => $itemType,
            'item_id' => $itemId,
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
        ]);
    }
}
