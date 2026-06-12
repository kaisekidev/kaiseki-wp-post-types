<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

use function array_replace;
use function array_values;
use function is_array;

final class Taxonomy implements TaxonomyInterface
{
    /** @var array<string, mixed> */
    private array $args = [];
    /** @var array<string, string> */
    private array $labels = [];
    /** @var list<string> */
    private array $postTypes = [];

    private function __construct(
        private readonly string $name,
        private readonly string $singular,
        private readonly string $plural,
        private readonly string $slug,
    ) {
    }

    public static function create(string $name, string $singular, string $plural, ?string $slug = null): self
    {
        return new self($name, $singular, $plural, $slug ?? $name);
    }

    public function withPostTypes(string ...$postTypes): self
    {
        $clone = clone $this;
        $clone->postTypes = [...$clone->postTypes, ...array_values($postTypes)];

        return $clone;
    }

    public function withPublic(bool $public = true): self
    {
        return $this->withArg('public', $public);
    }

    public function withHierarchical(bool $hierarchical = true): self
    {
        return $this->withArg('hierarchical', $hierarchical);
    }

    public function withShowUi(bool $showUi = true): self
    {
        return $this->withArg('show_ui', $showUi);
    }

    public function withShowInRest(bool $showInRest = true): self
    {
        return $this->withArg('show_in_rest', $showInRest);
    }

    public function withShowAdminColumn(bool $showAdminColumn = true): self
    {
        return $this->withArg('show_admin_column', $showAdminColumn);
    }

    /**
     * @param array<string, mixed>|bool $rewrite
     */
    public function withRewrite(array|bool $rewrite): self
    {
        return $this->withArg('rewrite', $rewrite);
    }

    /**
     * Merged over the auto-generated label set.
     *
     * @param array<string, string> $labels
     */
    public function withLabels(array $labels): self
    {
        $clone = clone $this;
        $clone->labels = array_replace($clone->labels, $labels);

        return $clone;
    }

    /**
     * Escape hatch for any register_taxonomy() argument without a typed
     * wither. Same precedence bucket as the typed withers: last call wins.
     *
     * @param array<string, mixed> $options
     */
    public function withOptions(array $options): self
    {
        $clone = clone $this;
        $clone->args = array_replace($clone->args, $options);

        return $clone;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return list<string>
     */
    public function getPostTypes(): array
    {
        return $this->postTypes;
    }

    /**
     * @param array<string, mixed> $defaults
     *
     * @return array<string, mixed>
     */
    public function toArray(array $defaults = []): array
    {
        $defaultLabels = is_array($defaults['labels'] ?? null) ? $defaults['labels'] : [];
        unset($defaults['labels']);

        $args = array_replace(
            ['hierarchical' => true, 'show_admin_column' => true],
            $defaults,
            $this->args,
        );
        // Rewrite arrays replace wholesale; only the slug default is injected when missing.
        $rewrite = $args['rewrite'] ?? [];
        if (is_array($rewrite)) {
            $args['rewrite'] = array_replace(['slug' => $this->slug], $rewrite);
        }
        $args['labels'] ??= array_replace(
            LabelGenerator::taxonomyLabels($this->singular, $this->plural),
            $defaultLabels,
            $this->labels,
        );

        return $args;
    }

    private function withArg(string $key, mixed $value): self
    {
        $clone = clone $this;
        $clone->args[$key] = $value;

        return $clone;
    }
}
