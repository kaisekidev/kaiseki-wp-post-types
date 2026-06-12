<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

use function array_values;

final class Columns
{
    /** @var list<Column> */
    private array $columns = [];
    /** @var list<string> */
    private array $hidden = [];

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function with(Column ...$columns): self
    {
        $clone = clone $this;
        $clone->columns = [...$clone->columns, ...array_values($columns)];

        return $clone;
    }

    /**
     * Hide default columns by key (e.g. "date", "author").
     *
     * @param string ...$keys
     */
    public function without(string ...$keys): self
    {
        $clone = clone $this;
        $clone->hidden = [...$clone->hidden, ...array_values($keys)];

        return $clone;
    }

    /**
     * @return list<Column>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return list<string>
     */
    public function getHidden(): array
    {
        return $this->hidden;
    }

    public function getColumn(string $key): ?Column
    {
        foreach ($this->columns as $column) {
            if ($column->getKey() === $key) {
                return $column;
            }
        }

        return null;
    }
}
