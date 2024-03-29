<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\PostType;

use PostTypes\PostType;

interface PostTypeBuilderInterface
{
    /**
     * @param array<string, string> $names
     * @param array<string, mixed>  $options
     * @param array<string, string> $labels
     */
    public function build(array $names, array $options = [], array $labels = []): PostType;
}
