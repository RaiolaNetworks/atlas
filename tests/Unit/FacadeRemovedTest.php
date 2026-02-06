<?php

declare(strict_types=1);

it('Atlas facade class does not exist', function () {
    expect(class_exists('Raiolanetworks\Atlas\Facades\Atlas'))->toBeFalse();
});

it('Atlas service class does not exist', function () {
    expect(class_exists('Raiolanetworks\Atlas\Atlas'))->toBeFalse();
});
