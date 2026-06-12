# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## Unreleased

### Changed

- **First-party registration — `jjgrainger/posttypes` removed.** Post types and taxonomies are now
  declared as immutable `PostType` / `Taxonomy` value objects (static `create(name, singular, plural,
  ?slug)` plus clone-based `with*()` withers and a `withOptions()` escape hatch) returned from
  `PostTypeProviderInterface` / `TaxonomyProviderInterface` classes listed under the new
  `post_type.post_types` / `post_type.taxonomies` config keys. A single `PostTypeRegistry`
  (`kaiseki/wp-hook` hook provider) registers everything in one `init` callback — all taxonomies, then
  all post types, then every taxonomy↔post-type association declared on either side. This ordering is
  a public contract.
- Label auto-generation reproduces the `jjgrainger/posttypes` 2.x output (13 post-type / 17 taxonomy
  labels, "Seperate" typo fixed); `withLabels()` merges over the generated set, and a `labels` entry
  passed through `withOptions()` replaces it (call order between the two is preserved). Labels stay
  English by design — translate per definition.
- `default_post_type_options` / `default_taxonomy_options` are applied by the registry between the
  package baseline and each definition's own values (a `labels` entry in the defaults merges between
  the generated set and `withLabels()`).
- Admin list table columns are typed: `Columns::create()->without(...)->with(Column::create(...))` with
  renderers that **return** the cell markup instead of echoing; sortable supports meta and numeric
  ordering and only rewrites the main admin list query (posttypes 2.x also touched secondary queries).
  Attached via `PostType::withColumns()`.
- Taxonomy filter dropdowns on the admin list table are now **opt-in** via `withTaxonomyFilters()`
  (posttypes 2.x derived them implicitly from the assigned taxonomies).
- Rewrite args replace wholesale; only the slug default is injected when a rewrite array has no `slug`
  (posttypes 2.x merged recursively).

### Removed

- `PostTypeBuilder` / `TaxonomyBuilder` (and interfaces/factories) and `PostTypeHelper` /
  `TaxonomyHelper` — the wrapper API around `jjgrainger/posttypes` is gone, together with the
  dependency and its name-inflection magic (all consumers pass explicit singular/plural/slug).
  Consumers stay on `^1.0` until they migrate to the provider API.

### Added

- Unit test suite (PHPUnit 11 + Brain Monkey): registration call args and ordering, label parity with
  posttypes 2.x, value-object merge precedence and immutability, column and filter hook wiring,
  `ConfigProvider` / registry factory.

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
