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
use rdfInterface\QuadCompareInterface;

/**
 * Description of QuadTemplate
 *
 * @author zozlak
 */
class PredicateTemplate implements TermCompareInterface, QuadCompareInterface {

    private QuadTemplate $tmpl;

    public function __construct(TermCompareInterface | string | null $predicate = null,
                                TermCompareInterface | string | null $object = null,
                                TermCompareInterface | string | null $graph = null,
                                bool $negate = false) {
        $this->tmpl = new QuadTemplate(null, $predicate, $object, $graph, $negate);
    }

    public function __toString(): string {
        return (string) $this->tmpl;
    }

    public function equals(TermCompareInterface $quad): bool {
        return $this->tmpl->equals($quad);
    }

    public function getSubject(): TermCompareInterface | null {
        return $this->tmpl->getSubject();
    }

    public function getPredicate(): TermCompareInterface | null {
        return $this->tmpl->getPredicate();
    }

    public function getObject(): TermCompareInterface | null {
        return $this->tmpl->getObject();
    }

    public function getGraph(): TermCompareInterface | null {
        return $this->tmpl->getGraph();
    }

    public function withSubject(TermCompareInterface | string | null $subject = null): QuadTemplate {
        return $this->tmpl->withSubject($subject);
    }

    public function withPredicate(TermCompareInterface | string | null $predicate = null): QuadTemplate {
        return $this->tmpl->withPredicate($predicate);
    }

    public function withObject(TermCompareInterface | string | null $object = null): QuadTemplate {
        return $this->tmpl->withObject($object);
    }

    public function withGraph(TermCompareInterface | string | null $graph = null): QuadTemplate {
        return $this->tmpl->withGraph($graph);
    }

    public function withNegate(bool $negate): QuadTemplate {
        return $this->tmpl->withNegate($negate);
    }
}
