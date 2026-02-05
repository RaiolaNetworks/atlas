<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                  $id
 * @property string               $name
 * @property int                  $region_id
 * @property array<string,string> $translations
 * @property string               $wiki_data_id
 */
class Subregion extends BaseModel
{
    protected $fillable = [
        'name',
        'region_id',
        'translations',
        'wiki_data_id',
    ];

    protected $casts = [
        'translations' => 'array',
    ];

    public $timestamps = false;

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        /** @var string $tableName */
        $tableName = config('atlas.subregions_tablename');

        return $tableName ?: parent::getTable();
    }

    /**
     * @return HasMany<Country,covariant self>
     */
    public function countries(): HasMany
    {
        return $this->hasMany(Country::class);
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
            'translations' => json_encode($jsonItem['translations']),
            'wiki_data_id' => $jsonItem['wikiDataId'],
        ];

        if (config()->boolean('atlas.entities.regions')) {
            $parser['region_id'] = $jsonItem['region_id'];
        }

        return $parser;
    }
}
