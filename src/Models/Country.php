<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    protected $fillable = [
        'name',
        'iso2',
        'iso3',
        'numeric_code',
        'phonecode',
        'capital',
        'currency_code',
        'tld',
        'native',
        'region',
        'region_id',
        'subregion',
        'subregion_id',
        'nationality',
        'translations',
        'latitude',
        'longitude',
        'emoji',
        'emojiU',
    ];

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
     * @return BelongsTo<Region,covariant self>
     */
    public function regions(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * @return BelongsTo<Subregion,covariant self>
     */
    public function subregions(): BelongsTo
    {
        return $this->belongsTo(Subregion::class);
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
            table: config()->string('atlas.country_timezone_pivot_tablename'),
            foreignPivotKey: 'country_id',
            relatedPivotKey: 'timezone_name',
            parentKey: 'id',
            relatedKey: 'zone_name'
        );
    }

    /**
     * @return BelongsTo<Currency,covariant self>
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    /**
     * @param  array<string,mixed> $jsonItem
     * @return array<string,mixed>
     */
    public static function fromJsonToDBRecord(array $jsonItem): array
    {
        $parser = [
            'name'         => $jsonItem['name'],
            'iso2'         => $jsonItem['iso2'],
            'iso3'         => $jsonItem['iso3'],
            'numeric_code' => $jsonItem['numeric_code'],
            'phonecode'    => $jsonItem['phonecode'],
            'capital'      => $jsonItem['capital'],
            'tld'          => $jsonItem['tld'],
            'native'       => $jsonItem['native'],
            'region'       => $jsonItem['region'],
            'subregion'    => $jsonItem['subregion'],
            'nationality'  => $jsonItem['nationality'],
            'translations' => json_encode($jsonItem['translations']),
            'latitude'     => $jsonItem['latitude'],
            'longitude'    => $jsonItem['longitude'],
            'emoji'        => $jsonItem['emoji'],
            'emojiU'       => $jsonItem['emojiU'],
        ];

        if (config()->boolean('atlas.entities.regions')) {
            $parser['region_id'] = $jsonItem['region_id'];
        }

        if (config()->boolean('atlas.entities.subregions')) {
            $parser['subregion_id'] = $jsonItem['subregion_id'];
        }

        if (config()->boolean('atlas.entities.currencies')) {
            $parser['currency_code'] = $jsonItem['currency'];
        }

        return $parser;
    }
}
