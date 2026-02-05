# Upgrading to 2.x from 1.x

## Requirements

- **PHP 8.3+** (was 8.2+)
- **Laravel 11+** (Laravel 10 is no longer supported)
- New dependency `halaxa/json-machine` is installed automatically via Composer.

## High impact changes

### Relationship renames on `Country`

The following `BelongsTo` relationships were renamed to singular form to follow Laravel conventions:

```php
// Before
$country->regions;
$country->subregions;

// After
$country->region;
$country->subregion;
```

**Action:** Search your codebase for `->regions` and `->subregions` on Country instances (including eager loads, `with()`, `has()`, `whereHas()`) and rename to singular.

### `Currency::country()` changed to `Currency::countries()`

The relationship was corrected from `BelongsTo` to `HasMany`, since one currency can belong to multiple countries.

```php
// Before
$currency->country;   // returned a single Country (BelongsTo)

// After
$currency->countries; // returns a Collection of Country (HasMany)
```

**Action:** Replace `->country` with `->countries` on Currency instances. Update any code that assumed a single return value.

### `Country::currency()` changed from `HasOne` to `BelongsTo`

The foreign key now lives on the `countries` table (`currency_code` column referencing `currencies.code`) instead of the previous incorrect `HasOne` lookup.

```php
// Before — HasOne looked for currency.country_id = country.id
$country->currency;

// After — BelongsTo looks up currencies.code via country.currency_code
$country->currency;
```

The method name and return type (`Currency`) remain the same. This only breaks code that relied on the `HasOne` query structure (e.g., `$country->currency()->create(...)` or saving through the relationship).

**Action:** If you only used `$country->currency` for reading, no change is needed. If you used `HasOne`-specific methods on the relationship, update accordingly.

### Config key typo fixed: `country_timezon_pivot_tablename`

The config key was renamed from `country_timezon_pivot_tablename` to `country_timezone_pivot_tablename`.

A backwards-compatibility shim is included: if the old key is present in your published config, it will be automatically mapped to the new key at runtime. However, you should update your published `config/atlas.php`:

```php
// Before
'country_timezon_pivot_tablename' => env('ATLAS_COUNTRY_TIMEZONE', 'country_timezone'),

// After
'country_timezone_pivot_tablename' => env('ATLAS_COUNTRY_TIMEZONE', 'country_timezone'),
```

### Pivot column renamed: `time_zone_name` → `timezone_name`

The `country_timezone` pivot table column was renamed. An upgrade migration handles this automatically for existing databases. No action needed unless you query the pivot table directly:

```php
// Before
DB::table('country_timezone')->where('time_zone_name', $tz);

// After
DB::table('country_timezone')->where('timezone_name', $tz);
```

### Country columns renamed: `region` → `region_name`, `subregion` → `subregion_name`

The `region` and `subregion` string columns on the `countries` table were renamed to `region_name` and `subregion_name` to avoid conflicts with the `region()` and `subregion()` relationship methods. An upgrade migration handles this automatically.

```php
// Before
$country->getAttributes()['region'];    // string column
$country->getAttributes()['subregion']; // string column

// After
$country->region_name;    // string column
$country->subregion_name; // string column
```

**Action:** If you query these columns directly (e.g., `where('region', ...)` or `$country->region` expecting the string value), update to use `region_name` / `subregion_name`.

## Medium impact changes

### `Raiolanetworks\Atlas\Atlas` class removed

The duplicate Facade class at `src/Atlas.php` was removed. Use the canonical Facade instead:

```php
// Before
use Raiolanetworks\Atlas\Atlas;

// After
use Raiolanetworks\Atlas\Facades\Atlas;
```

### All models now use `$fillable` instead of `$guarded`

All models switched from `protected $guarded = []` to explicit `$fillable` arrays. If you were mass-assigning non-standard attributes via `create()` or `fill()`, those attributes will now be silently ignored.

**Action:** If you extended any Atlas model and relied on `$guarded = []` to mass-assign custom columns, override `$fillable` in your subclass to include your additional fields.

### `Currency` and `Timezone` models now declare string primary keys

Both models now correctly set `$incrementing = false` and `$keyType = 'string'` since their primary keys (`code` and `zone_name` respectively) are strings. This was already the intended behavior, but the Eloquent defaults were missing. If you relied on `Currency::find(1)` or `Timezone::find(1)` with integer IDs, this will no longer work.

### `region_id` column is now nullable

The `region_id` column on the `countries` table is now `NULLABLE` to match the `ON DELETE SET NULL` foreign key constraint. An upgrade migration handles this automatically.

### `Language` model now declares string primary key

The `Language` model now correctly sets `$primaryKey = 'code'`, `$incrementing = false`, and `$keyType = 'string'`. This allows `Language::find('en')` to work correctly.

### `Country::$translations` now cast to array

The `translations` attribute on the `Country` model is now cast to `array` (matching the existing behavior on `Region` and `Subregion`). If you were manually calling `json_decode()` on `$country->translations`, you can remove that call.

### `id` added to `$fillable` on all ID-based models

The `id` field was added to `$fillable` on `Country`, `City`, `State`, `Region`, and `Subregion` so that `fromJsonToDBRecord()` can mass-assign the JSON-sourced IDs during seeding.

### `Currency::$thousands_separator` removed from `$fillable`

The `thousands_separator` field was removed from `Currency::$fillable` since it is not present in the JSON source data. The migration column default (`,`) still applies.

## Low impact changes

### New `Subregion::region()` relationship

A `BelongsTo` relationship to `Region` was added to the `Subregion` model. This is purely additive and should not break existing code.

### `atlas:update` command now respects enabled entities

The `atlas:update` command only re-seeds entities that are enabled in `config('atlas.entities')` instead of always seeding all six entity types.

### Entity dependency validation

Both `atlas:install` and `atlas:update` now emit warnings when an entity is enabled but one of its dependencies is disabled (e.g., countries enabled but currencies disabled).

### Migrations converted to anonymous classes

All migrations now use anonymous classes (`return new class extends Migration`) instead of named classes. This is the Laravel default since version 9 and avoids class name collisions. The upgrade migration handles schema changes for existing databases.

### Translations loading enabled

Translation loading and publishing (`atlas-translations`) is now active. Previously it was commented out.

## Upgrade steps

1. Update your `composer.json` to require `"raiolanetworks/atlas": "^2.0"`.
2. Run `composer update raiolanetworks/atlas`.
3. Run `php artisan migrate` to apply the upgrade migration (renames pivot column, renames `region`/`subregion` columns, adds indexes, fixes `region_id` nullability).
4. If you published the config, update the `country_timezon_pivot_tablename` key to `country_timezone_pivot_tablename`.
5. Search for the renamed relationships and update your code:
   - `$country->regions` → `$country->region`
   - `$country->subregions` → `$country->subregion`
   - `$currency->country` → `$currency->countries`
6. Replace any imports of `Raiolanetworks\Atlas\Atlas` with `Raiolanetworks\Atlas\Facades\Atlas`.
7. If you query the pivot table directly, update `time_zone_name` references to `timezone_name`.
8. Update any direct references to `region`/`subregion` columns on countries to `region_name`/`subregion_name`.
