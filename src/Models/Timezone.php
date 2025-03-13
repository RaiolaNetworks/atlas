<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Timezone extends BaseModel
{
    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        return config()->string('atlas.timezones_tablename') ?: parent::getTable();
    }

    /**
     * @return BelongsToMany<Country,covariant self>
     */
    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Country::class,
            table: config()->string('atlas.country_timezone_pivot_tablename'),
            foreignPivotKey: 'time_zone_name',
            relatedPivotKey: 'country_id',
            parentKey: 'zone_name',
            relatedKey: 'id'
        );
    }

    /**
     * @param  array<string,mixed> $jsonItem
     * @return array<string,mixed>
     */
    public static function fromJsonToDBRecord(array $jsonItem): array
    {
        return [
            'zone_name'       => $jsonItem['zoneName'],
            'gmt_offset'      => $jsonItem['gmtOffset'],
            'gmt_offset_name' => $jsonItem['gmtOffsetName'],
            'tz_name'         => $jsonItem['tzName'],
        ];
    }
}
