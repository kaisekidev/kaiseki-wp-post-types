<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

final class ConfigProvider
{
    /**
     * @return array<mixed>
     */
    public function __invoke(): array
    {
        return [
            'post_type' => [
                'post_types' => [],
                'taxonomies' => [],
                'default_post_type_options' => [],
                'default_taxonomy_options' => [],
            ],
            'hook' => [
                'provider' => [
                    PostTypeRegistry::class,
                ],
            ],
            'dependencies' => [
                'factories' => [
                    PostTypeRegistry::class => PostTypeRegistryFactory::class,
                ],
            ],
        ];
    }
}
