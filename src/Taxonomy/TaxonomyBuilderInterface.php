<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\Taxonomy;

use PostTypes\Taxonomy;

interface TaxonomyBuilderInterface
{
    /**
     * @param array<string, string> $names
     * @param array<string, mixed> $options
     * @param array<string, string> $labels
     */
    public function build(array $names, array $options = [], array $labels = []): Taxonomy;
}
