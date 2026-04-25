<?php

declare(strict_types=1);

/*
 *  This file is part of the nalabdou/dsfr-bundle package.
 *
 *  (c) Nadim AL ABDOU <nadim.alabdou@gmail.com>
 *
 *  For the full copyright and license information, please view
 *  the LICENSE file that was distributed with this source code.
 *
 */

namespace Nalabdou\Dsfr\Tests\Attribute;

use Nalabdou\Dsfr\Attribute\AsCommunityComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AsCommunityComponent::class)]
final class AsCommunityComponentTest extends TestCase
{
    #[Test]
    public function classIsFinal(): void
    {
        self::assertTrue((new \ReflectionClass(AsCommunityComponent::class))->isFinal());
    }

    #[Test]
    public function classIsAnAttribute(): void
    {
        $attributes = (new \ReflectionClass(AsCommunityComponent::class))
            ->getAttributes(\Attribute::class);

        self::assertCount(1, $attributes);
    }

    #[Test]
    public function attributeTargetsClassOnly(): void
    {
        /** @var \Attribute $meta */
        $meta = (new \ReflectionClass(AsCommunityComponent::class))
            ->getAttributes(\Attribute::class)[0]
            ->newInstance();

        self::assertSame(\Attribute::TARGET_CLASS, $meta->flags);
    }

    #[Test]
    public function attributeIsInstantiableWithNoArguments(): void
    {
        $instance = new AsCommunityComponent();

        self::assertInstanceOf(AsCommunityComponent::class, $instance);
    }

    #[Test]
    public function attributeCanBeAppliedToAClass(): void
    {
        // Verify no error is thrown when reading the attribute from a class
        // that carries it (simulated via anonymous class + manual Reflection)
        $annotated = new #[AsCommunityComponent] class {};

        $attributes = (new \ReflectionClass($annotated))->getAttributes(AsCommunityComponent::class);

        self::assertCount(1, $attributes);
    }

    #[Test]
    public function attributeInstanceRetrievedFromClassIsCorrectType(): void
    {
        $annotated = new #[AsCommunityComponent] class {};

        $instance = (new \ReflectionClass($annotated))
            ->getAttributes(AsCommunityComponent::class)[0]
            ->newInstance();

        self::assertInstanceOf(AsCommunityComponent::class, $instance);
    }

    #[Test]
    public function attributeCannotBeAppliedToAFunction(): void
    {
        // TARGET_CLASS means flags do NOT include TARGET_FUNCTION
        /** @var \Attribute $meta */
        $meta = (new \ReflectionClass(AsCommunityComponent::class))
            ->getAttributes(\Attribute::class)[0]
            ->newInstance();

        self::assertSame(0, $meta->flags & \Attribute::TARGET_FUNCTION);
    }

    #[Test]
    public function attributeCannotBeAppliedToAMethod(): void
    {
        /** @var \Attribute $meta */
        $meta = (new \ReflectionClass(AsCommunityComponent::class))
            ->getAttributes(\Attribute::class)[0]
            ->newInstance();

        self::assertSame(0, $meta->flags & \Attribute::TARGET_METHOD);
    }

    #[Test]
    public function attributeCannotBeAppliedToAProperty(): void
    {
        /** @var \Attribute $meta */
        $meta = (new \ReflectionClass(AsCommunityComponent::class))
            ->getAttributes(\Attribute::class)[0]
            ->newInstance();

        self::assertSame(0, $meta->flags & \Attribute::TARGET_PROPERTY);
    }

    #[Test]
    public function attributeCannotBeAppliedToAParameter(): void
    {
        /** @var \Attribute $meta */
        $meta = (new \ReflectionClass(AsCommunityComponent::class))
            ->getAttributes(\Attribute::class)[0]
            ->newInstance();

        self::assertSame(0, $meta->flags & \Attribute::TARGET_PARAMETER);
    }
}
