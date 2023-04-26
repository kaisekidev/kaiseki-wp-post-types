<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\PostType;

use Kaiseki\Config\Config;
use Psr\Container\ContainerInterface;

class PostTypeFactoryFactory
{
    public function __invoke(ContainerInterface $container): PostTypeFactory
    {
        return new PostTypeFactory(Config::get($container)->array('post_type/post/default_options', []));
    }
}
