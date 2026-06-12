<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

interface PostTypeProviderInterface
{
    /**
     * @return list<PostTypeInterface>
     */
    public function getPostTypes(): array;
}
