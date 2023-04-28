<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

use Kaiseki\WordPress\PostType\PostType\PostTypeBuilder;
use Kaiseki\WordPress\PostType\PostType\PostTypeBuilderFactory;
use Kaiseki\WordPress\PostType\Taxonomy\PostTypeBuilderInterface;
use Kaiseki\WordPress\PostType\Taxonomy\TaxonomyBuilder;
use Kaiseki\WordPress\PostType\Taxonomy\TaxonomyBuilderFactory;
use Kaiseki\WordPress\PostType\Taxonomy\TaxonomyBuilderInterface;

final class ConfigProvider
{
    /**
     * @return array<mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'aliases' => [
                    PostTypeBuilderInterface::class => PostTypeBuilder::class,
                    TaxonomyBuilderInterface::class => TaxonomyBuilder::class,
                ],
                'factories' => [
                    PostTypeBuilder::class => PostTypeBuilderFactory::class,
                    TaxonomyBuilder::class => TaxonomyBuilderFactory::class,
                ],
            ],
        ];
    }
}
