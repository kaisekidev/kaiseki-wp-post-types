<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

use function sprintf;

/**
 * Reproduces the label sets jjgrainger/posttypes 2.x auto-generated, so 2.0
 * registers post types and taxonomies with unchanged admin wording (one typo
 * fixed: "Separate"). Labels stay English by design; translate per definition
 * via withLabels().
 */
final class LabelGenerator
{
    private function __construct()
    {
    }

    /**
     * @param string $singular
     * @param string $plural
     *
     * @return array<string, string>
     */
    public static function postTypeLabels(string $singular, string $plural): array
    {
        return [
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $plural,
            'all_items' => $plural,
            'add_new' => 'Add New',
            'add_new_item' => sprintf('Add New %s', $singular),
            'edit_item' => sprintf('Edit %s', $singular),
            'new_item' => sprintf('New %s', $singular),
            'view_item' => sprintf('View %s', $singular),
            'search_items' => sprintf('Search %s', $plural),
            'not_found' => sprintf('No %s found', $plural),
            'not_found_in_trash' => sprintf('No %s found in Trash', $plural),
            'parent_item_colon' => sprintf('Parent %s:', $singular),
        ];
    }

    /**
     * @param string $singular
     * @param string $plural
     *
     * @return array<string, string>
     */
    public static function taxonomyLabels(string $singular, string $plural): array
    {
        return [
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $plural,
            'all_items' => sprintf('All %s', $plural),
            'edit_item' => sprintf('Edit %s', $singular),
            'view_item' => sprintf('View %s', $singular),
            'update_item' => sprintf('Update %s', $singular),
            'add_new_item' => sprintf('Add New %s', $singular),
            'new_item_name' => sprintf('New %s Name', $singular),
            'parent_item' => sprintf('Parent %s', $plural),
            'parent_item_colon' => sprintf('Parent %s:', $plural),
            'search_items' => sprintf('Search %s', $plural),
            'popular_items' => sprintf('Popular %s', $plural),
            'separate_items_with_commas' => sprintf('Separate %s with commas', $plural),
            'add_or_remove_items' => sprintf('Add or remove %s', $plural),
            'choose_from_most_used' => sprintf('Choose from most used %s', $plural),
            'not_found' => sprintf('No %s found', $plural),
        ];
    }
}
