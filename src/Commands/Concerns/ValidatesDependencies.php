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

    /**
     * Return true (and print errors) when required dependencies are broken.
     */
    protected function hasBrokenDependencies(): bool
    {
        $errors = EntitiesEnum::validateDependencies();

        foreach ($errors as $error) {
            $this->error($error);
        }

        return count($errors) > 0;
    }
}
