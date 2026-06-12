<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

use Kaiseki\WordPress\Hook\HookProviderInterface;

use function add_action;
use function register_post_type;
use function register_taxonomy;
use function register_taxonomy_for_object_type;

final class PostTypeRegistry implements HookProviderInterface
{
    /**
     * @param list<PostTypeProviderInterface> $postTypeProviders
     * @param list<TaxonomyProviderInterface> $taxonomyProviders
     * @param array<string, mixed>            $defaultPostTypeOptions
     * @param array<string, mixed>            $defaultTaxonomyOptions
     */
    public function __construct(
        private readonly array $postTypeProviders,
        private readonly array $taxonomyProviders,
        private readonly array $defaultPostTypeOptions = [],
        private readonly array $defaultTaxonomyOptions = [],
    ) {
    }

    public function addHooks(): void
    {
        add_action('init', [$this, 'register']);
    }

    /**
     * Registration order is a public contract: all taxonomies, then all post
     * types, then every taxonomy↔post-type association declared on either
     * side — so the link holds regardless of which side declares it, including
     * post types or taxonomies registered by third parties.
     */
    public function register(): void
    {
        $taxonomies = [];
        foreach ($this->taxonomyProviders as $provider) {
            foreach ($provider->getTaxonomies() as $taxonomy) {
                $taxonomies[] = $taxonomy;
            }
        }
        $postTypes = [];
        foreach ($this->postTypeProviders as $provider) {
            foreach ($provider->getPostTypes() as $postType) {
                $postTypes[] = $postType;
            }
        }

        foreach ($taxonomies as $taxonomy) {
            register_taxonomy(
                $taxonomy->getName(),
                $taxonomy->getPostTypes(),
                $taxonomy->toArray($this->defaultTaxonomyOptions),
            );
        }

        foreach ($postTypes as $postType) {
            register_post_type($postType->getName(), $postType->toArray($this->defaultPostTypeOptions));

            $columns = $postType->getColumns();
            if ($columns !== null) {
                (new ColumnsRegistrar($postType->getName(), $columns))->addHooks();
            }
            if ($postType->getTaxonomyFilters() === []) {
                continue;
            }
            (new TaxonomyFilterRegistrar($postType->getName(), $postType->getTaxonomyFilters()))->addHooks();
        }

        foreach ($taxonomies as $taxonomy) {
            foreach ($taxonomy->getPostTypes() as $postTypeName) {
                register_taxonomy_for_object_type($taxonomy->getName(), $postTypeName);
            }
        }
        foreach ($postTypes as $postType) {
            foreach ($postType->getTaxonomies() as $taxonomyName) {
                register_taxonomy_for_object_type($taxonomyName, $postType->getName());
            }
        }
    }
}
