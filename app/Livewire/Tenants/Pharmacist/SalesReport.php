<?php

namespace App\Livewire\Tenants\Pharmacist;

use App\Models\Dispensation;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination; // If you want pagination for a larger dataset
use Symfony\Component\HttpFoundation\StreamedResponse; // For file downloads

#[Layout('components.layouts.pharmacist')]
class SalesReport extends Component
{
    // public $topSellingMedications; // No longer needed as a public property if using computed property
    public $search = '';

    // If you want pagination, uncomment this and use it in your render method
    // use WithPagination;

    // A computed property for top selling medications, allowing for search
    public function getTopSellingMedicationsProperty()
    {
        $query = Dispensation::selectRaw('medications.name, SUM(dispensations.quantity_issued) as total_quantity_sold')
            ->join('prescription_items', 'dispensations.prescription_item_id', '=', 'prescription_items.id')
            ->join('medications', 'prescription_items.medication_id', '=', 'medications.id')
            ->groupBy('medications.name')
            ->orderByDesc('total_quantity_sold')
            ->limit(10); // Still limiting to 10 for "top selling"

        if ($this->search) {
            $query->where('medications.name', 'like', "%{$this->search}%");
        }

        return $query->get();
    }

    public function downloadCsv()
    {
        $filename = 'sales_report_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Rank', 'Medication Name', 'Quantity Sold']);

            $medications = $this->getTopSellingMedicationsProperty(); // Use the computed property
            foreach ($medications as $index => $med) {
                fputcsv($file, [$index + 1, $med->name, $med->total_quantity_sold.' units']);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function downloadExcel()
    {
        // For actual Excel (XLSX) generation, you'd typically use a library like PhpSpreadsheet.
        // This example provides a basic CSV disguised as an Excel file, which most spreadsheet programs can open.
        // For a full Excel solution, you would need to integrate a dedicated library.

        $filename = 'sales_report_'.now()->format('Ymd_His').'.xlsx'; // Using .xlsx extension

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Proper MIME type for .xlsx
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function () {
            // For a true XLSX, you'd use PhpSpreadsheet here.
            // This is a simplified example that generates a CSV-like structure
            // which Excel can open.
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Rank', 'Medication Name', 'Quantity Sold']);

            $medications = $this->getTopSellingMedicationsProperty(); // Use the computed property
            foreach ($medications as $index => $med) {
                fputcsv($file, [$index + 1, $med->name, $med->total_quantity_sold.' units']);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.tenants.pharmacist.sales-report', [
            'topSellingMedications' => $this->getTopSellingMedicationsProperty(), // Pass computed property to view
        ]);
    }
}
