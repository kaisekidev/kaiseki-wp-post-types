<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\PostType;

use PostTypes\PostType;

use function array_merge;

final class PostTypeFactory
{
    /** @var array<string, mixed>  */
    private array $defaultOptions;

    /**
     * @param array<string, mixed> $defaultOptions
     */
    public function __construct(array $defaultOptions)
    {
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * @param array<string, string> $names
     * @param array<string, mixed> $options
     * @param array<string, string> $labels
     */
    public function create(array $names, array $options = [], array $labels = []): PostType
    {
        return new PostType($names, array_merge($this->defaultOptions, $options), $labels);
    }
}
