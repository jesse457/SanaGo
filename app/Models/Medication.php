<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use ParagonIE\CipherSweet\Transformation\Lowercase;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Medication extends Model implements CipherSweetEncrypted
{
    use BelongsToTenant, HasFactory,UsesCipherSweet;

    protected $fillable = [
        'name',
        'description',
        'stock_quantity',
        'min_stock_level',
        'unit_price_purchase',
    ];

    /**
     * Configure CipherSweet encryption for this model.
     *
     * Fields added with ->addField(...) should correspond to text columns
     * in your DB to store ciphertext. For exact-match searchable fields,
     * add a BlindIndex.
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            // first & last names - encrypted and searchable (exact match)
            ->addField('name')
            ->addBlindIndex('name', new BlindIndex('name_index', [
                new Lowercase, // Normalize to lowercase
            ]))
            ->addOptionalTextField('description');

        // transformations via BlindIndex options (see CipherSweet docs).
    }

    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
