# kaiseki/wp-post-types

Build and register WordPress post types and taxonomies — a config-driven wrapper around `jjgrainger/posttypes`.

Provides `PostTypeBuilder` / `TaxonomyBuilder` (and their interfaces) that create `jjgrainger/posttypes`
`PostType` / `Taxonomy` objects, applying a shared set of default options from config so all your post
types and taxonomies start from the same baseline. Wired through `ConfigProvider`.

## Installation

```bash
composer require kaiseki/wp-post-types
```

Requires PHP 8.2 or newer.

## Usage

Register `ConfigProvider` with your laminas-style config aggregator to set the default options applied
to every built post type / taxonomy:

```php
return [
    'post_type' => [
        // Defaults merged into every PostType built (register_post_type() args).
        'default_post_type_options' => [
            'supports' => ['title', 'editor', 'thumbnail'],
            'has_archive' => true,
        ],
        // Defaults merged into every Taxonomy built (register_taxonomy() args).
        'default_taxonomy_options' => [
            'hierarchical' => true,
        ],
    ],
];
```

Resolve a builder from the container and build/register your types:

```php
use Kaiseki\WordPress\PostType\PostType\PostTypeBuilderInterface;

$builder = $container->get(PostTypeBuilderInterface::class);
$book = $builder->build(['book', 'books'], ['menu_icon' => 'dashicons-book'], ['singular' => 'Book']);
$book->register();
```

`PostTypeHelper` / `TaxonomyHelper` provide convenience methods for associating taxonomies with post
types. Per-build `options` are merged over the configured defaults.

## Development

```bash
composer install
composer check   # check-deps, cs-check, phpstan
```

## License

MIT — see [LICENSE](LICENSE).
