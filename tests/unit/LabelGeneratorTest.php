<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType;

use Kaiseki\WordPress\PostType\LabelGenerator;
use PHPUnit\Framework\TestCase;

final class LabelGeneratorTest extends TestCase
{
    public function testPostTypeLabelsMatchPosttypesV2Output(): void
    {
        self::assertSame(
            [
                'name' => 'Doors',
                'singular_name' => 'Door',
                'menu_name' => 'Doors',
                'all_items' => 'Doors',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Door',
                'edit_item' => 'Edit Door',
                'new_item' => 'New Door',
                'view_item' => 'View Door',
                'search_items' => 'Search Doors',
                'not_found' => 'No Doors found',
                'not_found_in_trash' => 'No Doors found in Trash',
                'parent_item_colon' => 'Parent Door:',
            ],
            LabelGenerator::postTypeLabels('Door', 'Doors'),
        );
    }

    public function testTaxonomyLabelsMatchPosttypesV2OutputWithTypoFixed(): void
    {
        self::assertSame(
            [
                'name' => 'Topics',
                'singular_name' => 'Topic',
                'menu_name' => 'Topics',
                'all_items' => 'All Topics',
                'edit_item' => 'Edit Topic',
                'view_item' => 'View Topic',
                'update_item' => 'Update Topic',
                'add_new_item' => 'Add New Topic',
                'new_item_name' => 'New Topic Name',
                'parent_item' => 'Parent Topics',
                'parent_item_colon' => 'Parent Topics:',
                'search_items' => 'Search Topics',
                'popular_items' => 'Popular Topics',
                'separate_items_with_commas' => 'Separate Topics with commas',
                'add_or_remove_items' => 'Add or remove Topics',
                'choose_from_most_used' => 'Choose from most used Topics',
                'not_found' => 'No Topics found',
            ],
            LabelGenerator::taxonomyLabels('Topic', 'Topics'),
        );
    }
}
