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

use Nalabdou\Dsfr\Contract\NormalizerInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Validates and normalizes an HTML `id` attribute value.
 *
 * Rules:
 *   - Trimmed before validation
 *   - Must not be empty
 *   - Must not contain internal spaces
 *   - Must start with a letter and contain only `[a-zA-Z0-9_:.-]`
 *
 * @implements NormalizerInterface<string, string>
 */
final class HtmlIdNormalizer implements NormalizerInterface
{
    /**
     * Returns a normalizer closure that validates an HTML `id` attribute.
     *
     * Throws {@see \InvalidArgumentException} for empty, spaced, or pattern-violating values.
     *
     * @return \Closure(Options<array<string, mixed>>, string): string
     */
    public static function normalize(): \Closure
    {
        return static function (Options $o, string $v): string {
            $v = \trim($v);

            if ('' === $v) {
                throw new \InvalidArgumentException('[DSFR] HTML "id" cannot be empty.');
            }

            if (\str_contains($v, ' ')) {
                throw new \InvalidArgumentException(\sprintf('[DSFR] HTML "id" cannot contain spaces, got "%s".', $v));
            }

            if (1 !== \preg_match('/^[a-zA-Z][a-zA-Z0-9_:.-]*$/', $v)) {
                throw new \InvalidArgumentException(\sprintf('[DSFR] HTML "id" "%s" is invalid. Must start with a letter and contain only [a-zA-Z0-9_:.-].', $v));
            }

            return $v;
        };
    }
}
