<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

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

    protected string $dataPath = __DIR__ . '/../../resources/json/languages.json';

    protected string $overridedResourcesDataPath = 'json/languages.json';

    protected string $pluralName = '';

    protected string $model = Language::class;

    public function __construct()
    {
        parent::__construct();

        $this->pluralName = EntitiesEnum::Languages->value;
    }

    protected function parseItem(array $rawItem, array &$bulk): void
    {
        $bulk[] = [
            'code'        => $rawItem['code'],
            'name'        => $rawItem['name'],
            'name_native' => $rawItem['name_native'],
            'dir'         => $rawItem['dir'],
        ];
    }
}
