<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\Taxonomy;

use PostTypes\Taxonomy;

abstract class AbstractTaxonomy
{
    protected Taxonomy $taxonomy;

    public function addToPosttype(string ...$postTypes): Taxonomy
    {
        foreach ($postTypes as $postType) {
            $this->taxonomy->posttype($postType);
        }
        return $this->taxonomy;
    }

    protected function registerTaxonomy(): void
    {
        $this->taxonomy->register();
    }
}
