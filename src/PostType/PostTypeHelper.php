<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\PostType;

use PostTypes\PostType;

class PostTypeHelper
{
    public function addTaxonomies(PostType $postType, string ...$taxonomies): PostType
    {
        foreach ($taxonomies as $taxonomy) {
            $postType->taxonomy($taxonomy);
        }
        return $postType;
    }
}
