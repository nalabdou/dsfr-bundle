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

use Nalabdou\Dsfr\Normalizer\String\TrimNormalizer;
use Nalabdou\Dsfr\Tests\Fixtures\Trait\WithOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TrimNormalizer::class)]
final class TrimNormalizerTest extends TestCase
{
    use WithOptions;

    #[Test]
    #[DataProvider('provideTrimCases')]
    public function itTrimsWhitespace(string $input, string $expected): void
    {
        $closure = TrimNormalizer::normalize();

        self::assertSame($expected, ($closure)($this->options(), $input));
    }

    public static function provideTrimCases(): \Generator
    {
        yield 'no whitespace' => ['hello', 'hello'];
        yield 'leading spaces' => ['  hello', 'hello'];
        yield 'trailing spaces' => ['hello  ', 'hello'];
        yield 'both sides' => ['  hello  ', 'hello'];
        yield 'tabs' => ["\thello\t", 'hello'];
        yield 'newlines' => ["\nhello\n", 'hello'];
        yield 'mixed whitespace' => [" \t\nhello \t\n", 'hello'];
        yield 'empty string' => ['', ''];
        yield 'whitespace only' => ['   ', ''];
        yield 'already trimmed' => ['hello world', 'hello world'];
        yield 'internal spaces kept' => ['  hello world  ', 'hello world'];
    }

    #[Test]
    public function normalizeReturnsClosure(): void
    {
        self::assertInstanceOf(\Closure::class, TrimNormalizer::normalize());
    }
}
