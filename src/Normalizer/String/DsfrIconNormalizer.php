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

namespace Nalabdou\Dsfr\Normalizer\String;

use Nalabdou\Dsfr\Contract\NullableNormalizerInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Validates and normalizes a DSFR icon class name.
 *
 * Rules:
 *   - Must start with `"fr-icon-"`
 *   - Only `[a-z0-9-]` characters are allowed after the prefix
 *   - `null` or blank string → `null` (optional icon absent)
 *
 * ```php
 * $resolver->setNormalizer('icon', DsfrIconNormalizer::normalize());
 * $resolver->setNormalizer('icon', DsfrIconNormalizer::nullable());
 * ```
 *
 * @implements NullableNormalizerInterface<string, string|null>
 */
final class DsfrIconNormalizer implements NullableNormalizerInterface
{
    private const string PREFIX = 'fr-icon-';
    private const string PATTERN = '/^fr-icon-[a-z0-9-]+$/';

    /**
     * Returns a normalizer closure that validates a required DSFR icon class.
     *
     * Blank or `null` input resolves to `null`.
     * Throws {@see \InvalidArgumentException} for a missing prefix or invalid characters.
     *
     * @return \Closure(Options<array<string, mixed>>, ?string): ?string
     */
    public static function normalize(): \Closure
    {
        return static function (Options $o, ?string $v): ?string {
            if (null === $v || '' === \trim($v)) {
                return null;
            }

            $v = \trim($v);

            if (!\str_starts_with($v, self::PREFIX)) {
                throw new \InvalidArgumentException(\sprintf('[DSFR] Icon class must start with "%s", got "%s". Example: "fr-icon-calendar-line".', self::PREFIX, $v));
            }

            if (1 !== \preg_match(self::PATTERN, $v)) {
                throw new \InvalidArgumentException(\sprintf('[DSFR] Icon class "%s" contains invalid characters. Only [a-z0-9-] are allowed after "%s".', $v, self::PREFIX));
            }

            return $v;
        };
    }

    public static function nullable(): \Closure
    {
        $inner = self::normalize();

        return static fn (Options $o, ?string $v): ?string => null === $v ? null : $inner($o, $v);
    }
}
