<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

use WP_Query;

use function add_action;
use function add_filter;
use function array_slice;
use function is_admin;
use function is_string;

final class ColumnsRegistrar
{
    public function __construct(
        private readonly string $postType,
        private readonly Columns $columns,
    ) {
    }

    public function addHooks(): void
    {
        add_filter('manage_' . $this->postType . '_posts_columns', [$this, 'modifyColumns']);
        add_action('manage_' . $this->postType . '_posts_custom_column', [$this, 'renderColumn'], 10, 2);
        add_filter('manage_edit-' . $this->postType . '_sortable_columns', [$this, 'addSortableColumns']);
        add_action('pre_get_posts', [$this, 'applySortableOrder']);
    }

    /**
     * @param array<string, string> $columns
     *
     * @return array<string, string>
     */
    public function modifyColumns(array $columns): array
    {
        foreach ($this->columns->getHidden() as $key) {
            unset($columns[$key]);
        }
        foreach ($this->columns->getColumns() as $column) {
            $position = $column->getPosition();
            if ($position === null) {
                $columns[$column->getKey()] = $column->getLabel();

                continue;
            }
            $columns = array_slice($columns, 0, $position, true)
                + [$column->getKey() => $column->getLabel()]
                + array_slice($columns, $position, null, true);
        }

        return $columns;
    }

    public function renderColumn(string $columnKey, int $postId): void
    {
        $renderer = $this->columns->getColumn($columnKey)?->getRenderer();
        if ($renderer === null) {
            return;
        }

        echo $renderer($postId);
    }

    /**
     * @param array<string, string> $columns
     *
     * @return array<string, string>
     */
    public function addSortableColumns(array $columns): array
    {
        foreach ($this->columns->getColumns() as $column) {
            $orderBy = $column->getSortableOrderBy();
            if ($orderBy === null) {
                continue;
            }
            $columns[$column->getKey()] = $orderBy;
        }

        return $columns;
    }

    public function applySortableOrder(WP_Query $query): void
    {
        if (!is_admin() || $query->get('post_type') !== $this->postType) {
            return;
        }
        $orderBy = $query->get('orderby');
        if (!is_string($orderBy)) {
            return;
        }
        foreach ($this->columns->getColumns() as $column) {
            if (!$column->isSortableMeta() || $column->getSortableOrderBy() !== $orderBy) {
                continue;
            }
            $query->set('meta_key', $orderBy);
            $query->set('orderby', $column->isSortableNumeric() ? 'meta_value_num' : 'meta_value');
        }
    }
}
