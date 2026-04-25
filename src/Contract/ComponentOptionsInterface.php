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

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Contract for components that declare their props via OptionsResolver.
 *
 * Each component implementation defines its accepted props — their types,
 * default values, normalizers, and documentation strings — by configuring
 * the provided resolver. Props not declared in the resolver are preserved
 * as HTML passthrough attributes via the `resolve() + $data` pattern.
 *
 * Dependency Inversion Principle: the base class depends on this abstraction
 * rather than on concrete component classes, allowing any component to plug
 * into the shared resolution pipeline without coupling to the base class
 * internals.
 *
 * @see OptionsResolver
 */
interface ComponentOptionsInterface
{
    /**
     * Declares the props accepted by this component.
     *
     * Called once per render by the shared PreMount hook in the base class.
     * Use the fluent {@see OptionsResolver::define()} API to declare each prop
     * with its allowed types, default value, normalizer, and info string.
     *
     * Props not declared here are not stripped — they pass through as raw
     * HTML attributes on the root element via ComponentAttributes.
     */
    public function configureOptions(OptionsResolver $resolver): void;
}
