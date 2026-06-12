<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Kaiseki\WordPress\PostType\TaxonomyFilterRegistrar;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

use function has_action;

final class TaxonomyFilterRegistrarTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testAddHooksWiresRestrictManagePosts(): void
    {
        $registrar = new TaxonomyFilterRegistrar('book', ['genre']);

        $registrar->addHooks();

        self::assertSame(10, has_action('restrict_manage_posts', [$registrar, 'renderFilters']));
    }

    public function testRenderFiltersIgnoresOtherPostTypes(): void
    {
        $registrar = new TaxonomyFilterRegistrar('book', ['genre']);

        // No WordPress functions are stubbed: reaching past the guard would error.
        $registrar->renderFilters('page');

        $this->expectOutputString('');
    }

    public function testRenderFiltersSkipsUnknownAndUnattachedTaxonomies(): void
    {
        Functions\when('taxonomy_exists')->alias(static fn(string $taxonomy): bool => $taxonomy !== 'missing');
        Functions\when('is_object_in_taxonomy')->alias(
            static fn(string $postType, string $taxonomy): bool => $taxonomy !== 'unattached'
        );
        Functions\expect('get_taxonomy')->never();
        $registrar = new TaxonomyFilterRegistrar('book', ['missing', 'unattached']);

        $registrar->renderFilters('book');
    }

    public function testRenderFiltersRendersDropdownWithSelection(): void
    {
        Functions\when('taxonomy_exists')->justReturn(true);
        Functions\when('is_object_in_taxonomy')->justReturn(true);
        Functions\when('get_taxonomy')->justReturn((object)[
            'name' => 'genre',
            'hierarchical' => true,
            'labels' => (object)['all_items' => 'All Genres'],
        ]);
        Functions\when('sanitize_title')->returnArg();
        /** @var array<string, mixed> $dropdownArgs */
        $dropdownArgs = [];
        Functions\when('wp_dropdown_categories')->alias(
            static function (array $args) use (&$dropdownArgs): void {
                $dropdownArgs = $args;
            }
        );
        $_GET['genre'] = 'fantasy';

        try {
            (new TaxonomyFilterRegistrar('book', ['genre']))->renderFilters('book');
        } finally {
            unset($_GET['genre']);
        }

        self::assertSame('genre', $dropdownArgs['name']);
        self::assertSame('slug', $dropdownArgs['value_field']);
        self::assertSame('genre', $dropdownArgs['taxonomy']);
        self::assertSame('All Genres', $dropdownArgs['show_option_all']);
        self::assertTrue($dropdownArgs['hierarchical']);
        self::assertSame('fantasy', $dropdownArgs['selected']);
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
