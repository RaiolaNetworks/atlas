<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $code
 * @property string $name
 * @property string $symbol
 * @property string $symbol_native
 * @property int    $decimal_digits
 * @property string $thousands_separator
 */
class Currency extends BaseModel
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'symbol_native',
        'decimal_digits',
        'thousands_separator',
    ];

    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'decimal_digits' => 'integer',
        ];
    }

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        $table = config('atlas.currencies_tablename');

        return is_string($table) && $table !== '' ? $table : parent::getTable();
    }

    /**
     * @return HasMany<Country,covariant self>
     */
    public function countries(): HasMany
    {
        return $this->hasMany(Country::class, 'currency_code', 'code');
    }

    /**
     * @param  array<string,mixed> $jsonItem
     * @return array<string,mixed>
     */
    public static function fromJsonToDBRecord(array $jsonItem): array
    {
        return [
            'code'           => $jsonItem['code'],
            'name'           => $jsonItem['name'],
            'symbol'         => $jsonItem['symbol'],
            'symbol_native'  => $jsonItem['symbol_native'],
            'decimal_digits' => $jsonItem['decimal_digits'],
        ];
    }
}
