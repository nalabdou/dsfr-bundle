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

namespace Nalabdou\Dsfr\Tests\Fixtures\Trait;

use Symfony\Component\OptionsResolver\Options;

trait WithOptions
{
    private function options(): Options
    {
        /** @var Options<array<string, mixed>> $options */
        $options = $this->createMock(Options::class);

        return $options;
    }
}
