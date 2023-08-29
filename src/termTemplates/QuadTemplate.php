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

use rdfInterface\QuadInterface;
use rdfInterface\TermInterface;
use rdfInterface\TermCompareInterface;
use rdfInterface\QuadCompareInterface;
use rdfInterface\DefaultGraphInterface;

/**
 * Description of QuadTemplate
 *
 * @author zozlak
 */
class QuadTemplate implements TermCompareInterface, QuadCompareInterface {

    public function __construct(public TermCompareInterface | TermInterface | null $subject = null,
                                public TermCompareInterface | TermInterface | null $predicate = null,
                                public TermCompareInterface | TermInterface | null $object = null,
                                public TermCompareInterface | TermInterface | null $graph = null,
                                public bool $negate = false) {
        
    }

    public function __toString(): string {
        return rtrim("[$this->subject $this->predicate $this->object $this->graph]");
    }

    public function equals(TermCompareInterface $quad): bool {
        if ($quad instanceof QuadInterface) {
            $equal = ($this->subject === null || $this->subject->equals($quad->getSubject())) &&
                ($this->predicate === null || $this->predicate->equals($quad->getPredicate())) &&
                ($this->object === null || $this->object->equals($quad->getObject())) &&
                ($this->graph === null || $this->graph instanceof DefaultGraphInterface || $this->graph->equals($quad->getGraph()));
            return $this->negate ? !$equal : $equal;
        } else {
            return false;
        }
    }

    public function getSubject(): TermCompareInterface | null {
        return $this->subject;
    }

    public function getPredicate(): TermCompareInterface | null {
        return $this->predicate;
    }

    public function getObject(): TermCompareInterface | null {
        return $this->object;
    }

    public function getGraph(): TermCompareInterface | null {
        return $this->graph;
    }
}
