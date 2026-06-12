<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

use function add_action;
use function get_taxonomy;
use function is_object_in_taxonomy;
use function is_string;
use function sanitize_title;
use function taxonomy_exists;
use function wp_dropdown_categories;

/**
 * Renders taxonomy dropdown filters on the admin list table for the
 * taxonomies a post type opted into via withTaxonomyFilters().
 */
final class TaxonomyFilterRegistrar
{
    /**
     * @param string       $postType
     * @param list<string> $taxonomies
     */
    public function __construct(
        private readonly string $postType,
        private readonly array $taxonomies,
    ) {
    }

    public function addHooks(): void
    {
        add_action('restrict_manage_posts', [$this, 'renderFilters']);
    }

    public function renderFilters(string $postType): void
    {
        if ($postType !== $this->postType) {
            return;
        }
        foreach ($this->taxonomies as $taxonomyName) {
            if (!taxonomy_exists($taxonomyName)) {
                continue;
            }
            if (!is_object_in_taxonomy($this->postType, $taxonomyName)) {
                continue;
            }
            $taxonomy = get_taxonomy($taxonomyName);
            if ($taxonomy === false) {
                continue;
            }
            $allItems = $taxonomy->labels->all_items;
            $selected = isset($_GET[$taxonomyName]) && is_string($_GET[$taxonomyName])
                ? sanitize_title($_GET[$taxonomyName])
                : 0;
            wp_dropdown_categories([
                'name' => $taxonomyName,
                'value_field' => 'slug',
                'taxonomy' => $taxonomy->name,
                'show_option_all' => is_string($allItems) ? $allItems : '',
                'hierarchical' => $taxonomy->hierarchical,
                'selected' => $selected,
                'orderby' => 'name',
                'hide_empty' => 0,
                'show_count' => 0,
            ]);
        }
    }
}
