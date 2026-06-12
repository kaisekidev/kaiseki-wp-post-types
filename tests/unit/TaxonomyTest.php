<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType;

use Kaiseki\WordPress\PostType\LabelGenerator;
use Kaiseki\WordPress\PostType\Taxonomy;
use PHPUnit\Framework\TestCase;

final class TaxonomyTest extends TestCase
{
    public function testToArrayAppliesBaselineDefaults(): void
    {
        $args = Taxonomy::create('genre', 'Genre', 'Genres')->toArray();

        self::assertTrue($args['hierarchical']);
        self::assertTrue($args['show_admin_column']);
        self::assertSame(['slug' => 'genre'], $args['rewrite']);
        self::assertSame(LabelGenerator::taxonomyLabels('Genre', 'Genres'), $args['labels']);
    }

    public function testTypedWithersSetRegisterArgs(): void
    {
        $args = Taxonomy::create('genre', 'Genre', 'Genres', 'genres')
            ->withPublic(false)
            ->withHierarchical(false)
            ->withShowUi()
            ->withShowInRest()
            ->withShowAdminColumn(false)
            ->withRewrite(['hierarchical' => true])
            ->toArray();

        self::assertFalse($args['public']);
        self::assertFalse($args['hierarchical']);
        self::assertTrue($args['show_ui']);
        self::assertTrue($args['show_in_rest']);
        self::assertFalse($args['show_admin_column']);
        self::assertSame(['slug' => 'genres', 'hierarchical' => true], $args['rewrite']);
    }

    public function testDefaultsSitBetweenBaselineAndDefinition(): void
    {
        $args = Taxonomy::create('genre', 'Genre', 'Genres')
            ->withHierarchical(true)
            ->toArray(['hierarchical' => false, 'show_in_rest' => true]);

        self::assertTrue($args['hierarchical']);
        self::assertTrue($args['show_in_rest']);
    }

    public function testWithOptionsEscapeHatchLastCallWins(): void
    {
        $args = Taxonomy::create('genre', 'Genre', 'Genres')
            ->withShowAdminColumn()
            ->withOptions(['show_admin_column' => false, 'sort' => true])
            ->toArray();

        self::assertFalse($args['show_admin_column']);
        self::assertTrue($args['sort']);
    }

    public function testLabelPrecedenceGeneratedThenDefaultsThenDefinition(): void
    {
        $labels = Taxonomy::create('genre', 'Genre', 'Genres')
            ->withLabels(['add_new_item' => 'Neues Genre'])
            ->toArray(['labels' => ['add_new_item' => 'Hinzufügen', 'menu_name' => 'Genres (DE)']])['labels'];

        self::assertIsArray($labels);
        self::assertSame('Neues Genre', $labels['add_new_item']);
        self::assertSame('Genres (DE)', $labels['menu_name']);
        self::assertSame('Genres', $labels['name']);
    }

    public function testLabelWitherCallOrderIsPreserved(): void
    {
        $merged = Taxonomy::create('genre', 'Genre', 'Genres')
            ->withOptions(['labels' => ['name' => 'Genres only']])
            ->withLabels(['add_new_item' => 'Neues Genre'])
            ->toArray()['labels'];
        $replaced = Taxonomy::create('genre', 'Genre', 'Genres')
            ->withLabels(['add_new_item' => 'Neues Genre'])
            ->withOptions(['labels' => ['name' => 'Genres only']])
            ->toArray()['labels'];

        self::assertSame(['name' => 'Genres only', 'add_new_item' => 'Neues Genre'], $merged);
        self::assertSame(['name' => 'Genres only'], $replaced);
    }

    public function testWithersAreImmutable(): void
    {
        $original = Taxonomy::create('genre', 'Genre', 'Genres');
        $modified = $original->withPostTypes('book')->withHierarchical(false);

        self::assertSame([], $original->getPostTypes());
        self::assertTrue($original->toArray()['hierarchical']);
        self::assertSame(['book'], $modified->getPostTypes());
        self::assertFalse($modified->toArray()['hierarchical']);
    }

    public function testPostTypesAccumulateAcrossCalls(): void
    {
        $taxonomy = Taxonomy::create('genre', 'Genre', 'Genres')
            ->withPostTypes('book')
            ->withPostTypes('magazine', 'comic');

        self::assertSame(['book', 'magazine', 'comic'], $taxonomy->getPostTypes());
    }

    public function testGetName(): void
    {
        self::assertSame('genre', Taxonomy::create('genre', 'Genre', 'Genres')->getName());
    }
}
