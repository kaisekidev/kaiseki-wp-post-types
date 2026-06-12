<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType;

use Kaiseki\WordPress\PostType\ConfigProvider;
use Kaiseki\WordPress\PostType\PostTypeRegistry;
use Kaiseki\WordPress\PostType\PostTypeRegistryFactory;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    public function testProvidesConfigKeysHookProviderAndFactories(): void
    {
        $config = (new ConfigProvider())();

        self::assertSame(
            [
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
            ],
            $config,
        );
    }
}
