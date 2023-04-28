<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType\Taxonomy;

use PostTypes\Taxonomy;

use function array_merge;

final class TaxonomyBuilder implements TaxonomyBuilderInterface
{
    /**
     * For default taxonomy options, see $args parameter of register_taxonomy()
     * https://developer.wordpress.org/reference/functions/register_taxonomy/#parameters
     *
     * @param array<string, mixed> $defaultTaxonomyOptions
     */
    public function __construct(
        private readonly array $defaultTaxonomyOptions = []
    ) {
    }

    /**
     * @param array<string, string> $names
     * @param array<string, mixed> $options
     * @param array<string, string> $labels
     */
    public function build(array $names, array $options = [], array $labels = []): Taxonomy
    {
        $options = array_merge($this->defaultTaxonomyOptions, $options);
        return new class ($names, $options, $labels) extends Taxonomy {
            // @phpstan-ignore-next-line
            public function options(array $options = [])
            {
                $this->options = array_merge($this->options, $options);
                return $this;
            }
        };
    }
}
