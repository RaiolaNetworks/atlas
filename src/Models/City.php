<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends BaseModel
{
    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        /** @var string $tableName */
        $tableName = config('atlas.cities_tablename');

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
     * @return BelongsTo<State,covariant self>
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
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
            'state_id'     => $jsonItem['state_id'],
            'name'         => $jsonItem['name'],
            'country_code' => $jsonItem['country_code'],
            'state_code'   => $jsonItem['state_code'],
            'latitude'     => $jsonItem['latitude'],
            'longitude'    => $jsonItem['longitude'],
        ];
    }
}
