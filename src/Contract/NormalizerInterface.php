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
 * Contract for context-free normalizer factories.
 *
 * A normalizer is a static factory that returns a closure compatible with
 * {@see \Symfony\Component\OptionsResolver\OptionsResolver::define()->normalize()}.
 *
 * Unlike {@see ContextualNormalizerInterface}, implementations of this interface
 * require no configuration at call time — the same closure is always returned
 * regardless of arguments. This makes them suitable for simple, reusable
 * transformations such as trimming whitespace, casting booleans, or validating
 * URL formats.
 *
 * The returned closure MUST be pure: identical inputs always produce identical
 * outputs. It MUST throw {@see \InvalidArgumentException} for invalid input,
 * never silently swallow errors or return a fallback value.
 *
 * SOLID principles applied:
 *   SRP — one class, one transformation.
 *   OCP — new transformations are added as new classes, not by modifying existing ones.
 *   LSP — all implementations are substitutable wherever NormalizerInterface is expected.
 *   ISP — minimal surface area: a single method.
 *   DIP — components depend on this abstraction, not on concrete normalizer classes.
 *
 * @template TInput  Type of the raw input value before normalization.
 * @template TOutput Type of the normalized output value.
 *
 * @see ContextualNormalizerInterface For normalizers that require a context argument.
 * @see NullableNormalizerInterface   For normalizers that also support null input.
 */
interface NormalizerInterface
{
    /**
     * Returns a closure that normalizes a single prop value.
     *
     * The closure is called by OptionsResolver during prop resolution and
     * receives the full options bag alongside the raw value for the option
     * being normalized.
     *
     * The options bag can be used to implement cross-prop normalization —
     * for example, reading another prop's resolved value to influence the
     * current one — but must be accessed lazily to avoid resolution cycles.
     *
     * @return \Closure(Options<array<string, mixed>>, TInput): TOutput
     */
    public static function normalize(): \Closure;
}
