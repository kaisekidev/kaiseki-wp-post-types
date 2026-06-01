<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\Taxonomy;

use Kaiseki\Config\Config;
use Psr\Container\ContainerInterface;

use function array_filter;
use function is_string;

use const ARRAY_FILTER_USE_KEY;

class TaxonomyBuilderFactory
{
    public function __invoke(ContainerInterface $container): TaxonomyBuilder
    {
        $config = Config::fromContainer($container);
        $options = array_filter(
            $config->array('post_type.default_taxonomy_options'),
            static fn(int|string $key): bool => is_string($key),
            ARRAY_FILTER_USE_KEY,
        );

        return new TaxonomyBuilder($options);
    }
}
