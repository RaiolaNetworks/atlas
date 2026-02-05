<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $id
 * @property string $name
 * @property int    $state_id
 * @property string $state_code
 * @property string $state_name
 * @property int    $country_id
 * @property string $country_code
 * @property string $country_name
 * @property string $latitude
 * @property string $longitude
 * @property string $wiki_data_id
 */
class City extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
        'state_id',
        'state_code',
        'state_name',
        'country_id',
        'country_code',
        'country_name',
        'latitude',
        'longitude',
        'wiki_data_id',
    ];

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
        $parser = [
            'id'           => $jsonItem['id'],
            'name'         => $jsonItem['name'],
            'state_code'   => $jsonItem['state_code'],
            'state_name'   => $jsonItem['state_name'],
            'country_code' => $jsonItem['country_code'],
            'country_name' => $jsonItem['country_name'],
            'latitude'     => $jsonItem['latitude'],
            'longitude'    => $jsonItem['longitude'],
            'wiki_data_id' => $jsonItem['wikiDataId'],
        ];

        if (config()->boolean('atlas.entities.states')) {
            $parser['state_id'] = $jsonItem['state_id'];
        }

        if (config()->boolean('atlas.entities.countries')) {
            $parser['country_id'] = $jsonItem['country_id'];
        }

        return $parser;
    }
}
