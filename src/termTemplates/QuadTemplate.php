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

use rdfInterface\QuadInterface as iQuad;
use rdfInterface\TermInterface as iTerm;
use rdfInterface\TermCompareInterface as iTermCompare;
use rdfInterface\QuadCompareInterface as iQuadCompare;
use rdfInterface\DefaultGraphInterface as iDefaultGraph;

/**
 * Description of QuadTemplate
 *
 * @author zozlak
 */
class QuadTemplate implements iTermCompare, iQuadCompare {

    public iTermCompare | iTerm | null $subject;
    public iTermCompare | iTerm | null $predicate;
    public iTermCompare | iTerm | null $object;
    public iTermCompare | iTerm | null $graph;

    public function __construct(iTermCompare | iTerm | null $subject = null,
                                iTermCompare | iTerm | null $predicate = null,
                                iTermCompare | iTerm | null $object = null,
                                iTermCompare | iTerm | null $graph = null) {
        $this->subject   = $subject;
        $this->predicate = $predicate;
        $this->object    = $object;
        $this->graph     = $graph;
    }

    public function __toString(): string {
        return rtrim("$this->subject $this->predicate $this->object $this->graph");
    }

    public function equals(iTerm $quad): bool {
        if ($quad instanceof iQuad) {
            return ($this->subject === null || $this->subject->equals($quad->getSubject())) &&
                ($this->predicate === null || $this->predicate->equals($quad->getPredicate())) &&
                ($this->object === null || $this->object->equals($quad->getObject())) &&
                ($this->graph === null || $this->graph instanceof iDefaultGraph || $this->graph->equals($quad->getGraph()));
        } else {
            return false;
        }
    }

    public function getSubject(): iTerm | iTermCompare | null {
        return $this->subject;
    }

    public function getPredicate(): iTerm | iTermCompare | null {
        return $this->predicate;
    }

    public function getObject(): iTerm | iTermCompare | null {
        return $this->object;
    }

    public function getGraph(): iTerm | iTermCompare | null {
        return $this->graph;
    }
}
