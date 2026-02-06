<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Models;

/**
 * @property string $code
 * @property string $name
 * @property string $name_native
 * @property string $dir
 */
class Language extends BaseModel
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'name_native',
        'dir',
    ];

    public $timestamps = false;

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        $table = config('atlas.languages_tablename');

        return is_string($table) && $table !== '' ? $table : parent::getTable();
    }

    /**
     * @param  array<string,mixed> $jsonItem
     * @return array<string,mixed>
     */
    public static function fromJsonToDBRecord(array $jsonItem): array
    {
        return [
            'code'        => $jsonItem['code'],
            'name'        => $jsonItem['name'],
            'name_native' => $jsonItem['name_native'],
            'dir'         => $jsonItem['dir'],
        ];
    }
}
