# Get the data from the most recondite place with 'Atlas'

[![Latest Version on Packagist](https://img.shields.io/packagist/v/raiolanetworks/atlas.svg?style=flat-square)](https://packagist.org/packages/raiolanetworks/atlas)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/raiolanetworks/atlas/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/raiolanetworks/atlas/actions?query=workflow%3ATests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/raiolanetworks/atlas/pint.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/raiolanetworks/atlas/actions?query=workflow%3APint+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/raiolanetworks/atlas.svg?style=flat-square)](https://packagist.org/packages/raiolanetworks/atlas)

With 'Atlas' you will be able to create new tables in the database and fill them with information about countries, states, cities, timezones and more.

## Requirements

- PHP 8.3+
- Laravel 11+


## Get to know us

[<img src="https://cdn-assets.raiolanetworks.com/dist/images/logos/logo-blue.svg" width="419px" />](https://raiolanetworks.com)


## Installation

Install the package via Composer:
```bash
composer require raiolanetworks/atlas
```

Optionally publish the config file to customise table names or toggle entities:
```bash
php artisan vendor:publish --tag="atlas-config"
```

Run the migrations and seed the database:
```bash
php artisan atlas:install
```

The command will migrate the tables for every entity enabled in `config('atlas.entities')` (all enabled by default) and let you choose which seeders to run. The process may take a few minutes due to the large number of cities.

To re-seed the data after a package upgrade:
```bash
php artisan atlas:update
```

### Other publishable resources

```bash
php artisan vendor:publish --tag="atlas-translations"
php artisan vendor:publish --tag="atlas-jsons"        # JSON data files (for overriding)
```

> **Note:** Migrations are auto-loaded by the package. Do **not** publish them with `--tag="atlas-migrations"` unless you have a specific reason — published copies will cause "table already exists" errors.


## Usage

Internally, the package works with Laravel models, which allows you to work with this model as if they were models of your own project.

For example, if you want to get all the countries in Africa:

```php
use Raiolanetworks\Atlas\Models\Country;

class MyClass
{
	public function getAllAfricaCountries(): Collection
	{
		return Country::where('region_name', 'Africa')
			->orderBy('name')
			->get();
	}
}

```


## Upgrading

If you are upgrading from 1.x, please see [UPGRADE.md](./UPGRADE.md) for a list of breaking changes and migration steps.

## Changelog

Please see [CHANGELOG](./CHANGELOG.md) for more information on what has changed recently.


## Credits

- [Martín Gómez](https://github.com/soymgomez)
- [Víctor Escribano](https://github.com/victore13)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
