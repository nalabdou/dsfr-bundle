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

namespace Nalabdou\Dsfr\Formatter;

/**
 * Formats bundle error messages in a consistent way.
 *
 * Pattern: Static Factory + SRP
 *
 * Format: [Dsfr:ComponentName] message. Hint: suggestion.
 */
final class ErrorMessageFormatter
{
    private function __construct()
    {
    }

    public static function format(string $componentName, string $message): string
    {
        return \sprintf('[%s] %s', $componentName, \rtrim($message, '.').'.');
    }

    public static function withHint(
        string $componentName,
        string $message,
        string $hint,
    ): string {
        return \sprintf(
            '[%s] %s Hint: %s',
            $componentName,
            \rtrim($message, '.').'.',
            \rtrim($hint, '.').'.',
        );
    }

    /**
     * @param array<string, bool|float|int|string|null> $accepted
     */
    public static function invalidValue(
        string $componentName,
        string $propName,
        bool|float|int|string|null $value,
        array $accepted,
    ): string {
        return \sprintf(
            '[%s] "%s" value "%s" is invalid. Accepted: [%s].',
            $componentName,
            $propName,
            $value,
            \implode(', ', $accepted),
        );
    }

    public static function required(string $componentName, string $propName): string
    {
        return \sprintf('[%s] "%s" is required.', $componentName, $propName);
    }

    public static function requiredWhen(
        string $componentName,
        string $propName,
        string $conditionProp,
        string $conditionValue,
    ): string {
        return \sprintf(
            '[%s] "%s" is required when "%s" is "%s".',
            $componentName,
            $propName,
            $conditionProp,
            $conditionValue,
        );
    }

    public static function incompatible(
        string $componentName,
        string $propName,
        string $conflictProp,
        string $conflictValue,
    ): string {
        return \sprintf(
            '[%s] "%s" is not allowed when "%s" is "%s".',
            $componentName,
            $propName,
            $conflictProp,
            $conflictValue,
        );
    }
}
