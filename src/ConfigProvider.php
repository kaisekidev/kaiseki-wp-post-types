<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

use Kaiseki\WordPress\PostType\PostType\PostTypeFactory;
use Kaiseki\WordPress\PostType\PostType\PostTypeFactoryFactory;
use Kaiseki\WordPress\PostType\Taxonomy\TaxonomyFactory;
use Kaiseki\WordPress\PostType\Taxonomy\TaxonomyFactoryFactory;

final class ConfigProvider
{
    /**
     * @return array<mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    PostTypeFactory::class => PostTypeFactoryFactory::class,
                    TaxonomyFactory::class => TaxonomyFactoryFactory::class,
                ],
            ],
        ];
    }
}
