<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $zone_name
 * @property int    $gmt_offset
 * @property string $gmt_offset_name
 * @property string $tz_name
 */
class Timezone extends BaseModel
{
    protected $fillable = [
        'zone_name',
        'gmt_offset',
        'gmt_offset_name',
        'tz_name',
    ];

    protected $primaryKey = 'zone_name';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gmt_offset' => 'integer',
        ];
    }

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        $table = config('atlas.timezones_tablename');

        return is_string($table) && $table !== '' ? $table : parent::getTable();
    }

    /**
     * @return BelongsToMany<Country,covariant self>
     */
    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Country::class,
            table: config()->string('atlas.country_timezone_pivot_tablename'),
            foreignPivotKey: 'timezone_name',
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
