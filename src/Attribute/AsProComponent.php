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
 * Marks a Twig component as belonging to the Pro tier.
 *
 * Pro tier components require a valid purchased license key to render in
 * production environments. In development environments (localhost and equivalent
 * local TLDs), license enforcement is automatically disabled and Pro components
 * render without restriction.
 *
 * This attribute serves two purposes at runtime:
 *
 *   1. DI autoconfiguration — {@see \Nalabdou\Dsfr\DsfrBundle::loadExtension()} registers it
 *      via {@see \Symfony\Component\DependencyInjection\ContainerBuilder::registerAttributeForAutoconfiguration()},
 *      which automatically adds the {@see \Nalabdou\Dsfr\DsfrBundle::TAG_PRO} service tag to
 *      every class carrying this attribute.
 *
 * Every component class MUST carry exactly one of {@see AsCommunityComponent}
 * or {@see AsProComponent} — never both, never neither.
 *
 * @see AsCommunityComponent For the Community tier equivalent.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class AsProComponent
{
}
