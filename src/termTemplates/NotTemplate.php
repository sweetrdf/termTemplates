<?php

/*
 * The MIT License
 *
 * Copyright 2022 zozlak.
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
use rdfInterface\QuadCompareInterface;

/**
 * Provides condition negation 
 *
 * @author zozlak
 */
class NotTemplate implements TermCompareInterface, QuadCompareInterface {

    private TermCompareInterface | QuadCompareInterface $term;

    public function __construct(TermCompareInterface | QuadCompareInterface $term) {
        $this->term = $term;
    }

    public function __toString(): string {
        return '[not ' . $this->term . ']';
    }

    public function equals(TermCompareInterface | QuadCompareInterface $term): bool {
        return !$this->term->equals($term);
    }

    public function getGraph(): TermCompareInterface | null {
        return $this->term instanceof QuadCompareInterface ? $this->term->getGraph() : null;
    }

    public function getObject(): TermCompareInterface | null {
        return $this->term instanceof QuadCompareInterface ? $this->term->getObject() : null;
    }

    public function getPredicate(): TermCompareInterface | null {
        return $this->term instanceof QuadCompareInterface ? $this->term->getPredicate() : null;
    }

    public function getSubject(): TermCompareInterface | null {
        return $this->term instanceof QuadCompareInterface ? $this->term->getSubject() : null;
    }
}
