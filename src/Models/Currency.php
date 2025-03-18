<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Currency extends BaseModel
{
    protected $primaryKey = 'code';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        /** @var string $tableName */
        $tableName = config('atlas.currencies_tablename');

        return $tableName ?: parent::getTable();
    }

    /**
     * @return BelongsTo<Country,covariant self>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
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
