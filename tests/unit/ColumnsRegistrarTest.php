<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Kaiseki\WordPress\PostType\Column;
use Kaiseki\WordPress\PostType\Columns;
use Kaiseki\WordPress\PostType\ColumnsRegistrar;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

use function has_action;
use function has_filter;

final class ColumnsRegistrarTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testAddHooksWiresColumnHooks(): void
    {
        $registrar = new ColumnsRegistrar('book', Columns::create());

        $registrar->addHooks();

        self::assertSame(10, has_filter('manage_book_posts_columns', [$registrar, 'modifyColumns']));
        self::assertSame(10, has_action('manage_book_posts_custom_column', [$registrar, 'renderColumn']));
        self::assertSame(10, has_filter('manage_edit-book_sortable_columns', [$registrar, 'addSortableColumns']));
        self::assertSame(10, has_action('pre_get_posts', [$registrar, 'applySortableOrder']));
    }

    public function testModifyColumnsHidesAppendsAndPositions(): void
    {
        $registrar = new ColumnsRegistrar('book', Columns::create()
            ->without('date')
            ->with(
                Column::create('price', 'Price'),
                Column::create('sku', 'SKU')->withPosition(1),
            ));

        $columns = $registrar->modifyColumns([
            'cb' => '<input type="checkbox" />',
            'title' => 'Title',
            'date' => 'Date',
        ]);

        self::assertSame(
            [
                'cb' => '<input type="checkbox" />',
                'sku' => 'SKU',
                'title' => 'Title',
                'price' => 'Price',
            ],
            $columns,
        );
    }

    public function testRenderColumnEchoesRendererReturnValue(): void
    {
        $registrar = new ColumnsRegistrar('book', Columns::create()->with(
            Column::create('price', 'Price')->withRenderer(static fn(int $postId): string => 'price-of-' . $postId),
        ));

        $this->expectOutputString('price-of-42');
        $registrar->renderColumn('price', 42);
    }

    public function testRenderColumnIgnoresUnknownAndRendererlessColumns(): void
    {
        $registrar = new ColumnsRegistrar('book', Columns::create()->with(Column::create('price', 'Price')));

        $this->expectOutputString('');
        $registrar->renderColumn('price', 42);
        $registrar->renderColumn('unknown', 42);
    }

    public function testAddSortableColumnsAddsSortableKeysOnly(): void
    {
        $registrar = new ColumnsRegistrar('book', Columns::create()->with(
            Column::create('price', 'Price')->withSortable('price', isMeta: true),
            Column::create('notes', 'Notes'),
        ));

        self::assertSame(
            ['title' => 'title', 'price' => 'price'],
            $registrar->addSortableColumns(['title' => 'title']),
        );
    }

    public function testApplySortableOrderSetsMetaQueryForMetaColumns(): void
    {
        Functions\when('is_admin')->justReturn(true);
        $registrar = new ColumnsRegistrar('book', Columns::create()->with(
            Column::create('price', 'Price')->withSortable('price', isMeta: true, numeric: true),
        ));
        $query = Mockery::mock('WP_Query');
        $query->shouldReceive('is_main_query')->andReturn(true);
        $query->shouldReceive('get')->with('post_type')->andReturn('book');
        $query->shouldReceive('get')->with('orderby')->andReturn('price');
        $query->shouldReceive('set')->once()->with('meta_key', 'price');
        $query->shouldReceive('set')->once()->with('orderby', 'meta_value_num');

        $registrar->applySortableOrder($query);
    }

    public function testApplySortableOrderIgnoresOtherPostTypes(): void
    {
        Functions\when('is_admin')->justReturn(true);
        $registrar = new ColumnsRegistrar('book', Columns::create()->with(
            Column::create('price', 'Price')->withSortable('price', isMeta: true),
        ));
        $query = Mockery::mock('WP_Query');
        $query->shouldReceive('is_main_query')->andReturn(true);
        $query->shouldReceive('get')->with('post_type')->andReturn('page');
        $query->shouldNotReceive('set');

        $registrar->applySortableOrder($query);
    }

    public function testApplySortableOrderIgnoresSecondaryQueries(): void
    {
        Functions\when('is_admin')->justReturn(true);
        $registrar = new ColumnsRegistrar('book', Columns::create()->with(
            Column::create('price', 'Price')->withSortable('price', isMeta: true),
        ));
        $query = Mockery::mock('WP_Query');
        $query->shouldReceive('is_main_query')->andReturn(false);
        $query->shouldNotReceive('get');
        $query->shouldNotReceive('set');

        $registrar->applySortableOrder($query);
    }

    public function testApplySortableOrderIgnoresFrontend(): void
    {
        Functions\when('is_admin')->justReturn(false);
        $registrar = new ColumnsRegistrar('book', Columns::create()->with(
            Column::create('price', 'Price')->withSortable('price', isMeta: true),
        ));
        $query = Mockery::mock('WP_Query');
        $query->shouldNotReceive('set');

        $registrar->applySortableOrder($query);
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
