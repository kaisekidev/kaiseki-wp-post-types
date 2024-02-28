<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\Taxonomy;

use Kaiseki\Config\Config;
use Psr\Container\ContainerInterface;

class TaxonomyBuilderFactory
{
    public function __invoke(ContainerInterface $container): TaxonomyBuilder
    {
        $config = Config::fromContainer($container);

        return new TaxonomyBuilder(
            $config->array('post_type.default_taxonomy_options')
        );
    }
}
