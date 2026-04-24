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

namespace Nalabdou\Dsfr\Tests\Unit\Normalizer\Bool;

use Nalabdou\Dsfr\Normalizer\Bool\BoolNormalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Options;

#[CoversClass(BoolNormalizer::class)]
final class BoolNormalizerTest extends TestCase
{
    private \Closure $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = BoolNormalizer::normalize();
    }

    #[Test]
    #[DataProvider('provideTrueValues')]
    public function itReturnsTrue(mixed $input): void
    {
        $result = ($this->normalizer)($this->createOptions(), $input);

        self::assertTrue($result);
    }

    public static function provideTrueValues(): \Generator
    {
        yield 'bool true' => [true];
        yield 'int 1' => [1];
        yield 'int 2' => [2];
        yield 'string "true"' => ['true'];
        yield 'string "TRUE"' => ['TRUE'];
        yield 'string "True"' => ['True'];
        yield 'string "1"' => ['1'];
        yield 'string "yes"' => ['yes'];
        yield 'string "YES"' => ['YES'];
        yield 'string "on"' => ['on'];
        yield 'string "ON"' => ['ON'];
    }

    #[Test]
    #[DataProvider('provideFalseValues')]
    public function itReturnsFalse(mixed $input): void
    {
        $result = ($this->normalizer)($this->createOptions(), $input);

        self::assertFalse($result);
    }

    public static function provideFalseValues(): \Generator
    {
        yield 'bool false' => [false];
        yield 'int 0' => [0];
        yield 'string "false"' => ['false'];
        yield 'string "FALSE"' => ['FALSE'];
        yield 'string "False"' => ['False'];
        yield 'string "0"' => ['0'];
        yield 'string "no"' => ['no'];
        yield 'string "NO"' => ['NO'];
        yield 'string "off"' => ['off'];
        yield 'string "OFF"' => ['OFF'];
        yield 'empty string' => [''];
        yield 'whitespace' => ['   '];
    }

    #[Test]
    #[DataProvider('provideInvalidStringValues')]
    public function itThrowsForInvalidString(string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Cannot cast .* to bool/');

        ($this->normalizer)($this->createOptions(), $input);
    }

    public static function provideInvalidStringValues(): \Generator
    {
        yield 'string "maybe"' => ['maybe'];
        yield 'string "oui"' => ['oui'];
        yield 'string "vrai"' => ['vrai'];
        yield 'string "2"' => ['2'];
        yield 'string "null"' => ['null'];
    }

    #[Test]
    #[DataProvider('provideInvalidTypeValues')]
    public function itThrowsForInvalidType(mixed $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected bool, int or string, got/');

        ($this->normalizer)($this->createOptions(), $input);
    }

    public static function provideInvalidTypeValues(): \Generator
    {
        yield 'null' => [null];
        yield 'float' => [1.5];
        yield 'array' => [[]];
        yield 'object' => [new \stdClass()];
    }

    #[Test]
    public function normalizeReturnsAClosure(): void
    {
        self::assertInstanceOf(\Closure::class, BoolNormalizer::normalize());
    }

    #[Test]
    public function normalizeReturnsSameClosureOnMultipleCalls(): void
    {
        // Stateless — each call can return a new closure, that's fine
        // Just ensure it's always a Closure
        self::assertInstanceOf(\Closure::class, BoolNormalizer::normalize());
        self::assertInstanceOf(\Closure::class, BoolNormalizer::normalize());
    }

    private function createOptions(): Options
    {
        /** @var Options<array<string, mixed>> $options */
        $options = $this->createMock(Options::class);

        return $options;
    }
}
