<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Raiolanetworks\Atlas\Models\Language;

class LanguagesSeeder extends BaseSeeder
{
    public $signature = 'atlas:languages';

    public $description = 'Seeding of languages in the database';

    protected string $resourceKey = 'languages';

    protected string $model = Language::class;
}
