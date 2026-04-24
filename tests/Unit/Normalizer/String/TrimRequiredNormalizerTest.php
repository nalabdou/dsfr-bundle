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

use Nalabdou\Dsfr\Normalizer\String\TrimRequiredNormalizer;
use Nalabdou\Dsfr\Tests\Fixtures\Trait\WithOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TrimRequiredNormalizer::class)]
final class TrimRequiredNormalizerTest extends TestCase
{
    use WithOptions;

    #[Test]
    #[DataProvider('provideValidValues')]
    public function itReturnsTrimmedValue(string $input, string $expected): void
    {
        $closure = TrimRequiredNormalizer::normalize();

        self::assertSame($expected, ($closure)($this->options(), $input));
    }

    public static function provideValidValues(): \Generator
    {
        yield 'simple value' => ['hello', 'hello'];
        yield 'leading spaces' => ['  hello', 'hello'];
        yield 'trailing spaces' => ['hello  ', 'hello'];
        yield 'both sides' => ['  hello  ', 'hello'];
        yield 'tabs' => ["\thello\t", 'hello'];
        yield 'newlines' => ["\nhello\n", 'hello'];
        yield 'internal spaces kept' => ['  hello world  ', 'hello world'];
        yield 'single char' => ['a', 'a'];
        yield 'single char spaces' => ['  a  ', 'a'];
    }

    #[Test]
    public function itThrowsForEmptyString(): void
    {
        $closure = TrimRequiredNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('"value" cannot be an empty string');

        ($closure)($this->options(), '');
    }

    #[Test]
    public function itThrowsForWhitespaceOnly(): void
    {
        $closure = TrimRequiredNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('"value" cannot be an empty string');

        ($closure)($this->options(), '   ');
    }

    #[Test]
    public function itThrowsForTabOnly(): void
    {
        $closure = TrimRequiredNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);

        ($closure)($this->options(), "\t");
    }

    #[Test]
    public function itThrowsForNewlineOnly(): void
    {
        $closure = TrimRequiredNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);

        ($closure)($this->options(), "\n");
    }

    #[Test]
    public function itIncludesPropNameInExceptionMessage(): void
    {
        $closure = TrimRequiredNormalizer::normalize('label');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('"label" cannot be an empty string');

        ($closure)($this->options(), '');
    }

    #[Test]
    #[DataProvider('providePropNames')]
    public function itUsesCustomPropNameInMessage(string $propName): void
    {
        $closure = TrimRequiredNormalizer::normalize($propName);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('"%s" cannot be an empty string', $propName));

        ($closure)($this->options(), '');
    }

    public static function providePropNames(): \Generator
    {
        yield 'label' => ['label'];
        yield 'title' => ['title'];
        yield 'description' => ['description'];
        yield 'dismissLabel' => ['dismissLabel'];
    }

    #[Test]
    public function normalizeReturnsClosure(): void
    {
        self::assertInstanceOf(\Closure::class, TrimRequiredNormalizer::normalize());
    }

    #[Test]
    public function normalizeReturnsClosureWithCustomProp(): void
    {
        self::assertInstanceOf(\Closure::class, TrimRequiredNormalizer::normalize('label'));
    }
}
