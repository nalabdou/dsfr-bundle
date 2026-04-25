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

namespace Nalabdou\Dsfr\Tests\Formatter;

use Nalabdou\Dsfr\Formatter\ErrorMessageFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ErrorMessageFormatter::class)]
final class ErrorMessageFormatterTest extends TestCase
{
    #[Test]
    public function formatReturnsExpectedPattern(): void
    {
        $result = ErrorMessageFormatter::format('Button', 'Something went wrong');

        self::assertSame('[Button] Something went wrong.', $result);
    }

    #[Test]
    public function formatNormalizesTrailingDot(): void
    {
        // message already ends with a dot → no double dot
        $result = ErrorMessageFormatter::format('Alert', 'Bad value.');

        self::assertSame('[Alert] Bad value.', $result);
    }

    #[Test]
    public function formatWithoutTrailingDotAddsOne(): void
    {
        $result = ErrorMessageFormatter::format('Tag', 'Missing attribute');

        self::assertSame('[Tag] Missing attribute.', $result);
    }

    /** @param non-empty-string $component */
    #[Test]
    #[DataProvider('formatComponentNameProvider')]
    public function formatWrapsComponentNameInBrackets(string $component): void
    {
        $result = ErrorMessageFormatter::format($component, 'msg');

        self::assertStringStartsWith("[$component]", $result);
    }

    public static function formatComponentNameProvider(): \Generator
    {
        yield 'simple name' => ['Button'];
        yield 'compound name' => ['RadioButton'];
        yield 'lowercase' => ['accordion'];
        yield 'with numbers' => ['Header2'];
    }

    #[Test]
    public function withHintReturnsExpectedPattern(): void
    {
        $result = ErrorMessageFormatter::withHint('Modal', 'Title is missing', 'Provide a title prop');

        self::assertSame('[Modal] Title is missing. Hint: Provide a title prop.', $result);
    }

    #[Test]
    public function withHintNormalizesTrailingDotOnMessage(): void
    {
        $result = ErrorMessageFormatter::withHint('Modal', 'Title is missing.', 'Provide a title prop');

        self::assertSame('[Modal] Title is missing. Hint: Provide a title prop.', $result);
    }

    #[Test]
    public function withHintNormalizesTrailingDotOnHint(): void
    {
        $result = ErrorMessageFormatter::withHint('Modal', 'Title is missing', 'Provide a title prop.');

        self::assertSame('[Modal] Title is missing. Hint: Provide a title prop.', $result);
    }

    #[Test]
    public function withHintNormalizesTrailingDotOnBoth(): void
    {
        $result = ErrorMessageFormatter::withHint('Modal', 'Title is missing.', 'Provide a title prop.');

        self::assertSame('[Modal] Title is missing. Hint: Provide a title prop.', $result);
    }

    #[Test]
    public function withHintContainsHintKeyword(): void
    {
        $result = ErrorMessageFormatter::withHint('X', 'msg', 'hint');

        self::assertStringContainsString('Hint:', $result);
    }

    #[Test]
    public function invalidValueFormatsStringValue(): void
    {
        $result = ErrorMessageFormatter::invalidValue(
            'Select',
            'size',
            'huge',
            ['sm', 'md', 'lg'],
        );

        self::assertSame('[Select] "size" value "huge" is invalid. Accepted: [sm, md, lg].', $result);
    }

    #[Test]
    public function invalidValueFormatsIntValue(): void
    {
        $result = ErrorMessageFormatter::invalidValue('Pagination', 'step', 0, [1, 2, 5]);

        self::assertSame('[Pagination] "step" value "0" is invalid. Accepted: [1, 2, 5].', $result);
    }

    #[Test]
    public function invalidValueFormatsBoolValue(): void
    {
        $result = ErrorMessageFormatter::invalidValue('Toggle', 'checked', true, ['true', 'false']);

        self::assertSame('[Toggle] "checked" value "1" is invalid. Accepted: [true, false].', $result);
    }

    #[Test]
    public function invalidValueFormatsNullValue(): void
    {
        $result = ErrorMessageFormatter::invalidValue('Badge', 'type', null, ['success', 'error']);

        self::assertSame('[Badge] "type" value "" is invalid. Accepted: [success, error].', $result);
    }

    #[Test]
    public function invalidValueFormatsFloatValue(): void
    {
        $result = ErrorMessageFormatter::invalidValue('Slider', 'step', 0.5, [1.0, 2.0]);

        self::assertSame('[Slider] "step" value "0.5" is invalid. Accepted: [1, 2].', $result);
    }

    #[Test]
    public function invalidValueWithEmptyAcceptedArray(): void
    {
        $result = ErrorMessageFormatter::invalidValue('Foo', 'bar', 'x', []);

        self::assertSame('[Foo] "bar" value "x" is invalid. Accepted: [].', $result);
    }

    #[Test]
    public function requiredReturnsExpectedPattern(): void
    {
        $result = ErrorMessageFormatter::required('Link', 'href');

        self::assertSame('[Link] "href" is required.', $result);
    }

    #[Test]
    #[DataProvider('requiredPropNameProvider')]
    public function requiredWrapsPropertyNameInQuotes(string $propName): void
    {
        $result = ErrorMessageFormatter::required('Cmp', $propName);

        self::assertStringContainsString("\"$propName\"", $result);
    }

    public static function requiredPropNameProvider(): \Generator
    {
        yield 'simple' => ['label'];
        yield 'camelCase' => ['ariaLabel'];
        yield 'with dash' => ['data-id'];
    }

    #[Test]
    public function requiredWhenReturnsExpectedPattern(): void
    {
        $result = ErrorMessageFormatter::requiredWhen('Input', 'label', 'type', 'text');

        self::assertSame('[Input] "label" is required when "type" is "text".', $result);
    }

    #[Test]
    public function requiredWhenContainsAllFourInterpolatedValues(): void
    {
        $result = ErrorMessageFormatter::requiredWhen('A', 'propB', 'propC', 'valD');

        self::assertStringContainsString('"propB"', $result);
        self::assertStringContainsString('"propC"', $result);
        self::assertStringContainsString('"valD"', $result);
        self::assertStringStartsWith('[A]', $result);
    }

    #[Test]
    public function incompatibleReturnsExpectedPattern(): void
    {
        $result = ErrorMessageFormatter::incompatible('Card', 'shadow', 'flat', 'true');

        self::assertSame('[Card] "shadow" is not allowed when "flat" is "true".', $result);
    }

    #[Test]
    public function incompatibleContainsAllFourInterpolatedValues(): void
    {
        $result = ErrorMessageFormatter::incompatible('A', 'propB', 'propC', 'valD');

        self::assertStringContainsString('"propB"', $result);
        self::assertStringContainsString('"propC"', $result);
        self::assertStringContainsString('"valD"', $result);
        self::assertStringStartsWith('[A]', $result);
    }

    #[Test]
    public function classIsNotInstantiableFromOutside(): void
    {
        $reflection = new \ReflectionClass(ErrorMessageFormatter::class);

        self::assertFalse($reflection->getConstructor()?->isPublic());
    }

    #[Test]
    public function classIsFinal(): void
    {
        $reflection = new \ReflectionClass(ErrorMessageFormatter::class);

        self::assertTrue($reflection->isFinal());
    }
}
