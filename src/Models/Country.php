<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int                   $id
 * @property string                $name
 * @property string                $iso2
 * @property string                $iso3
 * @property string                $numeric_code
 * @property string                $phonecode
 * @property string                $capital
 * @property string                $currency_id
 * @property string                $tld
 * @property string                $native
 * @property string                $region
 * @property int                   $region_id
 * @property string                $subregion
 * @property int                   $subregion_id
 * @property string                $nationality
 * @property array<string, string> $translations
 * @property string                $latitude
 * @property string                $longitude
 * @property string                $emoji
 * @property string                $emojiU
 */
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
            table: config()->string('atlas.country_timezon_pivot_tablename'),
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
            'name'          => $jsonItem['name'],
            'iso2'          => $jsonItem['iso2'],
            'iso3'          => $jsonItem['iso3'],
            'numeric_code'  => $jsonItem['numeric_code'],
            'phonecode'     => $jsonItem['phonecode'],
            'capital'       => $jsonItem['capital'],
            'currency_code' => $jsonItem['currency'],
            'tld'           => $jsonItem['tld'],
            'native'        => $jsonItem['native'],
            'region'        => $jsonItem['region'],
            // 'region_id'    => $jsonItem['region_id'],
            'subregion'     => $jsonItem['subregion'],
            // 'subregion_id' => $jsonItem['subregion_id'],
            'nationality'   => $jsonItem['nationality'],
            'translations'  => json_encode($jsonItem['translations']),
            'latitude'      => $jsonItem['latitude'],
            'longitude'     => $jsonItem['longitude'],
            'emoji'         => $jsonItem['emoji'],
            'emojiU'        => $jsonItem['emojiU'],
        ];
    }
}
