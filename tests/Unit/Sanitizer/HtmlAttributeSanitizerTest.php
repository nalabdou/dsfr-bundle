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

namespace Nalabdou\Dsfr\Tests\Sanitizer;

use Nalabdou\Dsfr\Sanitizer\HtmlAttributeSanitizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(HtmlAttributeSanitizer::class)]
final class HtmlAttributeSanitizerTest extends TestCase
{
    #[Test]
    public function protectedAriaContainsExpectedAttributes(): void
    {
        self::assertContains('aria-pressed', HtmlAttributeSanitizer::PROTECTED_ARIA);
        self::assertContains('aria-label', HtmlAttributeSanitizer::PROTECTED_ARIA);
        self::assertContains('aria-disabled', HtmlAttributeSanitizer::PROTECTED_ARIA);
        self::assertContains('aria-modal', HtmlAttributeSanitizer::PROTECTED_ARIA);
        self::assertContains('aria-labelledby', HtmlAttributeSanitizer::PROTECTED_ARIA);
        self::assertContains('aria-expanded', HtmlAttributeSanitizer::PROTECTED_ARIA);
        self::assertContains('aria-controls', HtmlAttributeSanitizer::PROTECTED_ARIA);
    }

    #[Test]
    public function protectedAriaIsAList(): void
    {
        self::assertSame(
            \array_values(HtmlAttributeSanitizer::PROTECTED_ARIA),
            HtmlAttributeSanitizer::PROTECTED_ARIA,
        );
    }

    #[Test]
    public function protectedStructuralContainsExpectedAttributes(): void
    {
        self::assertContains('role', HtmlAttributeSanitizer::PROTECTED_STRUCTURAL);
        self::assertContains('type', HtmlAttributeSanitizer::PROTECTED_STRUCTURAL);
        self::assertContains('disabled', HtmlAttributeSanitizer::PROTECTED_STRUCTURAL);
        self::assertContains('href', HtmlAttributeSanitizer::PROTECTED_STRUCTURAL);
    }

    #[Test]
    public function protectedStructuralIsAList(): void
    {
        self::assertSame(
            \array_values(HtmlAttributeSanitizer::PROTECTED_STRUCTURAL),
            HtmlAttributeSanitizer::PROTECTED_STRUCTURAL,
        );
    }

    #[Test]
    public function protectedReturnsMergeOfBothConstants(): void
    {
        $expected = \array_merge(
            HtmlAttributeSanitizer::PROTECTED_ARIA,
            HtmlAttributeSanitizer::PROTECTED_STRUCTURAL,
        );

        self::assertSame($expected, HtmlAttributeSanitizer::protected());
    }

    #[Test]
    public function protectedContainsAllAriaAttributes(): void
    {
        $protected = HtmlAttributeSanitizer::protected();

        foreach (HtmlAttributeSanitizer::PROTECTED_ARIA as $attr) {
            self::assertContains($attr, $protected);
        }
    }

    #[Test]
    public function protectedContainsAllStructuralAttributes(): void
    {
        $protected = HtmlAttributeSanitizer::protected();

        foreach (HtmlAttributeSanitizer::PROTECTED_STRUCTURAL as $attr) {
            self::assertContains($attr, $protected);
        }
    }

    #[Test]
    public function protectedReturnsAList(): void
    {
        $result = HtmlAttributeSanitizer::protected();

        self::assertSame(\array_values($result), $result);
    }

    #[Test]
    public function sanitizeWithNoProtectedArgUsesFullProtectedList(): void
    {
        $attributes = [
            'class' => 'foo',
            'aria-label' => 'Close',
            'data-id' => '42',
            'role' => 'button',
        ];

        $result = HtmlAttributeSanitizer::sanitize($attributes);

        self::assertArrayHasKey('class', $result);
        self::assertArrayHasKey('data-id', $result);
        self::assertArrayNotHasKey('aria-label', $result);
        self::assertArrayNotHasKey('role', $result);
    }

    #[Test]
    public function sanitizeRemovesAllDefaultProtectedAttributes(): void
    {
        $attributes = \array_fill_keys(HtmlAttributeSanitizer::protected(), 'value');
        $attributes['class'] = 'safe';

        $result = HtmlAttributeSanitizer::sanitize($attributes);

        self::assertSame(['class' => 'safe'], $result);
    }

    #[Test]
    public function sanitizeKeepsUnprotectedAttributes(): void
    {
        $attributes = [
            'class' => 'btn',
            'id' => 'my-btn',
            'data-track' => 'click',
            'tabindex' => '0',
        ];

        $result = HtmlAttributeSanitizer::sanitize($attributes);

        self::assertSame($attributes, $result);
    }

    #[Test]
    public function sanitizeWithEmptyInputReturnsEmptyArray(): void
    {
        self::assertSame([], HtmlAttributeSanitizer::sanitize([]));
    }

    #[Test]
    public function sanitizePreservesAttributeValues(): void
    {
        $attributes = ['class' => 'fr-btn fr-btn--primary', 'id' => 'btn-1'];

        $result = HtmlAttributeSanitizer::sanitize($attributes);

        self::assertSame('fr-btn fr-btn--primary', $result['class']);
        self::assertSame('btn-1', $result['id']);
    }

    #[Test]
    public function sanitizeWithCustomProtectedListRemovesOnlyThoseKeys(): void
    {
        $attributes = [
            'class' => 'foo',
            'aria-label' => 'bar',    // in default list but NOT in custom
            'data-x' => 'baz',
            'id' => 'qux',
        ];

        $result = HtmlAttributeSanitizer::sanitize($attributes, ['id', 'data-x']);

        self::assertArrayNotHasKey('id', $result);
        self::assertArrayNotHasKey('data-x', $result);
        self::assertArrayHasKey('class', $result);
        self::assertArrayHasKey('aria-label', $result);
    }

    #[Test]
    public function sanitizeWithEmptyCustomProtectedListFallsBackToDefault(): void
    {
        // empty $protected → falls back to self::protected()
        $attributes = ['aria-label' => 'x', 'class' => 'y'];

        $result = HtmlAttributeSanitizer::sanitize($attributes, []);

        self::assertArrayNotHasKey('aria-label', $result);
        self::assertArrayHasKey('class', $result);
    }

    #[Test]
    public function sanitizeWithCustomListDoesNotMutateInputArray(): void
    {
        $attributes = ['role' => 'button', 'class' => 'btn'];
        $original = $attributes;

        HtmlAttributeSanitizer::sanitize($attributes, ['role']);

        self::assertSame($original, $attributes);
    }

    #[Test]
    public function sanitizeWithNonExistentProtectedKeyIsNoop(): void
    {
        $attributes = ['class' => 'btn', 'id' => 'x'];

        $result = HtmlAttributeSanitizer::sanitize($attributes, ['does-not-exist']);

        self::assertSame($attributes, $result);
    }

    #[Test]
    #[DataProvider('validDataAttributeProvider')]
    public function validDataAttributeReturnsTrueForValidNames(string $name): void
    {
        self::assertTrue(HtmlAttributeSanitizer::validDataAttribute($name));
    }

    public static function validDataAttributeProvider(): \Generator
    {
        yield 'simple' => ['data-id'];
        yield 'with numbers' => ['data-item2'];
        yield 'with hyphens' => ['data-my-custom-attr'];
        yield 'single letter' => ['data-x'];
        yield 'letter then number' => ['data-a1'];
        yield 'long name' => ['data-very-long-attribute-name'];
    }

    #[Test]
    #[DataProvider('invalidDataAttributeProvider')]
    public function validDataAttributeReturnsFalseForInvalidNames(string $name): void
    {
        self::assertFalse(HtmlAttributeSanitizer::validDataAttribute($name));
    }

    public static function invalidDataAttributeProvider(): \Generator
    {
        yield 'no data prefix' => ['id'];
        yield 'data prefix only' => ['data-'];
        yield 'starts with number' => ['data-1abc'];
        yield 'uppercase letter' => ['data-Foo'];
        yield 'uppercase after data' => ['DATA-foo'];
        yield 'contains underscore' => ['data-my_attr'];
        yield 'contains space' => ['data-my attr'];
        yield 'empty string' => [''];
        yield 'aria attribute' => ['aria-label'];
        yield 'data with special chars' => ['data-foo@bar'];
    }

    #[Test]
    public function classIsNotInstantiableFromOutside(): void
    {
        $reflection = new \ReflectionClass(HtmlAttributeSanitizer::class);
        $constructor = $reflection->getConstructor();

        // Either no constructor at all, or constructor is not public
        self::assertTrue(
            null === $constructor || !$constructor->isPublic(),
        );
    }

    #[Test]
    public function classIsFinal(): void
    {
        $reflection = new \ReflectionClass(HtmlAttributeSanitizer::class);

        self::assertTrue($reflection->isFinal());
    }
}
