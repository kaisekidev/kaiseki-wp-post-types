<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Kaiseki\Test\Unit\WordPress\PostType\TestDouble\DummyPostTypeProvider;
use Kaiseki\Test\Unit\WordPress\PostType\TestDouble\DummyTaxonomyProvider;
use Kaiseki\WordPress\PostType\Column;
use Kaiseki\WordPress\PostType\Columns;
use Kaiseki\WordPress\PostType\PostType;
use Kaiseki\WordPress\PostType\PostTypeRegistry;
use Kaiseki\WordPress\PostType\Taxonomy;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

use function has_action;
use function has_filter;

final class PostTypeRegistryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testAddHooksAddsRegisterCallbackToInit(): void
    {
        $registry = new PostTypeRegistry([], []);

        $registry->addHooks();

        self::assertSame(10, has_action('init', [$registry, 'register']));
    }

    public function testRegistersTaxonomiesThenPostTypesThenAssociations(): void
    {
        $calls = $this->captureRegistrationCalls();
        $registry = new PostTypeRegistry(
            [
                new DummyPostTypeProvider([
                    PostType::create('book', 'Book', 'Books')->withTaxonomies('category'),
                ]),
            ],
            [
                new DummyTaxonomyProvider([
                    Taxonomy::create('genre', 'Genre', 'Genres')->withPostTypes('book'),
                ]),
            ],
        );

        $registry->register();

        self::assertSame(
            [
                ['register_taxonomy', 'genre', ['book']],
                ['register_post_type', 'book'],
                ['register_taxonomy_for_object_type', 'genre', 'book'],
                ['register_taxonomy_for_object_type', 'category', 'book'],
            ],
            $calls(),
        );
    }

    public function testDefaultOptionsSitBetweenBaselineAndDefinition(): void
    {
        /** @var array<string, mixed> $postTypeArgs */
        $postTypeArgs = [];
        /** @var array<string, mixed> $taxonomyArgs */
        $taxonomyArgs = [];
        Functions\when('register_post_type')->alias(
            static function (string $name, array $args) use (&$postTypeArgs): void {
                $postTypeArgs = $args;
            }
        );
        Functions\when('register_taxonomy')->alias(
            static function (string $name, array $objectTypes, array $args) use (&$taxonomyArgs): void {
                $taxonomyArgs = $args;
            }
        );
        $registry = new PostTypeRegistry(
            [new DummyPostTypeProvider([PostType::create('book', 'Book', 'Books')->withMenuPosition(5)])],
            [new DummyTaxonomyProvider([Taxonomy::create('genre', 'Genre', 'Genres')])],
            ['show_in_rest' => true, 'menu_position' => 20],
            ['show_in_rest' => true, 'hierarchical' => false],
        );

        $registry->register();

        self::assertTrue($postTypeArgs['show_in_rest']);
        self::assertSame(5, $postTypeArgs['menu_position']);
        self::assertTrue($taxonomyArgs['show_in_rest']);
        self::assertFalse($taxonomyArgs['hierarchical']);
    }

    public function testColumnsAndTaxonomyFiltersAreWiredForPostType(): void
    {
        $this->captureRegistrationCalls();
        $registry = new PostTypeRegistry(
            [
                new DummyPostTypeProvider([
                    PostType::create('book', 'Book', 'Books')
                        ->withColumns(Columns::create()->with(Column::create('price', 'Price')))
                        ->withTaxonomyFilters('genre'),
                ]),
            ],
            [],
        );

        $registry->register();

        self::assertTrue((bool)has_filter('manage_book_posts_columns'));
        self::assertTrue((bool)has_action('manage_book_posts_custom_column'));
        self::assertTrue((bool)has_filter('manage_edit-book_sortable_columns'));
        self::assertTrue((bool)has_action('pre_get_posts'));
        self::assertTrue((bool)has_action('restrict_manage_posts'));
    }

    public function testNoColumnHooksWithoutColumns(): void
    {
        $this->captureRegistrationCalls();
        $registry = new PostTypeRegistry(
            [new DummyPostTypeProvider([PostType::create('book', 'Book', 'Books')])],
            [],
        );

        $registry->register();

        self::assertFalse((bool)has_filter('manage_book_posts_columns'));
        self::assertFalse((bool)has_action('restrict_manage_posts'));
    }

    /**
     * @return callable(): list<list<list<string>|string>>
     */
    private function captureRegistrationCalls(): callable
    {
        /** @var list<list<list<string>|string>> $calls */
        $calls = [];
        Functions\when('register_taxonomy')->alias(
            static function (string $name, array $objectTypes, array $args) use (&$calls): void {
                $calls[] = ['register_taxonomy', $name, $objectTypes];
            }
        );
        Functions\when('register_post_type')->alias(
            static function (string $name, array $args) use (&$calls): void {
                $calls[] = ['register_post_type', $name];
            }
        );
        Functions\when('register_taxonomy_for_object_type')->alias(
            static function (string $taxonomy, string $postType) use (&$calls): bool {
                $calls[] = ['register_taxonomy_for_object_type', $taxonomy, $postType];

                return true;
            }
        );

        return static function () use (&$calls): array {
            return $calls;
        };
    }

    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
