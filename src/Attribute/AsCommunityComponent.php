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

namespace Nalabdou\Dsfr\Attribute;

/**
 * Marks a Twig component as belonging to the Community tier.
 *
 * Community tier components are distributed under the MIT license and require
 * no license key to use in production. They are available to all users of the
 * bundle regardless of plan.
 *
 * This attribute serves two purposes at runtime:
 *
 *   1. DI autoconfiguration — {@see \Nalabdou\Dsfr\DsfrBundle::loadExtension()} registers it
 *      via {@see Symfony\Component\DependencyInjection\ContainerBuilder::registerAttributeForAutoconfiguration()},
 *      which automatically adds the {@see \Nalabdou\Dsfr\DsfrBundle::TAG_COMMUNITY} service
 *      tag to every class carrying this attribute.
 *
 * Every component class MUST carry exactly one of {@see AsCommunityComponent}
 * or {@see AsProComponent} — never both, never neither.
 *
 * @see AsProComponent For the Pro tier equivalent.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class AsCommunityComponent
{
}
