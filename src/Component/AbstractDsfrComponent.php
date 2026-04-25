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

namespace Nalabdou\Dsfr\Component;

use Nalabdou\Dsfr\Contract\ComponentOptionsInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

/**
 * Base class for all DSFR components.
 *
 * Template Method pattern:
 *   basePreMount() is the template — it calls configureOptions(),
 *   which each subclass implements (hook method).
 *
 * The #[PreMount(priority: 100)] ensures this hook runs
 * BEFORE any #[PreMount] in subclasses (default priority: 0).
 *
 * The resolve($data) + $data pattern:
 *   - resolve($data): declared props → normalized + validated
 *   - + $data: undeclared props (id, class, data-*…)
 *     re-injected without overriding resolved props (left-hand keys win)
 */
abstract class AbstractDsfrComponent implements ComponentOptionsInterface
{
    public const string COMPONENT_NAME = '';
    public const string TEMPLATE = '';

    /**
     * Template Method — delegates to configureOptions().
     * Must not be overridden in subclasses.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    #[PreMount(priority: 100)]
    final public function basePreMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setIgnoreUndefined(true);
        $this->configureOptions($resolver);

        return $resolver->resolve($data) + $data;
    }

    /**
     * Hook method — declares props via OptionsResolver.
     * Override in each subclass.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    abstract public function cssClass(): string;

    final public function getComponentName(): string
    {
        return static::COMPONENT_NAME;
    }
}
