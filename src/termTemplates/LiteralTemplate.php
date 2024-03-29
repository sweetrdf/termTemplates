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

use rdfInterface\TermCompareInterface;
use rdfInterface\LiteralInterface;

/**
 * Description of LiteralTemplate
 *
 * @author zozlak
 */
class LiteralTemplate extends ValueTemplate {

    private string | null $lang;
    private string | null $datatype;

    /**
     * 
     * @param string|array<string>|null $value
     * @param string $matchMode
     * @param string|null $lang
     * @param string|null $datatype
     */
    public function __construct(string | array | null $value = null,
                                string $matchMode = self::EQUALS,
                                string | null $lang = null,
                                string | null $datatype = null) {
        parent::__construct($value, $matchMode);
        $this->lang     = $lang;
        $this->datatype = $datatype;
    }

    public function __toString(): string {
        $value = match (count($this->value)) {
            0 => '',
            1 => $this->value[0],
            default => '[' . implode(', ', $this->value) . ']'
        };
        return "[l $this->matchMode \"$value\"@$this->lang^^$this->datatype]";
    }

    public function equals(TermCompareInterface $term): bool {
        if ($term instanceof LiteralInterface) {
            return parent::equals($term) &&
                ($this->lang === null || $this->lang === '' && !empty($term->getLang()) || $this->lang === $term->getLang()) &&
                ($this->datatype === null || $this->datatype === $term->getDatatype());
        } else {
            return false;
        }
    }
}
