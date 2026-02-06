<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                  $id
 * @property string               $name
 * @property array<string,string> $translations
 * @property string               $wiki_data_id
 */
class Region extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
        'translations',
        'wiki_data_id',
    ];

    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'translations' => 'array',
        ];
    }

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        $table = config('atlas.regions_tablename');

        return is_string($table) && $table !== '' ? $table : parent::getTable();
    }

    /**
     * @return HasMany<Subregion,covariant self>
     */
    public function subregions(): HasMany
    {
        return $this->hasMany(Subregion::class);
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
        return [
            'id'           => $jsonItem['id'],
            'name'         => $jsonItem['name'],
            'translations' => json_encode($jsonItem['translations']),
            'wiki_data_id' => $jsonItem['wikiDataId'],
        ];
    }
}
