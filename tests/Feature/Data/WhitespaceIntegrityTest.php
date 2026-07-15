<?php

declare(strict_types=1);

use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;
use Raiolanetworks\Atlas\Helpers\ResourcesManager;

/**
 * Guards the invariant that name-like fields in the shipped JSON data carry
 * no leading/trailing whitespace, no internal tabs/newlines and no collapsible
 * double spaces. Files are streamed so cities.json (~150k rows) stays cheap.
 */
describe('JSON data whitespace integrity', function () {
    $resources = [
        'countries' => ['name', 'native', 'capital', 'nationality'],
        'states'    => ['name', 'country_name'],
        'cities'    => ['name', 'state_name', 'country_name'],
    ];

    foreach ($resources as $resource => $fields) {
        it("has normalized whitespace in {$resource} name fields", function () use ($resource, $fields) {
            $items = Items::fromFile(
                ResourcesManager::getPackagePath($resource),
                ['decoder' => new ExtJsonDecoder(true)]
            );

            $offenders = [];

            foreach ($items as $item) {
                foreach ($fields as $field) {
                    $value = $item[$field] ?? null;

                    if (! is_string($value)) {
                        continue;
                    }

                    $normalized = trim((string) preg_replace('/\s+/u', ' ', $value));

                    if ($normalized !== $value) {
                        $offenders[] = "{$resource}.{$field}=" . json_encode($value);

                        if (count($offenders) >= 10) {
                            break 2;
                        }
                    }
                }
            }

            expect($offenders)->toBe([]);
        });
    }
});
