<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType\TestDouble;

use Kaiseki\WordPress\PostType\PostTypeInterface;
use Kaiseki\WordPress\PostType\PostTypeProviderInterface;

final class DummyPostTypeProvider implements PostTypeProviderInterface
{
    /**
     * @param list<PostTypeInterface> $postTypes
     */
    public function __construct(private readonly array $postTypes)
    {
    }

    public function getPostTypes(): array
    {
        return $this->postTypes;
    }
}
