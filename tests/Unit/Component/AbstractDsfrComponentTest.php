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

namespace Nalabdou\Dsfr\Tests\Component;

use Nalabdou\Dsfr\Component\AbstractDsfrComponent;
use Nalabdou\Dsfr\Contract\ComponentOptionsInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[CoversClass(AbstractDsfrComponent::class)]
final class AbstractDsfrComponentTest extends TestCase
{
    /**
     * Minimal concrete implementation — no declared options.
     */
    private function makeComponent(): AbstractDsfrComponent
    {
        return new class extends AbstractDsfrComponent {
            public const string COMPONENT_NAME = 'TestComponent';
            public const string TEMPLATE = 'tests/Fixtures/template/component.html.twig';

            public function cssClass(): string
            {
                return 'fr-test';
            }
        };
    }

    /**
     * Concrete implementation with declared options.
     */
    private function makeComponentWithOptions(): AbstractDsfrComponent
    {
        return new class extends AbstractDsfrComponent {
            public const string COMPONENT_NAME = 'OptionComponent';
            public const string TEMPLATE = 'tests/Fixtures/template/option.html.twig';

            public function configureOptions(OptionsResolver $resolver): void
            {
                $resolver->define('size')
                    ->default('md')
                    ->allowedValues('sm', 'md', 'lg');

                $resolver->define('disabled')
                    ->default(false)
                    ->allowedTypes('bool');
            }

            public function cssClass(): string
            {
                return 'fr-option';
            }
        };
    }

    #[Test]
    public function classIsAbstract(): void
    {
        $reflection = new \ReflectionClass(AbstractDsfrComponent::class);

        self::assertTrue($reflection->isAbstract());
    }

    #[Test]
    public function classImplementsComponentOptionsInterface(): void
    {
        self::assertInstanceOf(
            ComponentOptionsInterface::class,
            $this->makeComponent(),
        );
    }

    #[Test]
    public function constantsAreDefinedOnAbstractClass(): void
    {
        $reflection = new \ReflectionClass(AbstractDsfrComponent::class);

        self::assertTrue($reflection->hasConstant('COMPONENT_NAME'));
        self::assertTrue($reflection->hasConstant('TEMPLATE'));
        self::assertSame('', $reflection->getConstant('COMPONENT_NAME'));
        self::assertSame('', $reflection->getConstant('TEMPLATE'));
    }

    #[Test]
    public function basePreMountIsFinal(): void
    {
        $method = new \ReflectionMethod(AbstractDsfrComponent::class, 'basePreMount');

        self::assertTrue($method->isFinal());
    }

    #[Test]
    public function basePreMountHasPreMountAttributeWithPriority100(): void
    {
        $method = new \ReflectionMethod(AbstractDsfrComponent::class, 'basePreMount');
        $attributes = $method->getAttributes(PreMount::class);

        self::assertCount(1, $attributes);

        /** @var PreMount $preMountAttr */
        $preMountAttr = $attributes[0]->newInstance();

        self::assertSame(100, $preMountAttr->priority);
    }

    #[Test]
    public function basePreMountPassesThroughUndeclaredAttributes(): void
    {
        $component = $this->makeComponent();

        $result = $component->basePreMount([
            'id' => 'my-id',
            'class' => 'extra',
            'data-foo' => 'bar',
        ]);

        self::assertArrayHasKey('id', $result);
        self::assertArrayHasKey('class', $result);
        self::assertArrayHasKey('data-foo', $result);
        self::assertSame('my-id', $result['id']);
        self::assertSame('extra', $result['class']);
        self::assertSame('bar', $result['data-foo']);
    }

    #[Test]
    public function basePreMountResolvesAndNormalizesOptionDefaults(): void
    {
        $component = $this->makeComponentWithOptions();

        $result = $component->basePreMount([]);

        self::assertSame('md', $result['size']);
        self::assertFalse($result['disabled']);
    }

    #[Test]
    public function basePreMountOverridesDefaultsWithProvidedValues(): void
    {
        $component = $this->makeComponentWithOptions();

        $result = $component->basePreMount(['size' => 'lg', 'disabled' => true]);

        self::assertSame('lg', $result['size']);
        self::assertTrue($result['disabled']);
    }

    #[Test]
    public function basePreMountResolvedPropsWinOverUndeclaredProps(): void
    {
        // Ensures "resolved + $data" left-hand keys win over right-hand keys
        $component = $this->makeComponentWithOptions();

        // 'size' is declared → resolved value wins
        $result = $component->basePreMount(['size' => 'sm']);

        self::assertSame('sm', $result['size']);
    }

    #[Test]
    public function basePreMountMergesUndeclaredPropsWithResolvedOnes(): void
    {
        $component = $this->makeComponentWithOptions();

        $result = $component->basePreMount([
            'size' => 'lg',
            'id' => 'btn-1',
            'data-foo' => 'baz',
        ]);

        self::assertSame('lg', $result['size']);
        self::assertSame('btn-1', $result['id']);
        self::assertSame('baz', $result['data-foo']);
    }

    #[Test]
    public function basePreMountThrowsOnInvalidOptionValue(): void
    {
        $component = $this->makeComponentWithOptions();

        $this->expectException(InvalidOptionsException::class);

        $component->basePreMount(['size' => 'xl']);
    }

    #[Test]
    public function configureOptionsIsNotFinal(): void
    {
        $method = new \ReflectionMethod(AbstractDsfrComponent::class, 'configureOptions');

        self::assertFalse($method->isFinal());
    }

    #[Test]
    public function configureOptionsIsOverridableBySubclass(): void
    {
        $called = false;
        $component = new class($called) extends AbstractDsfrComponent {
            public function __construct(private bool &$called)
            {
            }

            public function configureOptions(OptionsResolver $resolver): void
            {
                $this->called = true;
            }

            public function cssClass(): string
            {
                return 'fr-test';
            }
        };

        $component->basePreMount([]);

        self::assertTrue($called);
    }

    #[Test]
    public function defaultConfigureOptionsIsANoop(): void
    {
        // The base no-op implementation should not declare any options,
        // so arbitrary data passes through unchanged.
        $component = $this->makeComponent();

        $result = $component->basePreMount(['foo' => 'bar', 'baz' => 42]);

        self::assertSame('bar', $result['foo']);
        self::assertSame(42, $result['baz']);
    }

    #[Test]
    public function cssClassIsAbstract(): void
    {
        $method = new \ReflectionMethod(AbstractDsfrComponent::class, 'cssClass');

        self::assertTrue($method->isAbstract());
    }

    #[Test]
    public function cssClassReturnsValueFromConcreteImplementation(): void
    {
        self::assertSame('fr-test', $this->makeComponent()->cssClass());
    }

    #[Test]
    public function getComponentNameIsFinal(): void
    {
        $method = new \ReflectionMethod(AbstractDsfrComponent::class, 'getComponentName');

        self::assertTrue($method->isFinal());
    }

    #[Test]
    public function getComponentNameReturnsStaticConstant(): void
    {
        self::assertSame('TestComponent', $this->makeComponent()->getComponentName());
    }

    #[Test]
    public function getComponentNameUsesLateStaticBinding(): void
    {
        // Two distinct anonymous subclasses with different COMPONENT_NAME constants
        $componentA = new class extends AbstractDsfrComponent {
            public const string COMPONENT_NAME = 'ComponentA';

            public function cssClass(): string
            {
                return '';
            }
        };

        $componentB = new class extends AbstractDsfrComponent {
            public const string COMPONENT_NAME = 'ComponentB';

            public function cssClass(): string
            {
                return '';
            }
        };

        self::assertSame('ComponentA', $componentA->getComponentName());
        self::assertSame('ComponentB', $componentB->getComponentName());
    }
}
