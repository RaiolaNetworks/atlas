<?php

declare(strict_types=1);

use Raiolanetworks\Atlas\Helpers\ResourcesManager;

/**
 * Guards the structural integrity of the state hierarchy: every populated
 * parent_id must point to a level-1 division in the same country and never
 * to itself. This protects the ES/FR/IT/BE/IE/LK/FJ/BA/GQ/KN mappings from
 * silent corruption on future regenerations of states.json.
 */
describe('State hierarchy data integrity', function () {
    it('has valid parent_id links (same country, level-1 parent, no self-reference)', function () {
        $states = json_decode(file_get_contents(ResourcesManager::getPackagePath('states')), true);

        $byId = [];

        foreach ($states as $state) {
            $byId[$state['id']] = $state;
        }

        $violations = [];

        foreach ($states as $state) {
            $parentId = $state['parent_id'] ?? null;

            if ($parentId === null) {
                continue;
            }

            $parent = $byId[$parentId] ?? null;

            if ($parent === null) {
                $violations[] = "#{$state['id']} -> missing parent {$parentId}";
            } elseif ($parentId === $state['id']) {
                $violations[] = "#{$state['id']} -> self-reference";
            } elseif ($parent['country_id'] !== $state['country_id']) {
                $violations[] = "#{$state['id']} -> parent in different country";
            } elseif (($parent['admin_level'] ?? null) !== 1) {
                $violations[] = "#{$state['id']} -> parent is not admin_level 1";
            }

            if (count($violations) >= 10) {
                break;
            }
        }

        expect($violations)->toBe([]);
    });

    it('has at least one child for every parent referenced by parent_id', function () {
        $states = json_decode(file_get_contents(ResourcesManager::getPackagePath('states')), true);

        $parents = [];

        foreach ($states as $state) {
            if (($state['parent_id'] ?? null) !== null) {
                $parents[$state['parent_id']] = true;
            }
        }

        expect($parents)->not->toBeEmpty();
    });
});
