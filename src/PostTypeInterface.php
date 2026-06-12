<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

interface PostTypeInterface
{
    /**
     * @return lowercase-string&non-empty-string
     */
    public function getName(): string;

    /**
     * @return list<string>
     */
    public function getTaxonomies(): array;

    /**
     * @return list<string>
     */
    public function getTaxonomyFilters(): array;

    public function getColumns(): ?Columns;

    /**
     * Complete register_post_type() args. $defaults sit between the package
     * baseline and the values set on the definition.
     *
     * @param array<string, mixed> $defaults
     *
     * @return array<string, mixed>
     */
    public function toArray(array $defaults = []): array;
}
