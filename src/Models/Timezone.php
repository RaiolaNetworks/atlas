<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timezone extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        /** @var string $tableName */
        $tableName = config('atlas.timezones_tablename');

        return $tableName ?: parent::getTable();
    }

    /**
     * @return BelongsTo<Country,covariant self>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
