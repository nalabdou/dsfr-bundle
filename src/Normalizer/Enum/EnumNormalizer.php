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

namespace Nalabdou\Dsfr\Normalizer\Enum;

use Nalabdou\Dsfr\Contract\ContextualNormalizerInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Normalizes a raw scalar or an already-resolved enum case to a typed {@see \BackedEnum} instance.
 *
 * Accepts `string`, `int`, or an existing instance of `T`; rejects anything else
 * with an {@see \InvalidArgumentException} listing all valid case values.
 *
 * ```php
 * $resolver->setNormalizer('size', EnumNormalizer::with(SizeEnum::class));
 * $resolver->setNormalizer('size', EnumNormalizer::withNullable(SizeEnum::class));
 * ```
 *
 * @template T of \BackedEnum
 *
 * @implements ContextualNormalizerInterface<string|int|T, T, class-string<T>>
 */
final class EnumNormalizer implements ContextualNormalizerInterface
{
    /**
     * Returns a normalizer closure that resolves a scalar or enum instance to `T`.
     *
     * @param class-string<T> $context the target {@see \BackedEnum} class
     *
     * @return \Closure(Options<array<string, mixed>>, string|int|T): T
     */
    public static function with(mixed $context): \Closure
    {
        /** @var class-string<T> $enumClass */
        $enumClass = $context;

        return static function (Options $options, mixed $value) use ($enumClass): \BackedEnum {
            if ($value instanceof $enumClass) {
                return $value;
            }

            if (!\is_string($value) && !\is_int($value)) {
                throw new \InvalidArgumentException(\sprintf('[DSFR] Expected string|int|%s, got %s.', (new \ReflectionEnum($enumClass))->getShortName(), \get_debug_type($value)));
            }

            $result = $enumClass::tryFrom($value);

            if (null === $result) {
                $cases = $enumClass::cases();

                $valid = \implode(', ', \array_map(
                    static fn (\BackedEnum $case): string => (string) $case->value,
                    $cases
                ));

                throw new \InvalidArgumentException(\sprintf('[DSFR] "%s" is not a valid %s. Accepted: [%s].', (string) $value, (new \ReflectionEnum($enumClass))->getShortName(), $valid));
            }

            return $result;
        };
    }

    /**
     * Returns a nullable variant of {@see self::with()} that passes `null` through unchanged.
     *
     * @param class-string<T> $context the target {@see \BackedEnum} class
     *
     * @return \Closure(Options<array<string, mixed>>, string|int|T|null): (T|null)
     */
    public static function withNullable(mixed $context): \Closure
    {
        $inner = self::with($context);

        return static function (
            Options $options,
            string|int|\BackedEnum|null $value,
        ) use ($inner): \BackedEnum|null {
            if (null === $value) {
                return null;
            }

            return $inner($options, $value);
        };
    }
}
