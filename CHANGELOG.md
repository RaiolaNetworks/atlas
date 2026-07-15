# Changelog

All notable changes to `atlas` will be documented in this file.

## 3.0.0

Major release. **Drops Laravel 11** and requires CRLF-patched framework
versions, adds Laravel 13 support, ships the state hierarchy feature and
cleans up the shipped data.

### Removed

- **Dropped support for Laravel 11.** Its security support ended on 2026-03-12 and every 11.x release now carries unpatched security advisories, so it can no longer be installed via `composer update`. Requires `laravel/framework ^12.60 || ^13.10`.

### Added

- **Laravel 13 support** (`orchestra/testbench ^11`, Pest 4). CI now covers Laravel 12 and 13 on PHP 8.3 and 8.4.
- **State hierarchy**: `admin_level` and `parent_id` columns on the `states` table, `State::parent()` / `State::children()` relationships and `State::topLevel()` / `State::adminLevel(int $level)` query scopes.
- Ceuta and Melilla added to `cities.json` as city entries.

### Changed

- `states.json` enriched with `admin_level` and `parent_id` for all 5038 entries, sorted by country, then `admin_level`, then name. Run `php artisan atlas:states` to populate.
- `admin_level` assigned for the 95 countries with multiple administrative division types.
- `parent_id` populated for 10 countries (340 divisions): Spain (ES), France (FR), Italy (IT), Belgium (BE), Ireland (IE), Sri Lanka (LK), Fiji (FJ), Bosnia & Herzegovina (BA), Equatorial Guinea (GQ) and Saint Kitts & Nevis (KN).
- Ceuta and Melilla (Spain) reclassified from `admin_level: 1` to `admin_level: 2` to appear alongside provinces in address forms.
- Country `native` is now **required** (non-null). Existing null natives are backfilled from `name` via a dedicated migration; the seeder applies the same `native ?? name` fallback.

### Fixed

- **Security (GHSA-5vg9-5847-vvmq):** raised the framework floor so the package can no longer resolve a Laravel version affected by the CRLF injection advisory.
- Whitespace normalized in 874 name fields across countries/states/cities (leading/trailing spaces, stray tabs, double spaces), including denormalized `country_name` / `state_name` copies.
- Côte d'Ivoire `native` (was `null`) set to `"Côte d'Ivoire"` in `countries.json`.
- Corrected the `type` of Belgium's "Flanders" from `province` to `region`.

### Known limitations

- **Parent/child navigation is only populated for 10 countries** (340 divisions: ES, FR, IT, BE, IE, LK, FJ, BA, GQ, KN). Elsewhere `parent_id` is `null`, so `State::parent()` returns `null` and `State::children()` returns an empty collection. `admin_level` / `topLevel()` / `adminLevel()` still work for every country.
- **`topLevel()` does not guarantee unique names within a country.** A few countries have two co-equal first-level divisions sharing a name (e.g. Minsk oblast + Minsk city, Almaty region + Almaty city, Moscow oblast + Moscow city, Zagreb county + Zagreb city). Both correctly remain at `admin_level: 1`; disambiguate by `type` or `state_code` in the UI.

### Upgrade steps

1. Ensure your app runs on Laravel 12.60+ or 13.10+ (Laravel 11 is no longer supported).
2. Run `php artisan migrate` to add the new columns and enforce the non-null `native` (existing null natives are backfilled from `name`).
3. Run `php artisan atlas:states` to re-seed with hierarchical data.
4. Optionally run `php artisan atlas:cities` to seed the new Ceuta/Melilla city entries.
5. Use `State::where('country_id', $id)->topLevel()->get()` in dropdowns where you only want first-level divisions.

> ⚠️ **Heads-up for production:** `atlas:states` / `atlas:cities` are **destructive re-seeds** — they empty the table and re-insert every row inside a transaction, with foreign-key checks disabled during the run. Primary keys are preserved (they come from the JSON `id`), so existing foreign keys that reference states/cities stay valid, but **any local edits to those tables are overwritten**. Run it in a maintenance window.
