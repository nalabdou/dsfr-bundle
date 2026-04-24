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
 * Contract for context-aware normalizers that also support nullable input.
 *
 * Extends {@see ContextualNormalizerInterface} with a {@see withNullable()} factory
 * that short-circuits on null — returning null immediately without delegating
 * to the inner normalizer.
 *
 * Useful for optional props that may legitimately be absent, such as icon class
 * names, optional href attributes, or nullable enum values.
 *
 * @template TInput  Type of the non-null input value before normalization.
 * @template TOutput Type of the output value after normalization.
 * @template TContext Type of the context passed to the normalizer factory.
 *
 * @extends ContextualNormalizerInterface<TInput, TOutput, TContext>
 */
interface NullableContextualNormalizerInterface extends ContextualNormalizerInterface
{
    /**
     * Returns a closure that accepts null or a value of type TInput.
     *
     * When the resolved value is null, the closure returns null immediately.
     * When the resolved value is non-null, it delegates to {@see with()}.
     *
     * @param TContext $context Context passed to the inner normalizer (e.g. enum class name).
     *
     * @return \Closure(Options<array<string, mixed>>, TInput|null): (TOutput|null)
     */
    public static function withNullable(mixed $context): \Closure;
}
