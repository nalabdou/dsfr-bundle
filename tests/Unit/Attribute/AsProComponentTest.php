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

use Nalabdou\Dsfr\Attribute\AsProComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AsProComponent::class)]
final class AsProComponentTest extends TestCase
{
    #[Test]
    public function classIsFinal(): void
    {
        self::assertTrue((new \ReflectionClass(AsProComponent::class))->isFinal());
    }

    #[Test]
    public function classIsAnAttribute(): void
    {
        $attributes = (new \ReflectionClass(AsProComponent::class))
            ->getAttributes(\Attribute::class);

        self::assertCount(1, $attributes);
    }

    #[Test]
    public function attributeTargetsClassOnly(): void
    {
        /** @var \Attribute $meta */
        $meta = (new \ReflectionClass(AsProComponent::class))
            ->getAttributes(\Attribute::class)[0]
            ->newInstance();

        self::assertSame(\Attribute::TARGET_CLASS, $meta->flags);
    }

    #[Test]
    public function attributeIsInstantiableWithNoArguments(): void
    {
        $instance = new AsProComponent();

        self::assertInstanceOf(AsProComponent::class, $instance);
    }

    #[Test]
    public function attributeCanBeAppliedToAClass(): void
    {
        // Verify no error is thrown when reading the attribute from a class
        // that carries it (simulated via anonymous class + manual Reflection)
        $annotated = new #[AsProComponent] class {};

        $attributes = (new \ReflectionClass($annotated))->getAttributes(AsProComponent::class);

        self::assertCount(1, $attributes);
    }

    #[Test]
    public function attributeInstanceRetrievedFromClassIsCorrectType(): void
    {
        $annotated = new #[AsProComponent] class {};

        $instance = (new \ReflectionClass($annotated))
            ->getAttributes(AsProComponent::class)[0]
            ->newInstance();

        self::assertInstanceOf(AsProComponent::class, $instance);
    }

    #[Test]
    public function attributeCannotBeAppliedToAFunction(): void
    {
        // TARGET_CLASS means flags do NOT include TARGET_FUNCTION
        /** @var \Attribute $meta */
        $meta = (new \ReflectionClass(AsProComponent::class))
            ->getAttributes(\Attribute::class)[0]
            ->newInstance();

        self::assertSame(0, $meta->flags & \Attribute::TARGET_FUNCTION);
    }

    #[Test]
    public function attributeCannotBeAppliedToAMethod(): void
    {
        /** @var \Attribute $meta */
        $meta = (new \ReflectionClass(AsProComponent::class))
            ->getAttributes(\Attribute::class)[0]
            ->newInstance();

        self::assertSame(0, $meta->flags & \Attribute::TARGET_METHOD);
    }

    #[Test]
    public function attributeCannotBeAppliedToAProperty(): void
    {
        /** @var \Attribute $meta */
        $meta = (new \ReflectionClass(AsProComponent::class))
            ->getAttributes(\Attribute::class)[0]
            ->newInstance();

        self::assertSame(0, $meta->flags & \Attribute::TARGET_PROPERTY);
    }

    #[Test]
    public function attributeCannotBeAppliedToAParameter(): void
    {
        /** @var \Attribute $meta */
        $meta = (new \ReflectionClass(AsProComponent::class))
            ->getAttributes(\Attribute::class)[0]
            ->newInstance();

        self::assertSame(0, $meta->flags & \Attribute::TARGET_PARAMETER);
    }
}
