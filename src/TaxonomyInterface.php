<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

interface TaxonomyInterface
{
    public function getName(): string;

    /**
     * @return list<string>
     */
    public function getPostTypes(): array;

    /**
     * Complete register_taxonomy() args. $defaults sit between the package
     * baseline and the values set on the definition.
     *
     * @param array<string, mixed> $defaults
     *
     * @return array<string, mixed>
     */
    public function toArray(array $defaults = []): array;
}
