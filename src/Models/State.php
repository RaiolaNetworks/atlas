<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int    $id
 * @property string $name
 * @property int    $country_id
 * @property string $country_code
 * @property string $country_name
 * @property string $state_code
 * @property string $type
 * @property string $latitude
 * @property string $longitude
 */
class State extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
        'country_id',
        'country_code',
        'country_name',
        'state_code',
        'type',
        'latitude',
        'longitude',
    ];

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
        $parser = [
            'id'           => $jsonItem['id'],
            'name'         => $jsonItem['name'],
            'country_code' => $jsonItem['country_code'],
            'country_name' => $jsonItem['country_name'],
            'state_code'   => $jsonItem['state_code'],
            'type'         => $jsonItem['type'],
            'latitude'     => $jsonItem['latitude'],
            'longitude'    => $jsonItem['longitude'],
        ];

        if (config()->boolean('atlas.entities.countries')) {
            $parser['country_id'] = $jsonItem['country_id'];
        }

        return $parser;
    }
}
