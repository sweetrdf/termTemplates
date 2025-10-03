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

use zozlak\RdfConstants as RDF;
use rdfInterface\LiteralInterface;
use rdfInterface\TermCompareInterface as TermCompareInterface;

/**
 * Description of AnyNamedNode
 *
 * @author zozlak
 */
class NumericTemplate extends ValueTemplate {

    /**
     * 
     * @var array<string>
     */
    static private $numericTypes = [
        RDF::XSD_INTEGER,
        RDF::XSD_DECIMAL,
        RDF::XSD_BYTE,
        RDF::XSD_SHORT,
        RDF::XSD_INT,
        RDF::XSD_LONG,
        RDF::XSD_FLOAT,
        RDF::XSD_DOUBLE,
        RDF::XSD_G_DAY,
        RDF::XSD_G_MONTH,
        RDF::XSD_G_YEAR,
        RDF::XSD_NEGATIVE_INTEGER,
        RDF::XSD_NON_NEGATIVE_INTEGER,
        RDF::XSD_NON_POSITIVE_INTEGER,
        RDF::XSD_POSITIVE_INTEGER,
        RDF::XSD_UNSIGNED_BYTE,
        RDF::XSD_UNSIGNED_SHORT,
        RDF::XSD_UNSIGNED_INT,
        RDF::XSD_UNSIGNED_LONG,
    ];

    /**
     * 
     * @var callable
     */
    private $fn;
    private bool $strict;
    protected float | null $fvalue;

    public function __construct(float | null $value = null,
                                string $matchMode = self::EQUALS,
                                bool $strict = false) {
        $value           = $matchMode === self::ANY ? null : $value;
        $this->matchMode = $value === null ? self::ANY : $matchMode;
        $this->fvalue     = $value;
        $this->strict    = $strict;
        switch ($this->matchMode) {
            case self::EQUALS:
                $this->fn = function (float $termValue) use ($value) {
                    return $termValue === $value;
                };
                break;
            case self::NOT_EQUALS:
                $this->fn = function (float $termValue) use ($value) {
                    return $termValue !== $value;
                };
                break;
            case self::GREATER:
                $this->fn = function (float $termValue) use ($value): bool {
                    return $termValue > $value;
                };
                break;
            case self::LOWER:
                $this->fn = function (float $termValue) use ($value): bool {
                    return $termValue < $value;
                };
                break;
            case self::GREATER_EQUAL:
                $this->fn = function (float $termValue) use ($value): bool {
                    return $termValue >= $value;
                };
                break;
            case self::LOWER_EQUAL:
                $this->fn = function (float $termValue) use ($value): bool {
                    return $termValue <= $value;
                };
                break;
            case self::ANY:
                $this->fn = function (float $termValue): bool {
                    return true;
                };
                break;
            default:
                throw new TermTemplatesException("Unknown match mode");
        }
    }

    public function __toString(): string {
        $strict = $this->strict ? 'strict ' : '';
        return "[n $strict$this->matchMode $this->fvalue]";
    }

    public function equals(TermCompareInterface $term): bool {
        if ($term instanceof LiteralInterface) {
            return is_numeric($term->getValue()) && ($this->fn)((float) $term->getValue()) &&
                ($this->strict === false || in_array($term->getDatatype(), self::$numericTypes));
        } else {
            return false;
        }
    }
}
