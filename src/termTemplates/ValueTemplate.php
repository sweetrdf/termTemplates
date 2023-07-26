<?php

/*
 * The MIT License
 *
 * Copyright 2021 zozlak.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace termTemplates;

use rdfInterface\TermInterface as TermInterface;
use rdfInterface\TermCompareInterface as TermCompareInterface;

/**
 * Description of AnyNamedNode
 *
 * @author zozlak
 */
class ValueTemplate implements TermCompareInterface {

    const EQUALS        = '==';
    const NOT_EQUALS    = '!=';
    const STARTS        = 'starts';
    const ENDS          = 'ends';
    const CONTAINS      = 'contains';
    const GREATER       = '>';
    const LOWER         = '<';
    const GREATER_EQUAL = '>=';
    const LOWER_EQUAL   = '<=';
    const ANY           = 'any';
    const REGEX         = 'regex';

    /**
     * 
     * @var callable
     */
    private $fn;
    protected string | null $value;
    protected string $matchMode;

    public function __construct(?string $value = null,
                                string $matchMode = self::EQUALS) {
        $value           = $matchMode === self::ANY ? null : $value;
        $this->matchMode = $value === null ? self::ANY : $matchMode;
        $this->value     = $value;
        switch ($this->matchMode) {
            case self::EQUALS:
                $this->fn = function (TermInterface $term) use ($value) {
                    return $term->getValue() === $value;
                };
                break;
            case self::NOT_EQUALS:
                $this->fn = function (TermInterface $term) use ($value) {
                    return $term->getValue() !== $value;
                };
                break;
            case self::STARTS:
                $this->fn = function (TermInterface $term) use ($value): bool {
                    return str_starts_with((string) $term->getValue(), $value ?? '');
                };
                break;
            case self::ENDS:
                $this->fn = function (TermInterface $term) use ($value): bool {
                    return str_ends_with((string) $term->getValue(), $value ?? '');
                };
                break;
            case self::CONTAINS:
                $this->fn = function (TermInterface $term) use ($value): bool {
                    return str_contains((string) $term->getValue(), $value ?? '');
                };
                break;
            case self::GREATER:
                $this->fn = function (TermInterface $term) use ($value): bool {
                    return $term->getValue() > $value;
                };
                break;
            case self::LOWER;
                $this->fn = function (TermInterface $term) use ($value): bool {
                    return $term->getValue() < $value;
                };
                break;
            case self::GREATER_EQUAL:
                $this->fn = function (TermInterface $term) use ($value): bool {
                    return $term->getValue() >= $value;
                };
                break;
            case self::LOWER_EQUAL:
                $this->fn = function (TermInterface $term) use ($value): bool {
                    return $term->getValue() <= $value;
                };
                break;
            case self::REGEX:
                $this->fn = function (TermInterface $term) use ($value): bool {
                    return (bool) preg_match((string) $value, (string) $term->getValue());
                };
                break;
            case self::ANY:
                $this->fn = function (): bool {
                    return true;
                };
                break;
            default:
                throw new TermTemplatesException("Unknown match mode");
        }
    }

    public function __toString(): string {
        return "[v $this->matchMode $this->value]";
    }

    public function equals(TermCompareInterface $term): bool {
        return ($this->fn)($term);
    }
}
