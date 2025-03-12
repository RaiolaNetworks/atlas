<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends BaseModel
{
    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        /** @var string $tableName */
        $tableName = config('atlas.states_tablename');

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
     * @return HasMany<City, covariant self>
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * @param  array<string,mixed> $jsonItem
     * @return array<string,mixed>
     */
    public static function fromJsonToDBRecord(array $jsonItem): array
    {
        return [
            'id'           => $jsonItem['id'],
            'country_id'   => $jsonItem['country_id'],
            'name'         => $jsonItem['name'],
            'country_code' => $jsonItem['country_code'],
            'state_code'   => $jsonItem['state_code'],
            'type'         => $jsonItem['type'],
            'latitude'     => $jsonItem['latitude'],
            'longitude'    => $jsonItem['longitude'],
        ];
    }
}
