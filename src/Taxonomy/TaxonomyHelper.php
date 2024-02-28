<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\Taxonomy;

use PostTypes\Taxonomy;

class TaxonomyHelper
{
    public function assignToPostTypes(Taxonomy $taxonomy, string ...$postTypes): Taxonomy
    {
        foreach ($postTypes as $postType) {
            $taxonomy->posttype($postType);
        }

        return $taxonomy;
    }
}
