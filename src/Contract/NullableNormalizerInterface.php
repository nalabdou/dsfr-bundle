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

namespace Nalabdou\Dsfr\Contract;

use Symfony\Component\OptionsResolver\Options;

/**
 * Contract for context-free normalizers that also support nullable input.
 *
 * Extends {@see NormalizerInterface} with a {@see nullable()} factory that
 * short-circuits on null — returning null immediately without delegating to
 * the inner normalizer returned by {@see normalize()}.
 *
 * This separates two distinct concerns into two distinct methods:
 *   - {@see normalize()} handles the non-null path and enforces all validation rules.
 *   - {@see nullable()} wraps the non-null path and adds null passthrough.
 *
 * Callers opt in to null support explicitly by choosing {@see nullable()} over
 * {@see normalize()}, making the nullability of each prop visible at the
 * call site in {@see ComponentOptionsInterface::configureOptions()}.
 *
 * Null Object pattern: null is a valid, intentional absence of a value —
 * not an error. It passes through untouched rather than triggering validation.
 *
 * Typical use cases: optional icon class names, optional href attributes,
 * optional aria labels whose absence carries meaning distinct from an empty string.
 *
 * @template TInput  Type of the non-null input value before normalization.
 * @template TOutput Type of the output value after normalization.
 *
 * @extends NormalizerInterface<TInput, TOutput>
 *
 * @see NormalizerInterface                  For the base contract without null support.
 * @see NullableContextualNormalizerInterface For the context-aware equivalent.
 */
interface NullableNormalizerInterface extends NormalizerInterface
{
    /**
     * Returns a closure that accepts null or a value of type TInput.
     *
     * When the resolved value is null, the closure returns null immediately
     * without invoking any validation or transformation logic.
     * When the resolved value is non-null, it delegates to {@see normalize()}.
     *
     * The returned closure is intended for use with optional props declared
     * via {@see \Symfony\Component\OptionsResolver\OptionsResolver::define()->normalize()}.
     *
     * @return \Closure(Options<array<string, mixed>>, TInput|null): (TOutput|null)
     */
    public static function nullable(): \Closure;
}
