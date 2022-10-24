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

use rdfInterface\TermInterface as iTerm;
use rdfInterface\TermCompareInterface as iTermCompare;

/**
 * Description of AnyOfTemplate
 *
 * @author zozlak
 */
class AnyOfTemplate implements iTermCompare {

    /**
     * 
     * @var iterable<iTerm>
     */
    private iterable $terms;

    /**
     * 
     * @param iterable<iTerm> $terms
     */
    public function __construct(iterable $terms) {
        $this->terms = $terms;
    }

    public function __toString(): string {
        $ret = "[any {";
        foreach ($this->terms as $n => $i) {
            $ret .= ($n > 0 ? ', ' : '') . (string) $i;
        }
        $ret .= '}]';
        return $ret;
    }

    public function equals(iTerm $term): bool {
        foreach ($this->terms as $i) {
            if ($i->equals($term)) {
                return true;
            }
        }
        return false;
    }
}
