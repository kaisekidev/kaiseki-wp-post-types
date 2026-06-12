<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType;

use Kaiseki\WordPress\PostType\Columns;
use Kaiseki\WordPress\PostType\LabelGenerator;
use Kaiseki\WordPress\PostType\PostType;
use PHPUnit\Framework\TestCase;

final class PostTypeTest extends TestCase
{
    public function testToArrayAppliesBaselineDefaults(): void
    {
        $args = PostType::create('book', 'Book', 'Books')->toArray();

        self::assertTrue($args['public']);
        self::assertSame(['slug' => 'book'], $args['rewrite']);
        self::assertSame(LabelGenerator::postTypeLabels('Book', 'Books'), $args['labels']);
    }

    public function testExplicitSlugFeedsRewrite(): void
    {
        $args = PostType::create('advcal_door', 'Door', 'Doors', 'cd')->toArray();

        self::assertSame(['slug' => 'cd'], $args['rewrite']);
    }

    public function testTypedWithersSetRegisterArgs(): void
    {
        $args = PostType::create('book', 'Book', 'Books')
            ->withPublic(false)
            ->withHierarchical()
            ->withShowUi()
            ->withShowInMenu(false)
            ->withShowInAdminBar()
            ->withShowInRest()
            ->withHasArchive('books-archive')
            ->withSupports('title', 'editor')
            ->withMenuIcon('dashicons-book')
            ->withMenuPosition(5)
            ->toArray();

        self::assertFalse($args['public']);
        self::assertTrue($args['hierarchical']);
        self::assertTrue($args['show_ui']);
        self::assertFalse($args['show_in_menu']);
        self::assertTrue($args['show_in_admin_bar']);
        self::assertTrue($args['show_in_rest']);
        self::assertSame('books-archive', $args['has_archive']);
        self::assertSame(['title', 'editor'], $args['supports']);
        self::assertSame('dashicons-book', $args['menu_icon']);
        self::assertSame(5, $args['menu_position']);
    }

    public function testDefaultsSitBetweenBaselineAndDefinition(): void
    {
        $postType = PostType::create('book', 'Book', 'Books')->withShowInRest(false);

        $args = $postType->toArray(['show_in_rest' => true, 'menu_position' => 20, 'public' => false]);

        self::assertFalse($args['show_in_rest']);
        self::assertSame(20, $args['menu_position']);
        self::assertFalse($args['public']);
    }

    public function testWithOptionsSharesBucketWithTypedWithersLastCallWins(): void
    {
        $args = PostType::create('book', 'Book', 'Books')
            ->withMenuPosition(5)
            ->withOptions(['menu_position' => 10, 'capability_type' => 'page'])
            ->toArray();

        self::assertSame(10, $args['menu_position']);
        self::assertSame('page', $args['capability_type']);

        $args = PostType::create('book', 'Book', 'Books')
            ->withOptions(['menu_position' => 10])
            ->withMenuPosition(5)
            ->toArray();

        self::assertSame(5, $args['menu_position']);
    }

    public function testRewriteArrayGetsSlugInjectedWhenMissing(): void
    {
        $args = PostType::create('book', 'Book', 'Books')
            ->withRewrite(['with_front' => false])
            ->toArray();

        self::assertSame(['slug' => 'book', 'with_front' => false], $args['rewrite']);
    }

    public function testRewriteFalseIsKept(): void
    {
        $args = PostType::create('book', 'Book', 'Books')->withRewrite(false)->toArray();

        self::assertFalse($args['rewrite']);
    }

    public function testLabelPrecedenceGeneratedThenDefaultsThenDefinition(): void
    {
        $postType = PostType::create('book', 'Book', 'Books')
            ->withLabels(['add_new' => 'Neues Buch']);

        $labels = $postType->toArray(['labels' => ['add_new' => 'Hinzufügen', 'menu_name' => 'Bücher']])['labels'];

        self::assertIsArray($labels);
        self::assertSame('Neues Buch', $labels['add_new']);
        self::assertSame('Bücher', $labels['menu_name']);
        self::assertSame('Books', $labels['name']);
    }

    public function testLabelsViaOptionsEscapeHatchReplaceGeneratedSet(): void
    {
        $args = PostType::create('book', 'Book', 'Books')
            ->withOptions(['labels' => ['name' => 'Books only']])
            ->toArray();

        self::assertSame(['name' => 'Books only'], $args['labels']);
    }

    public function testWithLabelsAfterOptionsLabelsMergesOverThem(): void
    {
        $args = PostType::create('book', 'Book', 'Books')
            ->withOptions(['labels' => ['name' => 'Books only', 'add_new' => 'Add']])
            ->withLabels(['add_new' => 'Neues Buch'])
            ->toArray();

        self::assertSame(['name' => 'Books only', 'add_new' => 'Neues Buch'], $args['labels']);
    }

    public function testOptionsLabelsAfterWithLabelsReplaceThem(): void
    {
        $args = PostType::create('book', 'Book', 'Books')
            ->withLabels(['add_new' => 'Neues Buch'])
            ->withOptions(['labels' => ['name' => 'Books only']])
            ->toArray();

        self::assertSame(['name' => 'Books only'], $args['labels']);
    }

    public function testWithersAreImmutable(): void
    {
        $original = PostType::create('book', 'Book', 'Books');
        $modified = $original
            ->withPublic(false)
            ->withTaxonomies('genre')
            ->withTaxonomyFilters('genre')
            ->withColumns(Columns::create());

        self::assertTrue($original->toArray()['public']);
        self::assertSame([], $original->getTaxonomies());
        self::assertSame([], $original->getTaxonomyFilters());
        self::assertNull($original->getColumns());
        self::assertFalse($modified->toArray()['public']);
        self::assertSame(['genre'], $modified->getTaxonomies());
        self::assertSame(['genre'], $modified->getTaxonomyFilters());
        self::assertNotNull($modified->getColumns());
    }

    public function testTaxonomiesAccumulateAcrossCalls(): void
    {
        $postType = PostType::create('book', 'Book', 'Books')
            ->withTaxonomies('genre')
            ->withTaxonomies('audience', 'era');

        self::assertSame(['genre', 'audience', 'era'], $postType->getTaxonomies());
    }

    public function testGetName(): void
    {
        self::assertSame('book', PostType::create('book', 'Book', 'Books')->getName());
    }
}
