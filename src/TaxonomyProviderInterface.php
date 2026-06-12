<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

interface TaxonomyProviderInterface
{
    /**
     * @return list<TaxonomyInterface>
     */
    public function getTaxonomies(): array;
}
