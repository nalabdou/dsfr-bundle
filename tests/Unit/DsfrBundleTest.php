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

namespace Nalabdou\Dsfr\Tests;

use Nalabdou\Dsfr\DsfrBundle;
use Nyholm\BundleTest\TestKernel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

#[CoversClass(DsfrBundle::class)]
final class DsfrBundleTest extends KernelTestCase
{
    #[\Override]
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    #[\Override]
    protected static function createKernel(array $options = []): KernelInterface
    {
        /** @var TestKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(DsfrBundle::class);
        $kernel->handleOptions($options);

        return $kernel;
    }

    #[Test]
    public function versionConstantMatchesExpectedValue(): void
    {
        self::assertSame('0.1.0', DsfrBundle::VERSION);
    }

    #[Test]
    public function dsfrVersionConstantMatchesExpectedValue(): void
    {
        self::assertSame('1.14', DsfrBundle::DSFR_VERSION);
    }

    #[Test]
    public function bundleNameConstantMatchesExpectedValue(): void
    {
        self::assertSame('dsfr_bundle', DsfrBundle::BUNDLE_NAME);
    }

    #[Test]
    public function tagCommunityConstantMatchesExpectedValue(): void
    {
        self::assertSame('dsfr.component.community', DsfrBundle::TAG_COMMUNITY);
    }

    #[Test]
    public function tagProConstantMatchesExpectedValue(): void
    {
        self::assertSame('dsfr.component.pro', DsfrBundle::TAG_PRO);
    }

    #[Test]
    public function containerHasDsfrVersionParameter(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        self::assertTrue($container->hasParameter('dsfr.version'));
        self::assertSame(DsfrBundle::VERSION, $container->getParameter('dsfr.version'));
    }

    #[Test]
    public function containerHasDsfrDsfrVersionParameter(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        self::assertTrue($container->hasParameter('dsfr.dsfr_version'));
        self::assertSame(DsfrBundle::DSFR_VERSION, $container->getParameter('dsfr.dsfr_version'));
    }

    #[Test]
    public function bundleBootsWithoutError(): void
    {
        self::bootKernel();

        self::assertNotNull(self::getContainer());
    }

    #[Test]
    public function extensionAliasIsDsfr(): void
    {
        $bundle = new DsfrBundle();

        self::assertSame('dsfr', $bundle->getContainerExtension()?->getAlias());
    }

    #[\Override]
    protected static function ensureKernelShutdown(): void
    {
        $wasBooted = static::$booted;
        parent::ensureKernelShutdown();

        if ($wasBooted) {
            \restore_exception_handler();
        }
    }
}
