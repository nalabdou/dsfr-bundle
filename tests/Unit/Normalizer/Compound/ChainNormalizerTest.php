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

namespace Nalabdou\Dsfr\Tests\Unit\Normalizer\Compound;

use Nalabdou\Dsfr\Normalizer\Compound\ChainNormalizer;
use Nalabdou\Dsfr\Tests\Fixtures\Trait\WithOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Options;

#[CoversClass(ChainNormalizer::class)]
final class ChainNormalizerTest extends TestCase
{
    use WithOptions;

    #[Test]
    public function itAppliesSingleNormalizer(): void
    {
        $upper = static fn (Options $o, mixed $v): string => \strtoupper((string) $v);

        $chain = ChainNormalizer::normalize($upper);

        self::assertSame('HELLO', ($chain)($this->options(), 'hello'));
    }

    #[Test]
    public function itAppliesNormalizersInOrder(): void
    {
        $trim = static fn (Options $o, mixed $v): string => \trim((string) $v);
        $upper = static fn (Options $o, mixed $v): string => \strtoupper((string) $v);
        $excl = static fn (Options $o, mixed $v): string => $v.'!';

        $chain = ChainNormalizer::normalize($trim, $upper, $excl);

        self::assertSame('HELLO!', ($chain)($this->options(), '  hello  '));
    }

    #[Test]
    public function itPassesOutputOfEachStepAsInputToNext(): void
    {
        $log = [];

        $first = static function (Options $o, mixed $v) use (&$log): int {
            $log[] = 'first:'.$v;

            return (int) $v * 2;
        };

        $second = static function (Options $o, mixed $v) use (&$log): int {
            $log[] = 'second:'.$v;

            return (int) $v + 10;
        };

        $chain = ChainNormalizer::normalize($first, $second);
        $result = ($chain)($this->options(), 5);

        self::assertSame(20, $result);
        self::assertSame(['first:5', 'second:10'], $log);
    }

    #[Test]
    public function itReturnsValueUnchangedWithNoNormalizers(): void
    {
        $chain = ChainNormalizer::normalize();

        self::assertSame('unchanged', ($chain)($this->options(), 'unchanged'));
        self::assertNull(($chain)($this->options(), null));
        self::assertSame(42, ($chain)($this->options(), 42));
    }

    #[Test]
    public function itPassesOptionsToEachNormalizer(): void
    {
        $received = [];

        $options = $this->options();

        $first = static function (Options $o, mixed $v) use (&$received, $options): mixed {
            $received[] = $o === $options;

            return $v;
        };

        $second = static function (Options $o, mixed $v) use (&$received, $options): mixed {
            $received[] = $o === $options;

            return $v;
        };

        $chain = ChainNormalizer::normalize($first, $second);
        ($chain)($options, 'test');

        self::assertSame([true, true], $received);
    }

    #[Test]
    public function itPropagatesExceptionFromFirstNormalizer(): void
    {
        $failing = static function (Options $o, mixed $v): never {
            throw new \InvalidArgumentException('first failed');
        };

        $second = static fn (Options $o, mixed $v): mixed => $v;

        $chain = ChainNormalizer::normalize($failing, $second);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('first failed');

        ($chain)($this->options(), 'value');
    }

    #[Test]
    public function itPropagatesExceptionFromMiddleNormalizer(): void
    {
        $first = static fn (Options $o, mixed $v): string => \trim((string) $v);
        $middle = static function (Options $o, mixed $v): never {
            throw new \InvalidArgumentException('middle failed');
        };
        $third = static fn (Options $o, mixed $v): mixed => $v;

        $chain = ChainNormalizer::normalize($first, $middle, $third);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('middle failed');

        ($chain)($this->options(), '  value  ');
    }

    #[Test]
    public function itDoesNotCallSubsequentNormalizersAfterException(): void
    {
        $called = false;

        $failing = static function (Options $o, mixed $v): never {
            throw new \InvalidArgumentException('failed');
        };

        $after = static function (Options $o, mixed $v) use (&$called): mixed {
            $called = true;

            return $v;
        };

        $chain = ChainNormalizer::normalize($failing, $after);

        try {
            ($chain)($this->options(), 'value');
        } catch (\InvalidArgumentException) {
        }

        self::assertFalse($called);
    }

    #[Test]
    public function normalizeReturnsAClosure(): void
    {
        self::assertInstanceOf(\Closure::class, ChainNormalizer::normalize());
    }

    #[Test]
    public function itHandlesNullValues(): void
    {
        $identity = static fn (Options $o, mixed $v): mixed => $v;
        $chain = ChainNormalizer::normalize($identity);

        self::assertNull(($chain)($this->options(), null));
    }

    #[Test]
    public function itHandlesArrayValues(): void
    {
        $count = static fn (Options $o, mixed $v): int => \count((array) $v);
        $chain = ChainNormalizer::normalize($count);

        self::assertSame(3, ($chain)($this->options(), ['a', 'b', 'c']));
    }
}
