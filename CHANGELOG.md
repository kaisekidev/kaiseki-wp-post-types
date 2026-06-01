# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.0.0 - 2026-06-01

First tagged release.

### Added

- `PostTypeBuilder` / `TaxonomyBuilder` (with interfaces) and `PostTypeHelper` / `TaxonomyHelper` —
  config-driven builders around `jjgrainger/posttypes` that apply shared `default_post_type_options` /
  `default_taxonomy_options` to every built type. Wired by `ConfigProvider` from the `post_type`
  config key.

### Changed

- PHP requirement is `^8.2` (PHP 8.4 is the primary target).
- Modernized the dev toolchain (PHPStan 2, PHPUnit 11 schema, composer-require-checker 4) and depends
  on `kaiseki/php-coding-standard: ^1.0` with the shared PHPStan config; `kaiseki/config` pinned to
  `^2.0`. CI now runs via the reusable workflow in `kaisekidev/.github`.
- **`jjgrainger/posttypes` kept at `^2.2`** (2.2.2 supports PHP `>=7.2`, so it runs on 8.2–8.4). Its
  `3.0` line is a ground-up redesign (`PostType`/`Taxonomy` became abstract base classes you extend,
  dropping the `new PostType($names, $options, $labels)` builder API), which would require a redesign
  of this package's public API and its consumers — tracked as a separate follow-up.

### Fixed

- PHPStan 2 (level max): the builder factories narrow the config options to string keys
  (`array_filter(..., ARRAY_FILTER_USE_KEY)`) so `Config::array()`'s `array<array-key, mixed>` matches
  the builders' `array<string, mixed>` option type. No behaviour change.
