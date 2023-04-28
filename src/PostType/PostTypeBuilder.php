<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\PostType;

use PostTypes\PostType;

use function array_merge;

final class PostTypeBuilder implements PostTypeBuilderInterface
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
    public function build(array $names, array $options = [], array $labels = []): PostType
    {
        $options = array_merge($this->defaultPostTypeOptions, $options);
        return new class ($names, $options, $labels) extends PostType {
            // @phpstan-ignore-next-line
            public function options(array $options = [])
            {
                $this->options = array_merge($this->options, $options);
                return $this;
            }
        };
    }
}
