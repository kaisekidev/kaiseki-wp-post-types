<?php

declare(strict_types=1);

namespace Kaiseki\Test\Unit\WordPress\PostType;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Kaiseki\Test\Unit\WordPress\PostType\TestDouble\DummyPostTypeProvider;
use Kaiseki\Test\Unit\WordPress\PostType\TestDouble\DummyTaxonomyProvider;
use Kaiseki\Test\Unit\WordPress\PostType\TestDouble\TestContainer;
use Kaiseki\WordPress\PostType\PostType;
use Kaiseki\WordPress\PostType\PostTypeRegistryFactory;
use Kaiseki\WordPress\PostType\Taxonomy;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

final class PostTypeRegistryFactoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testFactoryResolvesProvidersAndDefaultsFromConfig(): void
    {
        $container = new TestContainer([
            'config' => [
                'post_type' => [
                    'post_types' => [DummyPostTypeProvider::class],
                    'taxonomies' => [DummyTaxonomyProvider::class],
                    'default_post_type_options' => ['show_in_rest' => true, 0 => 'dropped'],
                    'default_taxonomy_options' => ['show_in_rest' => true],
                ],
            ],
            DummyPostTypeProvider::class => new DummyPostTypeProvider([
                PostType::create('book', 'Book', 'Books'),
            ]),
            DummyTaxonomyProvider::class => new DummyTaxonomyProvider([
                Taxonomy::create('genre', 'Genre', 'Genres'),
            ]),
        ]);
        /** @var list<array{string, array<string, mixed>}> $registered */
        $registered = [];
        Functions\when('register_post_type')->alias(
            static function (string $name, array $args) use (&$registered): void {
                $registered[] = [$name, $args];
            }
        );
        Functions\when('register_taxonomy')->alias(
            static function (string $name, array $objectTypes, array $args) use (&$registered): void {
                $registered[] = [$name, $args];
            }
        );

        $registry = (new PostTypeRegistryFactory())($container);
        $registry->register();

        self::assertCount(2, $registered);
        self::assertSame('genre', $registered[0][0]);
        self::assertTrue($registered[0][1]['show_in_rest']);
        self::assertSame('book', $registered[1][0]);
        self::assertTrue($registered[1][1]['show_in_rest']);
        self::assertArrayNotHasKey(0, $registered[1][1]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
