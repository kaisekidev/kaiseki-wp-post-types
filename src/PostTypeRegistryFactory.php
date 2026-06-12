<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

use Kaiseki\Config\Config;
use Psr\Container\ContainerInterface;

use function array_filter;
use function is_string;

use const ARRAY_FILTER_USE_KEY;

final class PostTypeRegistryFactory
{
    public function __invoke(ContainerInterface $container): PostTypeRegistry
    {
        $config = Config::fromContainer($container);
        /** @var list<class-string<PostTypeProviderInterface>> $postTypeClasses */
        $postTypeClasses = $config->array('post_type.post_types');
        /** @var list<class-string<TaxonomyProviderInterface>> $taxonomyClasses */
        $taxonomyClasses = $config->array('post_type.taxonomies');
        /** @var list<PostTypeProviderInterface> $postTypeProviders */
        $postTypeProviders = Config::initClassMap($container, $postTypeClasses);
        /** @var list<TaxonomyProviderInterface> $taxonomyProviders */
        $taxonomyProviders = Config::initClassMap($container, $taxonomyClasses);

        return new PostTypeRegistry(
            $postTypeProviders,
            $taxonomyProviders,
            self::stringKeyed($config->array('post_type.default_post_type_options')),
            self::stringKeyed($config->array('post_type.default_taxonomy_options')),
        );
    }

    /**
     * @param array<mixed> $options
     *
     * @return array<string, mixed>
     */
    private static function stringKeyed(array $options): array
    {
        return array_filter(
            $options,
            static fn(int|string $key): bool => is_string($key),
            ARRAY_FILTER_USE_KEY,
        );
    }
}
