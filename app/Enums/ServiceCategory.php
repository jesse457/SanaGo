<?php

namespace App\Enums;

enum ServiceCategory: string
{
    case Consultation = 'consultation';
    case Procedure = 'procedure';
    case Lab = 'lab';
    case Radiology = 'radiology';
    case Imaging = 'imaging';
    case Medication = 'medication';
    case Bed = 'bed';
    case Nursing = 'nursing';
    case Emergency = 'emergency';
    case Other = 'other';

    /**
     * The modules the price list and the cash desk work with. Every category
     * belongs to exactly one of these, so the counter picks a module first and
     * the services of that module next.
     *
     * @var array<string, string>
     */
    public const MODULES = [
        'consultation' => 'Consultation',
        'laboratory' => 'Lab Test',
        'radiology' => 'Radiology',
        'pharmacy' => 'Drugs',
        'admission' => 'Admission',
        'surgery' => 'Surgery',
        'other' => 'Other',
    ];

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function modules(): array
    {
        return array_map(
            fn (string $value, string $label): array => ['value' => $value, 'label' => $label],
            array_keys(self::MODULES),
            self::MODULES,
        );
    }

    /**
     * The billing module this catalog category belongs to.
     */
    public function module(): string
    {
        return match ($this) {
            self::Consultation => 'consultation',
            self::Lab => 'laboratory',
            self::Radiology, self::Imaging => 'radiology',
            self::Medication => 'pharmacy',
            self::Bed => 'admission',
            self::Procedure => 'surgery',
            default => 'other',
        };
    }
}
