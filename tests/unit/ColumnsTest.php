<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType;

use Kaiseki\WordPress\PostType\Column;
use Kaiseki\WordPress\PostType\Columns;
use PHPUnit\Framework\TestCase;

final class ColumnsTest extends TestCase
{
    public function testWithAndWithoutAccumulateImmutably(): void
    {
        $original = Columns::create();
        $modified = $original
            ->with(Column::create('price', 'Price'))
            ->with(Column::create('stock', 'Stock'))
            ->without('date')
            ->without('author');

        self::assertSame([], $original->getColumns());
        self::assertSame([], $original->getHidden());
        self::assertCount(2, $modified->getColumns());
        self::assertSame(['date', 'author'], $modified->getHidden());
    }

    public function testGetColumnFindsByKey(): void
    {
        $price = Column::create('price', 'Price');
        $columns = Columns::create()->with($price);

        self::assertSame($price, $columns->getColumn('price'));
        self::assertNull($columns->getColumn('unknown'));
    }

    public function testColumnWithersAreImmutableAndExposeState(): void
    {
        $renderer = static fn(int $postId): string => (string)$postId;
        $original = Column::create('price', 'Price');
        $modified = $original
            ->withRenderer($renderer)
            ->withSortable('price', isMeta: true, numeric: true)
            ->withPosition(2);

        self::assertNull($original->getRenderer());
        self::assertNull($original->getSortableOrderBy());
        self::assertFalse($original->isSortableMeta());
        self::assertFalse($original->isSortableNumeric());
        self::assertNull($original->getPosition());
        self::assertSame('price', $modified->getKey());
        self::assertSame('Price', $modified->getLabel());
        self::assertSame($renderer, $modified->getRenderer());
        self::assertSame('price', $modified->getSortableOrderBy());
        self::assertTrue($modified->isSortableMeta());
        self::assertTrue($modified->isSortableNumeric());
        self::assertSame(2, $modified->getPosition());
    }
}
