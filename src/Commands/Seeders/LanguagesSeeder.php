<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Illuminate\Console\Command;
use Raiolanetworks\Atlas\Enum\EntitiesEnum;
use Raiolanetworks\Atlas\Models\Language;

class LanguagesSeeder extends BaseSeeder
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'atlas:languages';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Seeding of languages in the database';

    protected string $resourceKey = 'languages';

    protected string $pluralName = '';

    protected string $model = Language::class;

    public function __construct()
    {
        parent::__construct();

        $this->pluralName = EntitiesEnum::Languages->value;
    }
}
