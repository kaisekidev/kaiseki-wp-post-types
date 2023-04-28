<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\PostType;

use Kaiseki\Config\Config;
use Psr\Container\ContainerInterface;

class PostTypeBuilderFactory
{
    public function __invoke(ContainerInterface $container): PostTypeBuilder
    {
        $config = Config::get($container);
        return new PostTypeBuilder(
            $config->array('post_type/default_post_type_options', [])
        );
    }
}
