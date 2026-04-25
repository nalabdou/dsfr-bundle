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

namespace Nalabdou\Dsfr\Tests\Unit\Normalizer\Enum;

use Nalabdou\Dsfr\Normalizer\Enum\EnumNormalizer;
use Nalabdou\Dsfr\Tests\Fixtures\Enum\DummyIntEnum;
use Nalabdou\Dsfr\Tests\Fixtures\Enum\DummyStringEnum;
use Nalabdou\Dsfr\Tests\Fixtures\Trait\WithOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(EnumNormalizer::class)]
final class EnumNormalizerTest extends TestCase
{
    use WithOptions;

    #[Test]
    public function itReturnsEnumInstanceFromString(): void
    {
        $closure = EnumNormalizer::with(DummyStringEnum::class);

        $result = ($closure)($this->options(), 'foo');

        self::assertSame(DummyStringEnum::Foo, $result);
    }

    #[Test]
    public function itReturnsEnumInstanceFromInt(): void
    {
        $closure = EnumNormalizer::with(DummyIntEnum::class);

        $result = ($closure)($this->options(), 1);

        self::assertSame(DummyIntEnum::One, $result);
    }

    #[Test]
    public function itReturnsSameInstanceWhenAlreadyEnum(): void
    {
        $closure = EnumNormalizer::with(DummyStringEnum::class);

        $result = ($closure)($this->options(), DummyStringEnum::Bar);

        self::assertSame(DummyStringEnum::Bar, $result);
    }

    #[Test]
    #[DataProvider('provideValidStringEnumValues')]
    public function itResolvesAllValidStringValues(string $value, DummyStringEnum $expected): void
    {
        $closure = EnumNormalizer::with(DummyStringEnum::class);

        self::assertSame($expected, ($closure)($this->options(), $value));
    }

    public static function provideValidStringEnumValues(): \Generator
    {
        yield '"foo"' => ['foo', DummyStringEnum::Foo];
        yield '"bar"' => ['bar', DummyStringEnum::Bar];
        yield '"baz"' => ['baz', DummyStringEnum::Baz];
    }

    #[Test]
    public function itThrowsForInvalidStringValue(): void
    {
        $closure = EnumNormalizer::with(DummyStringEnum::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/is not a valid DummyStringEnum/');
        $this->expectExceptionMessageMatches('/Accepted: \[foo, bar, baz\]/');

        ($closure)($this->options(), 'invalid');
    }

    #[Test]
    public function itThrowsForInvalidIntValue(): void
    {
        $closure = EnumNormalizer::with(DummyIntEnum::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/is not a valid DummyIntEnum/');

        ($closure)($this->options(), 99);
    }

    #[Test]
    public function itThrowsForInvalidType(): void
    {
        $closure = EnumNormalizer::with(DummyStringEnum::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected string\|int\|DummyStringEnum, got/');

        ($closure)($this->options(), ['not', 'a', 'string']);
    }

    #[Test]
    public function itThrowsForNullInNonNullable(): void
    {
        $closure = EnumNormalizer::with(DummyStringEnum::class);

        $this->expectException(\InvalidArgumentException::class);

        ($closure)($this->options(), null);
    }

    #[Test]
    public function itThrowsForFloat(): void
    {
        $closure = EnumNormalizer::with(DummyStringEnum::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected string\|int\|DummyStringEnum, got float/');

        ($closure)($this->options(), 1.5);
    }

    #[Test]
    public function nullableReturnsNullForNullInput(): void
    {
        $closure = EnumNormalizer::withNullable(DummyStringEnum::class);

        self::assertNull(($closure)($this->options(), null));
    }

    #[Test]
    public function nullableResolvesValidString(): void
    {
        $closure = EnumNormalizer::withNullable(DummyStringEnum::class);

        self::assertSame(DummyStringEnum::Foo, ($closure)($this->options(), 'foo'));
    }

    #[Test]
    public function nullableResolvesEnumInstance(): void
    {
        $closure = EnumNormalizer::withNullable(DummyStringEnum::class);

        self::assertSame(DummyStringEnum::Bar, ($closure)($this->options(), DummyStringEnum::Bar));
    }

    #[Test]
    public function nullableThrowsForInvalidString(): void
    {
        $closure = EnumNormalizer::withNullable(DummyStringEnum::class);

        $this->expectException(\InvalidArgumentException::class);

        ($closure)($this->options(), 'invalid');
    }

    #[Test]
    public function withReturnsAClosure(): void
    {
        self::assertInstanceOf(\Closure::class, EnumNormalizer::with(DummyStringEnum::class));
    }

    #[Test]
    public function withNullableReturnsAClosure(): void
    {
        self::assertInstanceOf(\Closure::class, EnumNormalizer::withNullable(DummyStringEnum::class));
    }
}
