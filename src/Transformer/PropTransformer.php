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

namespace Nalabdou\Dsfr\Transformer;

/**
 * Transforms PHP property names (camelCase) into HTML attributes (kebab-case).
 *
 * Used in error messages and auto-generated documentation
 * to align PHP naming with HTML template conventions.
 *
 * Pattern: SRP — naming transformation only.
 *
 * Examples:
 *   iconPosition → icon-position
 *   dismissLabel → dismiss-label
 *   external     → external
 */
final class PropTransformer
{
    /** @var array<string, string> */
    private static array $cache = [];

    private function __construct()
    {
    }

    public static function htmlAttribute(string $camelCase): string
    {
        return self::$cache[$camelCase] ??= \strtolower(
            (string) \preg_replace('/([A-Z])/', '-$1', $camelCase)
        );
    }

    /**
     * @param list<string> $props
     *
     * @return array<string, string>
     */
    public static function htmlAttributes(array $props): array
    {
        $result = [];
        foreach ($props as $prop) {
            $result[$prop] = self::htmlAttribute($prop);
        }

        return $result;
    }

    public static function phpProp(string $kebabCase): string
    {
        return \lcfirst(\str_replace(
            ' ',
            '',
            \ucwords(\str_replace('-', ' ', $kebabCase)),
        ));
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
