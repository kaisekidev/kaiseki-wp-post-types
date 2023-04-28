<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\PostType;

use PostTypes\PostType;

use function array_merge;

final class PostTypeBuilder
{
    /**
     * For default post type options, see $args parameter of register_post_type()
     * https://developer.wordpress.org/reference/functions/register_post_type/#parameters
     *
     * @param array<string, mixed> $defaultPostTypeOptions
     */
    public function __construct(
        private readonly array $defaultPostTypeOptions = []
    ) {
    }

    /**
     * @param array<string, string> $names
     * @param array<string, mixed> $options
     * @param array<string, string> $labels
     */
    public function createPostType(array $names, array $options = [], array $labels = []): PostType
    {
        return new PostType($names, array_merge($this->defaultPostTypeOptions, $options), $labels);
    }
}
