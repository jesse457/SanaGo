<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A till: reception, laboratory, pharmacy, imaging.
 *
 * Each cash point opens its own shift and prints its own receipt series, while
 * every payment still lands in one ledger — the "many tills, one ledger"
 * model a hospital counter needs.
 */
#[Fillable(['name', 'code', 'module', 'receipt_prefix', 'is_active'])]
class CashPoint extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(CashShift::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CashPayment::class);
    }

    /**
     * The default till every installation starts with.
     */
    public static function primary(): self
    {
        return self::firstOrCreate(
            ['code' => 'main'],
            [
                'name' => 'Caisse principale',
                'receipt_prefix' => 'REC',
                'is_active' => true,
            ],
        );
    }

    /**
     * Invoice line types this till is allowed to collect.
     *
     * A dedicated counter takes money for its own module only: the laboratory
     * desk must not collect a surgery fee. The catch-all desk (`module` null)
     * is the hospital's main caisse and collects anything, drugs included, so a
     * patient never has to queue at a second till to pay one bill.
     *
     * @return array<int, string>|null null means "any line type"
     */
    public function collectableItemTypes(): ?array
    {
        return match ($this->module) {
            'pharmacy' => ['pharmacy', 'medication'],
            'laboratory' => ['lab_test', 'laboratory', 'lab'],
            'radiology' => ['radiology', 'imaging'],
            'consultation' => ['consultation', 'appointment'],
            default => null,
        };
    }

    /**
     * Whether this till may take money for that kind of charge.
     */
    public function canCollect(string $itemType): bool
    {
        $allowed = $this->collectableItemTypes();

        if ($allowed === null) {
            return true;
        }

        return in_array($itemType, $allowed, true);
    }

    /**
     * Active tills grouped for a picker: `id`, `name`, `module`.
     *
     * @return array<int, array{id: int, name: string, code: string, module: string|null}>
     */
    public static function options(): array
    {
        return self::where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(fn (self $point): array => [
                'id' => $point->id,
                'name' => $point->name,
                'code' => $point->code,
                'module' => $point->module,
            ])
            ->all();
    }
}
