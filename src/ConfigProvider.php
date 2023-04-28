<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

use Kaiseki\WordPress\PostType\PostType\PostTypeBuilder;
use Kaiseki\WordPress\PostType\PostType\PostTypeBuilderFactory;
use Kaiseki\WordPress\PostType\Taxonomy\TaxonomyBuilder;
use Kaiseki\WordPress\PostType\Taxonomy\TaxonomyBuilderFactory;

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
                    PostTypeBuilder::class => PostTypeBuilderFactory::class,
                    TaxonomyBuilder::class => TaxonomyBuilderFactory::class,
                ],
            ],
        ];
    }
}
