<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\Taxonomy;

use Kaiseki\Config\Config;
use Psr\Container\ContainerInterface;

class TaxonomyFactoryFactory
{
    public function __invoke(ContainerInterface $container): TaxonomyFactory
    {
        return new TaxonomyFactory(Config::get($container)->array('post_type/taxonomy/default_options', []));
    }
}
