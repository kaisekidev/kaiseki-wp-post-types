<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

final class Column
{
    /** @var (callable(int): string)|null */
    private $renderer = null;
    private ?string $sortableOrderBy = null;
    private bool $sortableIsMeta = false;
    private bool $sortableNumeric = false;
    private ?int $position = null;

    private function __construct(
        private readonly string $key,
        private readonly string $label,
    ) {
    }

    public static function create(string $key, string $label): self
    {
        return new self($key, $label);
    }

    /**
     * The renderer returns the cell markup; the registrar echoes it.
     * Escaping is the renderer's responsibility.
     *
     * @param callable(int): string $renderer
     */
    public function withRenderer(callable $renderer): self
    {
        $clone = clone $this;
        $clone->renderer = $renderer;

        return $clone;
    }

    /**
     * @param string $orderBy the orderby query arg; the meta key when $isMeta
     * @param bool   $isMeta
     * @param bool   $numeric
     */
    public function withSortable(string $orderBy, bool $isMeta = false, bool $numeric = false): self
    {
        $clone = clone $this;
        $clone->sortableOrderBy = $orderBy;
        $clone->sortableIsMeta = $isMeta;
        $clone->sortableNumeric = $numeric;

        return $clone;
    }

    /**
     * Insertion index in the column list; appended when not set.
     *
     * @param int $position
     */
    public function withPosition(int $position): self
    {
        $clone = clone $this;
        $clone->position = $position;

        return $clone;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return (callable(int): string)|null
     */
    public function getRenderer(): ?callable
    {
        return $this->renderer;
    }

    public function getSortableOrderBy(): ?string
    {
        return $this->sortableOrderBy;
    }

    public function isSortableMeta(): bool
    {
        return $this->sortableIsMeta;
    }

    public function isSortableNumeric(): bool
    {
        return $this->sortableNumeric;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }
}
