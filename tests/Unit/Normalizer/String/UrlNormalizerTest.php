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

use Nalabdou\Dsfr\Normalizer\String\UrlNormalizer;
use Nalabdou\Dsfr\Tests\Fixtures\Trait\WithOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UrlNormalizer::class)]
final class UrlNormalizerTest extends TestCase
{
    use WithOptions;

    #[Test]
    #[DataProvider('provideValidUrls')]
    public function itReturnsValidUrl(string $input, string $expected): void
    {
        $closure = UrlNormalizer::normalize();

        self::assertSame($expected, ($closure)($this->options(), $input));
    }

    public static function provideValidUrls(): \Generator
    {
        yield 'empty string' => ['', ''];
        yield 'relative path' => ['/path/to/page', '/path/to/page'];
        yield 'relative with query' => ['/path?foo=bar', '/path?foo=bar'];
        yield 'relative with fragment' => ['/path#section', '/path#section'];
        yield 'https' => ['https://example.com', 'https://example.com'];
        yield 'https with path' => ['https://example.com/path', 'https://example.com/path'];
        yield 'http' => ['http://example.com', 'http://example.com'];
        yield 'mailto' => ['mailto:contact@example.com', 'mailto:contact@example.com'];
        yield 'tel' => ['tel:+33123456789', 'tel:+33123456789'];
        yield 'anchor' => ['#section', '#section'];
        yield 'anchor with id' => ['#my-modal', '#my-modal'];
        yield 'leading spaces trimmed' => ['  /path  ', '/path'];
        yield 'https uppercase scheme' => ['HTTPS://example.com', 'HTTPS://example.com'];
        yield 'HTTP uppercase scheme' => ['HTTP://example.com', 'HTTP://example.com'];
    }

    #[Test]
    #[DataProvider('provideInvalidUrls')]
    public function itThrowsForInvalidUrl(string $input): void
    {
        $closure = UrlNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Invalid href/');

        ($closure)($this->options(), $input);
    }

    public static function provideInvalidUrls(): \Generator
    {
        yield 'no scheme no slash' => ['example.com'];
        yield 'ftp scheme' => ['ftp://example.com'];
        yield 'javascript scheme' => ['javascript:alert(1)'];
        yield 'data scheme' => ['data:text/html,<h1>test</h1>'];
        yield 'relative no slash' => ['path/to/page'];
        yield 'double slash no scheme' => ['//example.com'];
        yield 'plain word' => ['hello'];
    }

    #[Test]
    public function itReturnsEmptyForWhitespaceOnly(): void
    {
        $closure = UrlNormalizer::normalize();

        self::assertSame('', ($closure)($this->options(), '   '));
    }

    #[Test]
    public function normalizeReturnsClosure(): void
    {
        self::assertInstanceOf(\Closure::class, UrlNormalizer::normalize());
    }
}
