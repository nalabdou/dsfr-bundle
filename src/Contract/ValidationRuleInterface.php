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

/**
 * Contract for cross-property validation rules applied after prop resolution.
 *
 * Single-prop validation — type checking, value constraints, format rules —
 * belongs in the OptionsResolver normalizer pipeline via
 * {@see ComponentOptionsInterface::configureOptions()}.
 *
 * This interface handles a different category of validation: rules that involve
 * two or more props whose combined values may be invalid even when each prop is
 * individually valid. These checks cannot run during OptionsResolver resolution
 * because the other props are not yet available on the component instance.
 *
 * Rules are registered in a component's PostMount hook via
 * {@see \Nalabdou\Dsfr\Validator\ComponentValidator} and execute after all props
 * have been assigned to the component instance.
 *
 * Examples of cross-property rules:
 *   - "href is required when variant is link"
 *   - "icon is forbidden when variant is dismissible"
 *   - "title is required when iconPosition is alone"
 *
 * SOLID principles applied:
 *   SRP — one class encapsulates exactly one validation rule.
 *   OCP — new rules are added as new classes without modifying existing ones.
 *   LSP — all implementations are substitutable in ComponentValidator.
 *   ISP — minimal surface area: a single method.
 *   DIP — ComponentValidator depends on this abstraction, not on concrete rules.
 */
interface ValidationRuleInterface
{
    /**
     * Validates the component against this rule.
     *
     * Called by {@see \Nalabdou\Dsfr\Validator\ComponentValidator::validate()}
     * after all props have been resolved and assigned to the component instance.
     * The component is passed as a plain object to avoid coupling this contract
     * to any specific base class.
     *
     * Implementations MUST throw {@see \InvalidArgumentException} when the rule
     * is violated. The exception message SHOULD include the component name and
     * clearly identify which props are involved and what the constraint is.
     *
     * Implementations MUST NOT produce side effects — they only read props
     * and throw on violation.
     *
     * @param object $component the mounted component instance with all props assigned
     *
     * @throws \InvalidArgumentException when the cross-property constraint is violated
     */
    public function validate(object $component): void;
}
