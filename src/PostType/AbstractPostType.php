<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\PostType;

use PostTypes\PostType;

abstract class AbstractPostType
{
    protected PostType $postType;

    public function addTaxonomies(string ...$taxonomies): PostType
    {
        foreach ($taxonomies as $taxonomy) {
            $this->postType->taxonomy($taxonomy);
        }
        return $this->postType;
    }

    protected function registerPostType(): void
    {
        $this->postType->register();
    }
}
