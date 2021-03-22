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

use rdfInterface\Quad as iQuad;
use rdfInterface\QuadCompare as iQuadCompare;
use rdfInterface\TermCompare as iTermCompare;
use rdfInterface\DefaultGraph as iDefaultGraph;

/**
 * Description of QuadTemplate
 *
 * @author zozlak
 */
class QuadTemplate implements iQuadCompare {

    public iTermCompare | null $subject;
    public iTermCompare | null $predicate;
    public iTermCompare | null $object;
    public iTermCompare | null $graphIri;

    public function __construct(iTermCompare | null $subject = null,
                                iTermCompare | null $predicate = null,
                                iTermCompare | null $object = null,
                                iTermCompare | null $graphIri = null) {
        $this->subject   = $subject;
        $this->predicate = $predicate;
        $this->object    = $object;
        $this->graphIri  = $graphIri;
    }

    public function __toString(): string {
        return rtrim("$this->subject $this->predicate $this->object $this->graphIri");
    }

    public function equals(iQuad $quad): bool {
        return ($this->subject === null || $this->subject->equals($quad->getSubject())) &&
            ($this->predicate === null || $this->predicate->equals($quad->getPredicate())) &&
            ($this->object === null || $this->object->equals($quad->getObject())) &&
            ($this->graphIri === null || $this->graphIri instanceof iDefaultGraph || $this->graphIri->equals($quad->getGraphIri()));
    }
}
