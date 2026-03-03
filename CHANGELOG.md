# Changelog

All notable changes to `atlas` will be documented in this file.

## 2.1.0

### Added

- `admin_level` column on `states` table — integer indicating hierarchical depth (1 = top-level, 2 = subdivision). Allows filtering states by administrative level for cleaner dropdowns.
- `parent_id` column on `states` table — nullable self-referential foreign key enabling tree navigation between administrative levels (e.g. autonomous community → province).
- `State::parent()` and `State::children()` Eloquent relationships for hierarchical traversal.
- `State::topLevel()` and `State::adminLevel(int $level)` query scopes for convenient filtering.
- Ceuta and Melilla added to `cities.json` as city entries.

### Changed

- `states.json` enriched with `admin_level` and `parent_id` fields for all 5038 entries. Run `php artisan atlas:states` to populate the new data.
- States are now sorted by country, then by `admin_level`, then by name.
- `admin_level` assigned for all 95 countries with multiple administrative division types.
- `parent_id` populated for Spain (ES), France (FR), Italy (IT) and Belgium (BE).
- Ceuta and Melilla (Spain) reclassified from `admin_level: 1` to `admin_level: 2` to appear alongside provinces in address forms.

### Upgrade steps

1. Run `php artisan migrate` to add the new columns (defaults ensure existing data remains valid).
2. Run `php artisan atlas:states` to re-seed with hierarchical data.
3. Optionally run `php artisan atlas:cities` to seed the new Ceuta/Melilla city entries.
4. Use `State::where('country_id', $id)->topLevel()->get()` in dropdowns where you only want first-level divisions.
