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

namespace Nalabdou\Dsfr\Tests\Transformer;

use Nalabdou\Dsfr\Transformer\PropTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PropTransformer::class)]
final class PropTransformerTest extends TestCase
{
    protected function setUp(): void
    {
        PropTransformer::clearCache();
    }

    #[Test]
    #[DataProvider('htmlAttributeProvider')]
    public function htmlAttributeConvertsCorrectly(string $input, string $expected): void
    {
        self::assertSame($expected, PropTransformer::htmlAttribute($input));
    }

    public static function htmlAttributeProvider(): \Generator
    {
        yield 'single word lowercase' => ['external', 'external'];
        yield 'two words camelCase' => ['iconPosition', 'icon-position'];
        yield 'two words camelCase #2' => ['dismissLabel', 'dismiss-label'];
        yield 'three words camelCase' => ['myCustomProp', 'my-custom-prop'];
        yield 'starts with uppercase' => ['AriaLabel', '-aria-label'];
        yield 'consecutive uppercase' => ['ariaHTML', 'aria-h-t-m-l'];
        yield 'already lowercase' => ['label', 'label'];
        yield 'single uppercase letter' => ['X', '-x'];
    }

    #[Test]
    public function htmlAttributeOutputIsLowercase(): void
    {
        $result = PropTransformer::htmlAttribute('someValue');

        self::assertSame(\strtolower($result), $result);
    }

    #[Test]
    public function htmlAttributeReturnsSameResultOnRepeatedCalls(): void
    {
        $first = PropTransformer::htmlAttribute('iconPosition');
        $second = PropTransformer::htmlAttribute('iconPosition');

        self::assertSame($first, $second);
    }

    #[Test]
    public function clearCacheAllowsFreshResolution(): void
    {
        $before = PropTransformer::htmlAttribute('iconPosition');
        PropTransformer::clearCache();
        $after = PropTransformer::htmlAttribute('iconPosition');

        // Same result — cache is an optimisation, not a state change
        self::assertSame($before, $after);
    }

    #[Test]
    public function clearCacheDoesNotAffectFutureResults(): void
    {
        PropTransformer::htmlAttribute('dismissLabel');
        PropTransformer::clearCache();

        self::assertSame('dismiss-label', PropTransformer::htmlAttribute('dismissLabel'));
    }

    #[Test]
    public function htmlAttributesReturnsMapOfPropToHtmlAttribute(): void
    {
        $result = PropTransformer::htmlAttributes(['iconPosition', 'dismissLabel', 'external']);

        self::assertSame([
            'iconPosition' => 'icon-position',
            'dismissLabel' => 'dismiss-label',
            'external' => 'external',
        ], $result);
    }

    #[Test]
    public function htmlAttributesWithEmptyListReturnsEmptyArray(): void
    {
        self::assertSame([], PropTransformer::htmlAttributes([]));
    }

    #[Test]
    public function htmlAttributesKeysAreOriginalPropNames(): void
    {
        $props = ['myProp', 'anotherProp'];
        $result = PropTransformer::htmlAttributes($props);

        self::assertArrayHasKey('myProp', $result);
        self::assertArrayHasKey('anotherProp', $result);
    }

    #[Test]
    public function htmlAttributesValuesAreKebabCased(): void
    {
        $result = PropTransformer::htmlAttributes(['myProp', 'anotherProp']);

        self::assertSame('my-prop', $result['myProp']);
        self::assertSame('another-prop', $result['anotherProp']);
    }

    #[Test]
    public function htmlAttributesWithSinglePropReturnsSingleEntry(): void
    {
        $result = PropTransformer::htmlAttributes(['isDisabled']);

        self::assertCount(1, $result);
        self::assertSame('is-disabled', $result['isDisabled']);
    }

    #[Test]
    #[DataProvider('phpPropProvider')]
    public function phpPropConvertsCorrectly(string $input, string $expected): void
    {
        self::assertSame($expected, PropTransformer::phpProp($input));
    }

    public static function phpPropProvider(): \Generator
    {
        yield 'single word' => ['external', 'external'];
        yield 'two segments' => ['icon-position', 'iconPosition'];
        yield 'two segments #2' => ['dismiss-label', 'dismissLabel'];
        yield 'three segments' => ['my-custom-prop', 'myCustomProp'];
        yield 'already camelCase' => ['label', 'label'];
        yield 'single char' => ['x', 'x'];
    }

    #[Test]
    public function phpPropStartsWithLowercase(): void
    {
        $result = PropTransformer::phpProp('icon-position');

        self::assertSame(\lcfirst($result), $result);
    }

    #[Test]
    public function phpPropContainsNoHyphens(): void
    {
        $result = PropTransformer::phpProp('my-custom-prop');

        self::assertStringNotContainsString('-', $result);
    }

    #[Test]
    #[DataProvider('roundTripProvider')]
    public function htmlAttributeThenPhpPropIsIdentity(string $camelCase): void
    {
        $kebab = PropTransformer::htmlAttribute($camelCase);
        $result = PropTransformer::phpProp($kebab);

        self::assertSame($camelCase, $result);
    }

    public static function roundTripProvider(): \Generator
    {
        yield 'single word' => ['external'];
        yield 'two words' => ['iconPosition'];
        yield 'three words' => ['myCustomProp'];
        yield 'isDisabled' => ['isDisabled'];
        yield 'dismissLabel' => ['dismissLabel'];
    }

    #[Test]
    public function classIsFinal(): void
    {
        $reflection = new \ReflectionClass(PropTransformer::class);

        self::assertTrue($reflection->isFinal());
    }

    #[Test]
    public function constructorIsPrivate(): void
    {
        $constructor = (new \ReflectionClass(PropTransformer::class))->getConstructor();

        self::assertNotNull($constructor);
        self::assertTrue($constructor->isPrivate());
    }

    #[Test]
    public function constructorThrowsWhenCalledFromOutside(): void
    {
        $this->expectException(\Error::class);

        /* @phpstan-ignore-next-line */
        new PropTransformer();
    }
}
