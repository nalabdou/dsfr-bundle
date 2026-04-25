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

namespace Nalabdou\Dsfr\Builder;

/**
 * Fluent builder for CSS class strings.
 *
 * Accepts multiple input modes: plain strings, nullable strings, arrays,
 * associative boolean maps, and conditional additions. Null, empty strings,
 * and false-mapped entries are silently ignored. Order and duplicates are
 * preserved. {@see build()} is idempotent.
 */
final class CssClassBuilder implements \Stringable
{
    /** @var list<string> */
    private array $classes = [];

    private function __construct(string ...$base)
    {
        foreach ($base as $class) {
            $this->pushIfValid($class);
        }
    }

    public static function create(string ...$base): self
    {
        return new self(...$base);
    }

    public function add(?string $class): self
    {
        $this->pushIfValid($class);

        return $this;
    }

    /**
     * @param iterable<mixed, string|null> $classes
     */
    public function addMany(iterable $classes): self
    {
        foreach ($classes as $class) {
            $this->pushIfValid($class);
        }

        return $this;
    }

    /**
     * @param array<mixed, mixed> $classes
     */
    public function addArray(array $classes): self
    {
        foreach ($classes as $class) {
            if (\is_string($class)) {
                $this->pushIfValid($class);
            }
        }

        return $this;
    }

    /**
     * Adds classes whose corresponding boolean value is true.
     *
     * Example:
     *   $builder->addMap([
     *       'fr-btn'           => true,
     *       'fr-btn--disabled' => $this->disabled,
     *   ]);
     *
     * @param array<string, bool> $map
     */
    public function addMap(array $map): self
    {
        foreach ($map as $class => $enabled) {
            if ($enabled) {
                $this->pushIfValid($class);
            }
        }

        return $this;
    }

    public function addWhen(bool $condition, ?string $class): self
    {
        if ($condition) {
            $this->pushIfValid($class);
        }

        return $this;
    }

    public function addEither(bool $condition, ?string $ifTrue, ?string $ifFalse = null): self
    {
        $this->pushIfValid($condition ? $ifTrue : $ifFalse);

        return $this;
    }

    /**
     * @param list<string> $classes
     */
    public function addWhenArray(bool $condition, array $classes): self
    {
        if ($condition) {
            foreach ($classes as $class) {
                $this->pushIfValid($class);
            }
        }

        return $this;
    }

    public function build(): string
    {
        return \implode(' ', $this->classes);
    }

    public function __toString(): string
    {
        return $this->build();
    }

    /**
     * @return list<string>
     */
    public function toArray(): array
    {
        return $this->classes;
    }

    public function isEmpty(): bool
    {
        return [] === $this->classes;
    }

    private function pushIfValid(mixed $class): void
    {
        if (!\is_string($class)) {
            return;
        }

        $trimmed = \trim($class);

        if ('' !== $trimmed) {
            $this->classes[] = $trimmed;
        }
    }
}
