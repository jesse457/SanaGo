<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class LabTestDefinition extends Model implements CipherSweetEncrypted
{
    use BelongsToTenant, HasFactory,UsesCipherSweet;

    protected $fillable = [
        'test_name',
        'description',
        'price',
        'test_code', // Example of a new field
        'normal_range', // Example of a new field
        'units', // Example of a new field

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
            ->addField('test_name')
            ->addBlindIndex('test_name', new BlindIndex('test_name_index'))
            ->addOptionalTextField('description');

        // If you want case-insensitive or transformed blind indexes, define
        // transformations via BlindIndex options (see CipherSweet docs).
    }

    public function labRequests()
    {
        return $this->hasMany(LabRequest::class);
    }
}
