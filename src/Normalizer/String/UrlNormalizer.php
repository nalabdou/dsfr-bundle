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
 * Validates and normalizes a URL suitable for use in an `href` attribute.
 *
 * Accepted: `''`, `/relative`, `https://`, `http://`, `mailto:`, `tel:`, `#anchor`
 * Rejected: any other non-empty value.
 *
 * An empty string is accepted to represent an absent optional `href` prop.
 *
 * @implements NormalizerInterface<string, string>
 */
final class UrlNormalizer implements NormalizerInterface
{
    private const string PATTERN = '#^(/(?!/)|https?://|mailto:|tel:|\\#)#i';

    /**
     * @return \Closure(Options<array<string, mixed>>, string): string
     */
    public static function normalize(): \Closure
    {
        return static function (Options $o, string $v): string {
            $v = \trim($v);

            if ('' === $v || 1 === \preg_match(self::PATTERN, $v)) {
                return $v;
            }

            throw new \InvalidArgumentException(\sprintf('[DSFR] Invalid href "%s". Expected /path, https://, http://, mailto:, tel:, or #anchor.', $v));
        };
    }
}
