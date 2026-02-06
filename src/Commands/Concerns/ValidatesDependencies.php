<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Concerns;

use Raiolanetworks\Atlas\Enum\EntitiesEnum;

trait ValidatesDependencies
{
    protected function warnAboutDisabledDependencies(): void
    {
        foreach (EntitiesEnum::validateDependencies() as $warning) {
            $this->warn($warning);
        }
    }
}
