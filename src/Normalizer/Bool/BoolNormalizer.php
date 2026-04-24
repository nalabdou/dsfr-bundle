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

namespace Nalabdou\Dsfr\Normalizer\Bool;

use Nalabdou\Dsfr\Contract\NormalizerInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Permissive boolean normalizer for Symfony OptionsResolver.
 *
 * Symfony's TwigPreLexer serializes HTML attributes in two ways before they
 * reach an OptionsResolver:
 *
 *   - Attribute without value → PHP `true`   (e.g. `<twig:X disabled />`)
 *   - Attribute with a value  → PHP `string`  (e.g. `<twig:X disabled="false" />`)
 *
 * Native PHP casts the string `"false"` to boolean `true`, which breaks the
 * expected HTML semantics. This normalizer corrects that behaviour and accepts
 * the full range of common truthy/falsy string literals.
 *
 * ### Accepted input
 *
 * | PHP type | Accepted values                                    |
 * |----------|----------------------------------------------------|
 * | `bool`   | `true`, `false`                                    |
 * | `int`    | any integer — `0` → false, everything else → true  |
 * | `string` | `"true"`, `"1"`, `"yes"`, `"on"` → `true`         |
 * |          | `"false"`, `"0"`, `"no"`, `"off"`, `""` → `false` |
 *
 * String matching is case-insensitive and trims surrounding whitespace.
 *
 * @implements NormalizerInterface<bool|int|string, bool>
 */
final class BoolNormalizer implements NormalizerInterface
{
    private const array MAP = [
        'true' => true,
        '1' => true,
        'yes' => true,
        'on' => true,
        'false' => false,
        '0' => false,
        'no' => false,
        'off' => false,
        '' => false,
    ];

    /**
     * Returns an OptionsResolver-compatible normalizer closure.
     *
     * Resolves `bool`, `int`, or `string` to a native PHP `bool`.
     * Throws {@see \InvalidArgumentException} for unrecognised strings or unsupported types.
     *
     * ```php
     * $resolver->setNormalizer('disabled', BoolNormalizer::normalize());
     * ```
     *
     * @return \Closure(Options<array<string, mixed>>, bool|int|string): bool
     */
    public static function normalize(): \Closure
    {
        return static function (Options $o, mixed $v): bool {
            if (\is_bool($v)) {
                return $v;
            }

            if (\is_int($v)) {
                return 0 !== $v;
            }

            if (\is_string($v)) {
                $key = \strtolower(\trim($v));

                if (\array_key_exists($key, self::MAP)) {
                    return self::MAP[$key];
                }

                throw new \InvalidArgumentException(\sprintf('[DSFR] Cannot cast "%s" to bool. Use: true/false, 1/0, yes/no, on/off.', $v));
            }

            throw new \InvalidArgumentException(\sprintf('[DSFR] Expected bool, int or string, got %s.', \get_debug_type($v)));
        };
    }
}
