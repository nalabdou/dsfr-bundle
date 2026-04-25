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
 * Contract for context-aware normalizer factories.
 *
 * A contextual normalizer is a static factory that receives a context value
 * at call time and returns a closure compatible with
 * {@see \Symfony\Component\OptionsResolver\OptionsResolver::define()->normalize()}.
 *
 * The context allows the same normalizer class to handle multiple variations
 * without subclassing
 *
 * Contrast with {@see NormalizerInterface}, which is context-free and always
 * returns the same closure regardless of call arguments.
 *
 * @template TInput   Type of the raw input value passed to the closure.
 * @template TOutput  Type of the normalized output value returned by the closure.
 * @template TContext Type of the context value passed to the factory method.
 */
interface ContextualNormalizerInterface
{
    /**
     * Returns a normalizer closure configured for the given context.
     *
     * The returned closure is called by OptionsResolver during prop resolution.
     * It receives the full options bag and the raw value for the option being
     * normalized, and must return a value of type TOutput.
     *
     * The closure MUST be pure: identical inputs always produce identical outputs.
     * The closure MUST throw {@see \InvalidArgumentException} for invalid input.
     *
     * @param TContext $context Context used to configure the normalizer
     *                          (e.g. a BackedEnum class name, a prop name).
     *
     * @return \Closure(Options<array<string, mixed>>, TInput): TOutput
     */
    public static function with(mixed $context): \Closure;
}
