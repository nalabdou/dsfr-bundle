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

namespace Nalabdou\Dsfr\Sanitizer;

/**
 * Cleans passthrough HTML attributes before injecting them into ComponentAttributes.
 *
 * Protects accessibility (a11y) attributes controlled by the component
 * against injections from the calling context.
 *
 * Pattern: Whitelist Sanitizer — everything is allowed except the blacklist.
 * Pattern: SRP — sanitization only.
 *
 * Usage in a template:
 *   {{ attributes.without(...HtmlAttributeSanitizer::PROTECTED_ARIA) }}
 */
final class HtmlAttributeSanitizer
{
    /** @var list<string> */
    public const array PROTECTED_ARIA = [
        'aria-pressed',
        'aria-label',
        'aria-disabled',
        'aria-modal',
        'aria-labelledby',
        'aria-expanded',
        'aria-controls',
    ];

    /** @var list<string> */
    public const array PROTECTED_STRUCTURAL = [
        'role',
        'type',
        'disabled',
        'href',
    ];

    /** @return list<string> */
    public static function protected(): array
    {
        return \array_merge(self::PROTECTED_ARIA, self::PROTECTED_STRUCTURAL);
    }

    /**
     * @param array<string, mixed> $attributes
     * @param list<string>         $protected
     *
     * @return array<string, mixed>
     */
    public static function sanitize(array $attributes, array $protected = []): array
    {
        $toRemove = [] !== $protected
            ? $protected
            : self::protected();

        return \array_diff_key($attributes, \array_flip($toRemove));
    }

    public static function validDataAttribute(string $name): bool
    {
        return (bool) \preg_match('/^data-[a-z][a-z0-9-]*$/', $name);
    }
}
