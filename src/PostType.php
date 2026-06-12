<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\PostType;

use function array_replace;
use function array_values;
use function is_array;

final class PostType implements PostTypeInterface
{
    /** @var array<string, mixed> */
    private array $args = [];
    /** @var array<string, string> */
    private array $labels = [];
    /** @var list<string> */
    private array $taxonomies = [];
    /** @var list<string> */
    private array $taxonomyFilters = [];
    private ?Columns $columns = null;

    /**
     * @param lowercase-string&non-empty-string $name
     * @param string                            $singular
     * @param string                            $plural
     * @param string                            $slug
     */
    private function __construct(
        private readonly string $name,
        private readonly string $singular,
        private readonly string $plural,
        private readonly string $slug,
    ) {
    }

    /**
     * @param lowercase-string&non-empty-string $name     the post type key, as register_post_type() requires it
     * @param string                            $singular
     * @param string                            $plural
     * @param ?string                           $slug
     */
    public static function create(string $name, string $singular, string $plural, ?string $slug = null): self
    {
        return new self($name, $singular, $plural, $slug ?? $name);
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

    public function withShowInMenu(bool|string $showInMenu = true): self
    {
        return $this->withArg('show_in_menu', $showInMenu);
    }

    public function withShowInAdminBar(bool $showInAdminBar = true): self
    {
        return $this->withArg('show_in_admin_bar', $showInAdminBar);
    }

    public function withShowInRest(bool $showInRest = true): self
    {
        return $this->withArg('show_in_rest', $showInRest);
    }

    public function withHasArchive(bool|string $hasArchive = true): self
    {
        return $this->withArg('has_archive', $hasArchive);
    }

    public function withSupports(string ...$features): self
    {
        return $this->withArg('supports', array_values($features));
    }

    public function withMenuIcon(string $menuIcon): self
    {
        return $this->withArg('menu_icon', $menuIcon);
    }

    public function withMenuPosition(int $menuPosition): self
    {
        return $this->withArg('menu_position', $menuPosition);
    }

    /**
     * @param array<string, mixed>|bool $rewrite
     */
    public function withRewrite(array|bool $rewrite): self
    {
        return $this->withArg('rewrite', $rewrite);
    }

    public function withTaxonomies(string ...$taxonomies): self
    {
        $clone = clone $this;
        $clone->taxonomies = [...$clone->taxonomies, ...array_values($taxonomies)];

        return $clone;
    }

    /**
     * Opt in to taxonomy dropdown filters on the admin list table.
     *
     * @param string ...$taxonomies
     */
    public function withTaxonomyFilters(string ...$taxonomies): self
    {
        $clone = clone $this;
        $clone->taxonomyFilters = [...$clone->taxonomyFilters, ...array_values($taxonomies)];

        return $clone;
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

    public function withColumns(Columns $columns): self
    {
        $clone = clone $this;
        $clone->columns = $columns;

        return $clone;
    }

    /**
     * Escape hatch for any register_post_type() argument without a typed
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
    public function getTaxonomies(): array
    {
        return $this->taxonomies;
    }

    /**
     * @return list<string>
     */
    public function getTaxonomyFilters(): array
    {
        return $this->taxonomyFilters;
    }

    public function getColumns(): ?Columns
    {
        return $this->columns;
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

        $args = array_replace(['public' => true], $defaults, $this->args);
        // Rewrite arrays replace wholesale; only the slug default is injected when missing.
        $rewrite = $args['rewrite'] ?? [];
        if (is_array($rewrite)) {
            $args['rewrite'] = array_replace(['slug' => $this->slug], $rewrite);
        }
        $args['labels'] ??= array_replace(
            LabelGenerator::postTypeLabels($this->singular, $this->plural),
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
