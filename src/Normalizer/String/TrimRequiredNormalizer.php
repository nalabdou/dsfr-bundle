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
 * Trims whitespace and rejects empty strings.
 *
 * Intended for required display props such as `label` or `title`.
 * Throws {@see \InvalidArgumentException} if the value is empty after trimming.
 *
 * @implements NormalizerInterface<string, string>
 */
final class TrimRequiredNormalizer implements NormalizerInterface
{
    /**
     * @return \Closure(Options<array<string, mixed>>, string): string
     */
    public static function normalize(string $propName = 'value'): \Closure
    {
        return static function (Options $o, string $v) use ($propName): string {
            $v = \trim($v);

            if ('' === $v) {
                throw new \InvalidArgumentException(\sprintf('[DSFR] "%s" cannot be an empty string.', $propName));
            }

            return $v;
        };
    }
}
