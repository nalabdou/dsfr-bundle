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

use Nalabdou\Dsfr\Normalizer\String\HtmlIdNormalizer;
use Nalabdou\Dsfr\Tests\Fixtures\Trait\WithOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(HtmlIdNormalizer::class)]
final class HtmlIdNormalizerTest extends TestCase
{
    use WithOptions;

    #[Test]
    #[DataProvider('provideValidIds')]
    public function itReturnsValidId(string $input, string $expected): void
    {
        $closure = HtmlIdNormalizer::normalize();

        self::assertSame($expected, ($closure)($this->options(), $input));
    }

    public static function provideValidIds(): \Generator
    {
        yield 'simple alpha' => ['modal', 'modal'];
        yield 'alpha with numbers' => ['modal1', 'modal1'];
        yield 'with dash' => ['my-modal', 'my-modal'];
        yield 'with underscore' => ['my_modal', 'my_modal'];
        yield 'with colon' => ['nav:main', 'nav:main'];
        yield 'with dot' => ['nav.main', 'nav.main'];
        yield 'uppercase' => ['MyModal', 'MyModal'];
        yield 'mixed' => ['Modal-1_a:b.c', 'Modal-1_a:b.c'];
        yield 'with leading spaces' => ['  modal  ', 'modal'];
    }

    #[Test]
    public function itThrowsForEmptyString(): void
    {
        $closure = HtmlIdNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('cannot be empty');

        ($closure)($this->options(), '');
    }

    #[Test]
    public function itThrowsForWhitespaceOnly(): void
    {
        $closure = HtmlIdNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('cannot be empty');

        ($closure)($this->options(), '   ');
    }

    #[Test]
    public function itThrowsForInternalSpace(): void
    {
        $closure = HtmlIdNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('cannot contain spaces');

        ($closure)($this->options(), 'my modal');
    }

    #[Test]
    public function itThrowsForTabCharacter(): void
    {
        $closure = HtmlIdNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);

        ($closure)($this->options(), "my\tmodal");
    }

    #[Test]
    #[DataProvider('provideInvalidIds')]
    public function itThrowsForInvalidId(string $input): void
    {
        $closure = HtmlIdNormalizer::normalize();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/is invalid|cannot contain spaces/');

        ($closure)($this->options(), $input);
    }

    public static function provideInvalidIds(): \Generator
    {
        yield 'starts with number' => ['1modal'];
        yield 'starts with dash' => ['-modal'];
        yield 'starts with dot' => ['.modal'];
        yield 'starts with colon' => [':modal'];
        yield 'starts with underscore' => ['_modal'];
        yield 'contains slash' => ['my/modal'];
        yield 'contains hash' => ['my#modal'];
        yield 'contains at' => ['my@modal'];
        yield 'contains bracket' => ['my[modal]'];
        yield 'contains ampersand' => ['my&modal'];
    }

    #[Test]
    public function normalizeReturnsClosure(): void
    {
        self::assertInstanceOf(\Closure::class, HtmlIdNormalizer::normalize());
    }
}
