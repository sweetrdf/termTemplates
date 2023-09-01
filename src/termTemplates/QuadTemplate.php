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
use rdfInterface\TermCompareInterface;
use rdfInterface\QuadCompareInterface;
use rdfInterface\DefaultGraphInterface;

/**
 * Description of QuadTemplate
 *
 * @author zozlak
 */
class QuadTemplate implements TermCompareInterface, QuadCompareInterface {

    private TermCompareInterface | null $subject;
    private TermCompareInterface | null $predicate;
    private TermCompareInterface | null $object;
    private TermCompareInterface | null $graph;
    private bool $negate;

    public function __construct(TermCompareInterface | string | null $subject = null,
                                TermCompareInterface | string | null $predicate = null,
                                TermCompareInterface | string | null $object = null,
                                TermCompareInterface | string | null $graph = null,
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

    public function withSubject(TermCompareInterface | string | null $subject = null): self {
        return new QuadTemplate($subject, $this->predicate, $this->object, $this->graph, $this->negate);
    }

    public function withPredicate(TermCompareInterface | string | null $predicate = null): self {
        return new QuadTemplate($this->subject, $predicate, $this->object, $this->graph, $this->negate);
    }

    public function withObject(TermCompareInterface | string | null $object = null): self {
        return new QuadTemplate($this->subject, $this->predicate, $object, $this->graph, $this->negate);
    }

    public function withGraph(TermCompareInterface | string | null $graph = null): self {
        return new QuadTemplate($this->subject, $this->predicate, $this->object, $graph, $this->negate);
    }

    public function withNegate(bool $negate): self {
        return new QuadTemplate($this->subject, $this->predicate, $this->object, $this->graph, $negate);
    }
}
