<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType\TestDouble;

use Psr\Container\ContainerInterface;

use function array_key_exists;

final class TestContainer implements ContainerInterface
{
    /**
     * @param array<string, mixed> $entries
     */
    public function __construct(private readonly array $entries)
    {
    }

    public function get(string $id): mixed
    {
        return $this->entries[$id] ?? null;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->entries);
    }
}
