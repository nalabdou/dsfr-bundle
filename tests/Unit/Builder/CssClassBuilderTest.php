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

namespace Nalabdou\Dsfr\Tests\Unit\Builder;

use Nalabdou\Dsfr\Builder\CssClassBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CssClassBuilder::class)]
final class CssClassBuilderTest extends TestCase
{
    #[Test]
    public function createWithNoBaseProducesEmptyBuilder(): void
    {
        $builder = CssClassBuilder::create();

        self::assertSame('', $builder->build());
        self::assertTrue($builder->isEmpty());
        self::assertSame([], $builder->toArray());
    }

    #[Test]
    public function createWithSingleBaseClass(): void
    {
        self::assertSame('fr-btn', CssClassBuilder::create('fr-btn')->build());
    }

    #[Test]
    public function createWithMultipleBaseClasses(): void
    {
        self::assertSame('fr-btn fr-btn--sm', CssClassBuilder::create('fr-btn', 'fr-btn--sm')->build());
    }

    #[Test]
    public function createTrimsBaseClasses(): void
    {
        self::assertSame('fr-btn', CssClassBuilder::create('  fr-btn  ')->build());
    }

    #[Test]
    public function addAppendsAClass(): void
    {
        $result = CssClassBuilder::create('fr-btn')
            ->add('fr-btn--sm')
            ->build();

        self::assertSame('fr-btn fr-btn--sm', $result);
    }

    #[Test]
    public function addIgnoresNull(): void
    {
        self::assertSame('fr-btn', CssClassBuilder::create('fr-btn')->add(null)->build());
    }

    #[Test]
    public function addIgnoresEmptyString(): void
    {
        self::assertSame('fr-btn', CssClassBuilder::create('fr-btn')->add('')->build());
    }

    #[Test]
    public function addIgnoresWhitespaceOnly(): void
    {
        self::assertSame('fr-btn', CssClassBuilder::create('fr-btn')->add('   ')->build());
    }

    #[Test]
    public function addTrimsClass(): void
    {
        self::assertSame('fr-btn fr-btn--sm', CssClassBuilder::create('fr-btn')->add('  fr-btn--sm  ')->build());
    }

    #[Test]
    public function addPreservesDuplicates(): void
    {
        $result = CssClassBuilder::create('fr-btn')->add('fr-btn')->build();

        self::assertSame('fr-btn fr-btn', $result);
    }

    #[Test]
    public function addManyAppendsIterable(): void
    {
        $result = CssClassBuilder::create('fr-btn')
            ->addMany(['fr-btn--sm', 'fr-btn--icon-left'])
            ->build();

        self::assertSame('fr-btn fr-btn--sm fr-btn--icon-left', $result);
    }

    #[Test]
    public function addManySkipsNulls(): void
    {
        $result = CssClassBuilder::create('fr-btn')
            ->addMany(['fr-btn--sm', null, 'fr-btn--icon-left'])
            ->build();

        self::assertSame('fr-btn fr-btn--sm fr-btn--icon-left', $result);
    }

    #[Test]
    public function addManyAcceptsEmptyIterable(): void
    {
        self::assertSame('fr-btn', CssClassBuilder::create('fr-btn')->addMany([])->build());
    }

    #[Test]
    public function addArraySkipsNonStrings(): void
    {
        $result = CssClassBuilder::create('fr-btn')
            ->addArray(['fr-btn--sm', 42, null, true, 'fr-btn--icon-left'])
            ->build();

        self::assertSame('fr-btn fr-btn--sm fr-btn--icon-left', $result);
    }

    #[Test]
    public function addMapAddsClassesWhereValueIsTrue(): void
    {
        $result = CssClassBuilder::create('fr-btn')
            ->addMap([
                'fr-btn--sm' => true,
                'fr-btn--disabled' => false,
                'fr-btn--icon-left' => true,
            ])
            ->build();

        self::assertSame('fr-btn fr-btn--sm fr-btn--icon-left', $result);
    }

    #[Test]
    public function addMapWithAllFalseAddsNothing(): void
    {
        $result = CssClassBuilder::create('fr-btn')
            ->addMap(['fr-btn--sm' => false, 'fr-btn--disabled' => false])
            ->build();

        self::assertSame('fr-btn', $result);
    }

    #[Test]
    public function addMapWithEmptyMapAddsNothing(): void
    {
        self::assertSame('fr-btn', CssClassBuilder::create('fr-btn')->addMap([])->build());
    }

    #[Test]
    public function addWhenAddsClassWhenConditionIsTrue(): void
    {
        $result = CssClassBuilder::create('fr-btn')->addWhen(true, 'fr-btn--sm')->build();

        self::assertSame('fr-btn fr-btn--sm', $result);
    }

    #[Test]
    public function addWhenSkipsClassWhenConditionIsFalse(): void
    {
        $result = CssClassBuilder::create('fr-btn')->addWhen(false, 'fr-btn--sm')->build();

        self::assertSame('fr-btn', $result);
    }

    #[Test]
    public function addWhenIgnoresNullClassEvenWhenConditionIsTrue(): void
    {
        $result = CssClassBuilder::create('fr-btn')->addWhen(true, null)->build();

        self::assertSame('fr-btn', $result);
    }

    #[Test]
    public function addEitherAddsIfTrueWhenConditionIsTrue(): void
    {
        $result = CssClassBuilder::create('fr-btn')
            ->addEither(true, 'fr-btn--primary', 'fr-btn--secondary')
            ->build();

        self::assertSame('fr-btn fr-btn--primary', $result);
    }

    #[Test]
    public function addEitherAddsIfFalseWhenConditionIsFalse(): void
    {
        $result = CssClassBuilder::create('fr-btn')
            ->addEither(false, 'fr-btn--primary', 'fr-btn--secondary')
            ->build();

        self::assertSame('fr-btn fr-btn--secondary', $result);
    }

    #[Test]
    public function addEitherAddsNothingWhenConditionFalseAndNoElse(): void
    {
        $result = CssClassBuilder::create('fr-btn')
            ->addEither(false, 'fr-btn--primary')
            ->build();

        self::assertSame('fr-btn', $result);
    }

    #[Test]
    public function addWhenArrayAddsAllClassesWhenConditionIsTrue(): void
    {
        $result = CssClassBuilder::create('fr-btn')
            ->addWhenArray(true, ['fr-btn--sm', 'fr-btn--icon-left'])
            ->build();

        self::assertSame('fr-btn fr-btn--sm fr-btn--icon-left', $result);
    }

    #[Test]
    public function addWhenArrayAddsNothingWhenConditionIsFalse(): void
    {
        $result = CssClassBuilder::create('fr-btn')
            ->addWhenArray(false, ['fr-btn--sm', 'fr-btn--icon-left'])
            ->build();

        self::assertSame('fr-btn', $result);
    }

    #[Test]
    public function buildIsIdempotent(): void
    {
        $builder = CssClassBuilder::create('fr-btn', 'fr-btn--sm');

        self::assertSame($builder->build(), $builder->build());
    }

    #[Test]
    public function toStringEqualsBuild(): void
    {
        $builder = CssClassBuilder::create('fr-btn', 'fr-btn--sm');

        self::assertSame($builder->build(), (string) $builder);
    }

    #[Test]
    public function toArrayReturnsListOfClasses(): void
    {
        $result = CssClassBuilder::create('fr-btn', 'fr-btn--sm')->toArray();

        self::assertSame(['fr-btn', 'fr-btn--sm'], $result);
    }

    #[Test]
    public function isEmptyReturnsTrueWhenNoClasses(): void
    {
        self::assertTrue(CssClassBuilder::create()->isEmpty());
    }

    #[Test]
    public function isEmptyReturnsFalseWhenClassesPresent(): void
    {
        self::assertFalse(CssClassBuilder::create('fr-btn')->isEmpty());
    }

    #[Test]
    public function fluentInterfaceReturnsSameInstance(): void
    {
        $builder = CssClassBuilder::create();

        self::assertSame($builder, $builder->add('fr-btn'));
        self::assertSame($builder, $builder->addMany([]));
        self::assertSame($builder, $builder->addArray([]));
        self::assertSame($builder, $builder->addMap([]));
        self::assertSame($builder, $builder->addWhen(true, 'fr-btn'));
        self::assertSame($builder, $builder->addEither(true, 'fr-btn'));
        self::assertSame($builder, $builder->addWhenArray(true, []));
    }

    #[Test]
    #[DataProvider('provideRealWorldCssClasses')]
    public function itBuildsRealWorldDsfrClassStrings(string $expected, \Closure $builder): void
    {
        self::assertSame($expected, $builder()->build());
    }

    public static function provideRealWorldCssClasses(): \Generator
    {
        yield 'tag static small with icon' => [
            'fr-tag fr-tag--sm fr-icon-calendar-line fr-tag--icon-left',
            static fn () => CssClassBuilder::create('fr-tag')
                ->addWhen(true, 'fr-tag--sm')
                ->addWhen(true, 'fr-icon-calendar-line')
                ->addWhen(true, 'fr-tag--icon-left'),
        ];

        yield 'button primary with icon' => [
            'fr-btn fr-icon-add fr-btn--icon-left',
            static fn () => CssClassBuilder::create('fr-btn')
                ->add(null)
                ->add('fr-icon-add')
                ->add('fr-btn--icon-left'),
        ];

        yield 'button secondary small disabled' => [
            'fr-btn fr-btn--secondary fr-btn--sm',
            static fn () => CssClassBuilder::create('fr-btn')
                ->addMap([
                    'fr-btn--secondary' => true,
                    'fr-btn--sm' => true,
                    'fr-btn--disabled' => false,
                ]),
        ];
    }
}
