<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * Prepares the data with the actual values before storing in the database
     *
     * @param  array<string,mixed> $jsonItem
     * @return array<string,mixed>
     */
    abstract public static function fromJsonToDBRecord(array $jsonItem): array;
}
