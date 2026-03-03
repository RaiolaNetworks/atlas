<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int         $id
 * @property string      $name
 * @property int         $country_id
 * @property string      $country_code
 * @property string      $country_name
 * @property string      $state_code
 * @property string|null $type
 * @property int         $admin_level
 * @property int|null    $parent_id
 * @property string      $latitude
 * @property string      $longitude
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
        'admin_level',
        'parent_id',
        'latitude',
        'longitude',
    ];

    public $timestamps = false;

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        $table = config('atlas.states_tablename');

        return is_string($table) && $table !== '' ? $table : parent::getTable();
    }

    /**
     * @return BelongsTo<Country,covariant self>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsTo<self,covariant self>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<self, covariant self>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return HasMany<City, covariant self>
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Scope to top-level administrative divisions (admin_level = 1).
     *
     * @param  Builder<self> $query
     * @return Builder<self>
     */
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->where('admin_level', 1);
    }

    /**
     * Scope to a specific administrative level.
     *
     * @param  Builder<self> $query
     * @return Builder<self>
     */
    public function scopeAdminLevel(Builder $query, int $level): Builder
    {
        return $query->where('admin_level', $level);
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
            'admin_level'  => $jsonItem['admin_level'] ?? 1,
            'parent_id'    => $jsonItem['parent_id'] ?? null,
            'latitude'     => $jsonItem['latitude'],
            'longitude'    => $jsonItem['longitude'],
        ];

        if (config()->boolean('atlas.entities.countries')) {
            $parser['country_id'] = $jsonItem['country_id'];
        }

        return $parser;
    }
}
