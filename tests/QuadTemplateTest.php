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

use zozlak\RdfConstants as RDF;
use simpleRdf\DataFactory as DF;

/**
 * Description of TermsTest
 *
 * @author zozlak
 */
class QuadTemplateTest extends \PHPUnit\Framework\TestCase {

    public function testEquals(): void {
        $bn  = DF::blankNode();
        $nn1 = DF::namedNode('foo');
        $nn2 = DF::namedNode('bar');
        $l1  = DF::literal('foo');
        $l2  = DF::literal(10, null, RDF::XSD_INT);
        $dg1 = DF::defaultGraph();
        $q   = [
            0 => DF::quad($nn1, $nn1, $nn1),
            1 => DF::quad($nn1, $nn1, $l2),
            2 => DF::quad($bn, $nn2, $l1),
            3 => DF::quad($bn, $nn2, $nn1, $dg1),
            4 => DF::quad($nn1, $nn1, $nn1, $nn2),
        ];

        $qt = [
            [
                'qt'      => new QuadTemplate($nn1),
                'matches' => [0, 1, 4]],
            [
                'qt'      => new QuadTemplate(null, $nn2->getValue()),
                'matches' => [2, 3]],
            [
                'qt'      => new QuadTemplate(null, null, $l1),
                'matches' => [2]
            ],
            [
                'qt'      => new QuadTemplate(null, null, null, $dg1),
                'matches' => [0, 1, 2, 3, 4]
            ],
            [
                'qt'      => new QuadTemplate(null, null, null, $nn2),
                'matches' => [4]
            ],
            [
                'qt'      => new QuadTemplate($bn->getValue(), $nn2),
                'matches' => [2, 3]
            ],
            [
                'qt'      => new QuadTemplate($nn2),
                'matches' => []
            ],
        ];
        foreach ($qt as $n => $i) {
            foreach ($q as $m => $j) {
                $expected = (int) in_array($m, $i['matches']);
                $this->assertEquals($expected, $i['qt']->equals($j), "equals() between QuadTemplate $n and Quad $m failed");
                
                $expected = (int) (!$expected);
                $this->assertEquals($expected, $i['qt']->withNegate(true)->equals($j), "equals() between negated QuadTemplate $n and Quad $m failed");
            }
        }

        $this->assertFalse((new QuadTemplate($nn1))->equals(DF::namedNode('foo')));
    }
    
    public function testToString(): void {
        $this->assertEquals('[foo bar baz ]', (string) (new QuadTemplate(DF::namedNode('foo'), DF::namedNode('bar'), DF::namedNode('baz'))));
    }

    public function testGetters(): void {
        $bn = DF::blankNode();
        $nn = DF::namedNode('foo');
        $l  = DF::literal('foo');
        $q  = new QuadTemplate($bn, $nn, $l);
        $this->assertEquals($bn, $q->getSubject());
        $this->assertEquals($nn, $q->getPredicate());
        $this->assertEquals($l, $q->getObject());
        $this->assertNull($q->getGraph());
    }
}
