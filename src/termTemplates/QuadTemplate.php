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

    public TermCompareInterface | TermInterface | null $subject;
    public TermCompareInterface | TermInterface | null $predicate;
    public TermCompareInterface | TermInterface | null $object;
    public TermCompareInterface | TermInterface | null $graph;
    public bool $negate;

    public function __construct(TermCompareInterface | TermInterface | string | null $subject = null,
                                TermCompareInterface | TermInterface | string | null $predicate = null,
                                TermCompareInterface | TermInterface | string | null $object = null,
                                TermCompareInterface | TermInterface | string | null $graph = null,
                                bool $negate = false) {
        $this->subject   = is_string($subject) ? new ValueTemplate($subject) : $subject;
        $this->predicate = is_string($predicate) ? new ValueTemplate($predicate) : $predicate;
        $this->object    = is_string($object) ? new ValueTemplate($object) : $object;
        $this->graph     = is_string($graph) ? new ValueTemplate($graph) : $graph;
        $this->negate    = $negate;
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
