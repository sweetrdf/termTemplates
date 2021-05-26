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

use rdfInterface\Term as iTerm;
use rdfInterface\TermCompare as iTermCompare;

/**
 * Description of AnyNamedNode
 *
 * @author zozlak
 */
class ValueTemplate implements iTermCompare {

    const EQUALS        = 1;
    const STARTS        = 2;
    const ENDS          = 3;
    const CONTAINS      = 4;
    const GREATER       = 5;
    const LOWER         = 6;
    const GREATER_EQUAL = 6;
    const LOWER_EQUAL   = 7;
    const ANY           = 8;

    /**
     * 
     * @var callable
     */
    private $fn;
    protected string | null $value;
    protected string $matchMode;

    public function __construct(?string $value, int $matchMode = self::EQUALS) {
        $value       = $matchMode === self::ANY ? null : $value;
        $matchMode   = $value === null ? self::ANY : $matchMode;
        $this->value = $value;
        switch ($matchMode) {
            case self::EQUALS:
                $this->matchMode = '==';
                $this->fn        = function(iTerm $term) use($value) {
                    return $term->getValue() === $value;
                };
                break;
            case self::STARTS:
                $this->matchMode = 'startswith';
                $this->fn        = function(iTerm $term) use($value): bool {
                    return str_starts_with($term->getValue(), $value ?? '');
                };
                break;
            case self::ENDS:
                $this->matchMode = 'endswith';
                $this->fn        = function(iTerm $term) use($value): bool {
                    return str_ends_with($term->getValue(), $value ?? '');
                };
                break;
            case self::CONTAINS:
                $this->matchMode = 'contains';
                $this->fn        = function(iTerm $term) use($value): bool {
                    return str_contains($term->getValue(), $value ?? '');
                };
                break;
            case self::GREATER:
                $this->matchMode = '>';
                $this->fn        = function(iTerm $term) use($value): bool {
                    return $term->getValue() > $value;
                };
                break;
            case self::LOWER;
                $this->matchMode = '<';
                $this->fn        = function(iTerm $term) use($value): bool {
                    return $term->getValue() < $value;
                };
                break;
            case self::GREATER_EQUAL:
                $this->matchMode = '>=';
                $this->fn        = function(iTerm $term) use($value): bool {
                    return $term->getValue() >= $value;
                };
                break;
            case self::LOWER_EQUAL:
                $this->matchMode = '<=';
                $this->fn        = function(iTerm $term) use($value): bool {
                    return $term->getValue() <= $value;
                };
                break;
            case self::ANY:
                $this->matchMode = 'any';
                $this->fn        = function(): bool {
                    return true;
                };
                break;
        }
    }

    public function __toString(): string {
        return "[v $this->matchMode $this->value]";
    }

    public function equals(iTerm $term): bool {
        return ($this->fn)($term);
    }
}
