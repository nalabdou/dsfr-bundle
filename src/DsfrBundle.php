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

namespace Nalabdou\Dsfr;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class DsfrBundle extends AbstractBundle
{
    public const string VERSION = '0.1.0';
    public const string DSFR_VERSION = '1.14';
    public const string BUNDLE_NAME = 'dsfr_bundle';

    public const string TAG_COMMUNITY = 'dsfr.component.community';
    public const string TAG_PRO = 'dsfr.component.pro';

    protected string $extensionAlias = 'dsfr';

    public function configure(DefinitionConfigurator $definition): void
    {
    }

    /**
     * @param array<string|int, mixed> $config
     */
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $container->parameters()
            ->set('dsfr.version', self::VERSION)
            ->set('dsfr.dsfr_version', self::DSFR_VERSION);

        $container->import('../config/services.php');
    }

    public function prependExtension(
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}
