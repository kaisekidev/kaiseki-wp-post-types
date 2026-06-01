<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\PostType;

use Kaiseki\Config\Config;
use Psr\Container\ContainerInterface;

use function array_filter;
use function is_string;

use const ARRAY_FILTER_USE_KEY;

class PostTypeBuilderFactory
{
    public function __invoke(ContainerInterface $container): PostTypeBuilder
    {
        $config = Config::fromContainer($container);
        $options = array_filter(
            $config->array('post_type.default_post_type_options'),
            static fn(int|string $key): bool => is_string($key),
            ARRAY_FILTER_USE_KEY,
        );

        return new PostTypeBuilder($options);
    }
}
