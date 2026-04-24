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

namespace Nalabdou\Dsfr\Tests\Unit\Normalizer\String;

use Nalabdou\Dsfr\Normalizer\String\DsfrIconNormalizer;
use Nalabdou\Dsfr\Tests\Fixtures\Trait\WithOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DsfrIconNormalizer::class)]
final class DsfrIconNormalizerTest extends TestCase
{
    use WithOptions;

    #[Test]
    #[DataProvider('provideValidIcons')]
    public function itReturnsValidIconClass(string $input, string $expected): void
    {
        $closure = DsfrIconNormalizer::normalize();

        self::assertSame($expected, ($closure)($this->options(), $input));
    }

    public static function provideValidIcons(): \Generator
    {
        yield 'simple icon' => ['fr-icon-calendar-line', 'fr-icon-calendar-line'];
        yield 'icon with numbers' => ['fr-icon-h1-line', 'fr-icon-h1-line'];
        yield 'icon with trim' => ['  fr-icon-user-line  ', 'fr-icon-user-line'];
        yield 'icon single word' => ['fr-icon-add', 'fr-icon-add'];
        yield 'icon many segments' => ['fr-icon-arrow-right-line', 'fr-icon-arrow-right-line'];
    }

    #[Test]
    public function itReturnsNullForNullInput(): void
    {
        $closure = DsfrIconNormalizer::normalize();

        self::assertNull(($closure)($this->options(), null));
    }

    #[Test]
    public function itReturnsNullForEmptyString(): void
    {
        $closure = DsfrIconNormalizer::normalize();

        self::assertNull(($closure)($this->options(), ''));
    }

    #[Test]
    public function itReturnsNullForWhitespaceOnly(): void
    {
        $closure = DsfrIconNormalizer::normalize();

        self::assertNull(($closure)($this->options(), '   '));
    }

    #[Test]
    #[DataProvider('provideInvalidPrefixIcons')]
    public function itThrowsForMissingPrefix(string $input): void
    {
        $closure = DsfrIconNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/must start with "fr-icon-"/');

        ($closure)($this->options(), $input);
    }

    public static function provideInvalidPrefixIcons(): \Generator
    {
        yield 'no prefix' => ['calendar-line'];
        yield 'wrong prefix' => ['icon-calendar-line'];
        yield 'dsfr prefix only' => ['dsfr-icon-calendar'];
        yield 'ri prefix' => ['ri-calendar-line'];
        yield 'fa prefix' => ['fa-calendar'];
    }

    #[Test]
    #[DataProvider('provideInvalidCharacterIcons')]
    public function itThrowsForInvalidCharacters(string $input): void
    {
        $closure = DsfrIconNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/contains invalid characters/');

        ($closure)($this->options(), $input);
    }

    public static function provideInvalidCharacterIcons(): \Generator
    {
        yield 'uppercase letters' => ['fr-icon-Calendar-Line'];
        yield 'underscore' => ['fr-icon-calendar_line'];
        yield 'space inside' => ['fr-icon-calendar line'];
        yield 'dot' => ['fr-icon-calendar.line'];
        yield 'slash' => ['fr-icon-calendar/line'];
    }

    #[Test]
    public function nullableReturnsNullForNull(): void
    {
        $closure = DsfrIconNormalizer::nullable();

        self::assertNull(($closure)($this->options(), null));
    }

    #[Test]
    public function nullableResolvesValidIcon(): void
    {
        $closure = DsfrIconNormalizer::nullable();

        self::assertSame('fr-icon-calendar-line', ($closure)($this->options(), 'fr-icon-calendar-line'));
    }

    #[Test]
    public function nullableThrowsForInvalidIcon(): void
    {
        $closure = DsfrIconNormalizer::nullable();

        $this->expectException(\InvalidArgumentException::class);

        ($closure)($this->options(), 'invalid-icon');
    }

    #[Test]
    public function normalizeReturnsClosure(): void
    {
        self::assertInstanceOf(\Closure::class, DsfrIconNormalizer::normalize());
    }

    #[Test]
    public function nullableReturnsClosure(): void
    {
        self::assertInstanceOf(\Closure::class, DsfrIconNormalizer::nullable());
    }
}
