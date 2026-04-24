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
 * Trims leading and trailing whitespace from a string value.
 *
 * An empty string after trimming is accepted without error.
 *
 * @implements NormalizerInterface<string, string>
 */
final class TrimNormalizer implements NormalizerInterface
{
    /**
     * @return \Closure(Options<array<string, mixed>>, string): string
     */
    public static function normalize(): \Closure
    {
        return static fn (Options $o, string $v): string => \trim($v);
    }
}
