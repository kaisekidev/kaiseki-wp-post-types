<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType\TestDouble;

use Kaiseki\WordPress\PostType\TaxonomyInterface;
use Kaiseki\WordPress\PostType\TaxonomyProviderInterface;

final class DummyTaxonomyProvider implements TaxonomyProviderInterface
{
    /**
     * @param list<TaxonomyInterface> $taxonomies
     */
    public function __construct(private readonly array $taxonomies)
    {
    }

    public function getTaxonomies(): array
    {
        return $this->taxonomies;
    }
}
