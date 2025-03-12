<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends BaseModel
{
    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        /** @var string $tableName */
        $tableName = config('atlas.countries_tablename');

        return $tableName ?: parent::getTable();
    }

    /**
     * @return HasMany<State,covariant self>
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'country_id', 'id');
    }

    /**
     * @return HasMany<City,covariant self>
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_id', 'id');
    }

    /**
     * @return BelongsToMany<Timezone, covariant self>
     */
    public function timezones(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Timezone::class,
            table: config()->string('atlas.countries_timezones_pivot_tablename'),
            foreignPivotKey: 'country_id',
            relatedPivotKey: 'time_zone_name',
            parentKey: 'id',
            relatedKey: 'zone_name'
        );
    }

    /**
     * @return HasOne<Currency,covariant self>
     */
    public function currency(): HasOne
    {
        return $this->hasOne(Currency::class, 'country_id', 'id');
    }

    /**
     * @param  array<string,mixed> $jsonItem
     * @return array<string,mixed>
     */
    public static function fromJsonToDBRecord(array $jsonItem): array
    {
        return [
            'id'         => $jsonItem['id'],
            'iso2'       => $jsonItem['iso2'],
            'name'       => $jsonItem['name'],
            'phone_code' => $jsonItem['phone_code'],
            'currency'   => $jsonItem['currency'],
            'iso3'       => $jsonItem['iso3'],
            'native'     => $jsonItem['native'],
            'region'     => $jsonItem['region'],
            'subregion'  => $jsonItem['subregion'],
            'latitude'   => $jsonItem['latitude'],
            'longitude'  => $jsonItem['longitude'],
        ];
    }
}
