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

namespace Nalabdou\Dsfr\Normalizer\Compound;

use Nalabdou\Dsfr\Contract\NormalizerInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Composes multiple normalizers into a single pipeline (Chain of Responsibility).
 *
 * Each normalizer's output becomes the next one's input, allowing
 * reusable combinations without creating a dedicated class per use case.
 *
 * ```php
 * ChainNormalizer::normalize(
 *     TrimNormalizer::normalize(),
 *     UrlNormalizer::normalize(),
 * )
 * ```
 *
 * @implements NormalizerInterface<mixed, mixed>
 */
final class ChainNormalizer implements NormalizerInterface
{
    /**
     * Returns a normalizer closure that pipes `$value` through each given normalizer in order.
     *
     * @param \Closure(Options<array<string, mixed>>, mixed): mixed ...$normalizers
     *
     * @return \Closure(Options<array<string, mixed>>, mixed): mixed
     */
    public static function normalize(\Closure ...$normalizers): \Closure
    {
        return static function (Options $options, mixed $value) use ($normalizers): mixed {
            foreach ($normalizers as $normalizer) {
                $value = $normalizer($options, $value);
            }

            return $value;
        };
    }
}
