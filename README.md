# kaiseki/wp-post-types

Declare and register WordPress post types and taxonomies — immutable definitions, config-driven, first-party.

Post types and taxonomies are declared as immutable `PostType` / `Taxonomy` value objects returned from
provider classes. A single `PostTypeRegistry` (a `kaiseki/wp-hook` hook provider) registers everything on
`init` in a fixed order: all taxonomies, then all post types, then every taxonomy↔post-type association
declared on either side. Labels are auto-generated from the singular/plural names; shared defaults come
from config. No third-party registration library.

## Installation

```bash
composer require kaiseki/wp-post-types
```

Requires PHP 8.2 or newer.

## Usage

Declare post types and taxonomies in provider classes:

```php
use Kaiseki\WordPress\PostType\PostType;
use Kaiseki\WordPress\PostType\PostTypeProviderInterface;

final class DoorPostType implements PostTypeProviderInterface
{
    public const NAME = 'advcal_door';

    public function getPostTypes(): array
    {
        return [
            PostType::create(self::NAME, 'Door', 'Doors', 'cd')
                ->withShowInMenu(false)
                ->withSupports('title', 'editor', 'thumbnail')
                ->withMenuIcon('dashicons-calendar-alt')
                ->withTaxonomies('category', 'post_tag'),
        ];
    }
}
```

```php
use Kaiseki\WordPress\PostType\Taxonomy;
use Kaiseki\WordPress\PostType\TaxonomyProviderInterface;

final class TopicTaxonomy implements TaxonomyProviderInterface
{
    public function getTaxonomies(): array
    {
        return [
            Taxonomy::create('topic', 'Topic', 'Topics')
                ->withPostTypes('post', DoorPostType::NAME),
        ];
    }
}
```

Register `ConfigProvider` with your laminas-style config aggregator and list the providers:

```php
return [
    'post_type' => [
        'post_types' => [DoorPostType::class],
        'taxonomies' => [TopicTaxonomy::class],
        // Defaults applied to every definition (between the package baseline and the definition).
        'default_post_type_options' => ['show_in_rest' => true],
        'default_taxonomy_options' => [],
    ],
];
```

Typed withers cover the common `register_post_type()` / `register_taxonomy()` args; `withOptions([...])`
is the escape hatch for the rest (same precedence — last call wins). `withLabels([...])` merges over the
auto-generated English label set, which is how definitions are translated.

### Admin list table columns

```php
use Kaiseki\WordPress\PostType\Column;
use Kaiseki\WordPress\PostType\Columns;

PostType::create('book', 'Book', 'Books')
    ->withColumns(
        Columns::create()
            ->without('date', 'author')
            ->with(
                Column::create('price', 'Price')
                    ->withRenderer(fn(int $postId): string => esc_html((string) get_post_meta($postId, 'price', true)))
                    ->withSortable('price', isMeta: true, numeric: true),
            ),
    );
```

Renderers return the cell markup (escaping included) — the package echoes it.

### Taxonomy filter dropdowns

Dropdown filters on the admin list table are opt-in per post type:

```php
PostType::create('book', 'Book', 'Books')
    ->withTaxonomies('genre')
    ->withTaxonomyFilters('genre');
```

## Development

```bash
composer install
composer check   # check-deps, cs-check, phpstan
composer phpunit
```

## License

MIT — see [LICENSE](LICENSE).
