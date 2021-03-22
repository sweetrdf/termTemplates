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
use rdfInterface\Literal as iLiteral;

/**
 * Description of LiteralTemplate
 *
 * @author zozlak
 */
class LiteralTemplate implements iTermCompare {

    public string | null $value;
    public string | null $lang;
    public string | null $datatype;

    public function __construct(string | null $value = null,
                                string | null $lang = null,
                                string | null $datatype = null) {
        $this->value    = $value;
        $this->lang     = $lang;
        $this->datatype = $datatype;
    }

    public function __toString(): string {
        return "$this->value $this->lang $this->datatype";
    }

    public function equals(iTerm $term): bool {
        if ($term instanceof iLiteral) {
            return ($this->value === null || $this->value === $term->getValue()) &&
                ($this->lang === null || $this->lang === '' && !empty($term->getLang()) || $this->lang === $term->getLang()) &&
                ($this->datatype === null || $this->datatype === $term->getDatatype());
        } else {
            return false;
        }
    }
}
