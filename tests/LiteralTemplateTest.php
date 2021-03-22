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
class LiteralTemplateTest extends \PHPUnit\Framework\TestCase {

    public function testToString(): void {
        $this->assertIsString((string) new LiteralTemplate('foo'));
    }

    public function testEquals(): void {
        $l = [
            0 => DF::literal('1'),
            1 => DF::literal('1', 'eng'),
            2 => DF::literal('1', null, RDF::XSD_INTEGER),
            3 => DF::literal('foo', 'eng'),
            4 => DF::blankNode(),
        ];

        $lt = [
            0 => ['qt' => new LiteralTemplate('1'), 'matches' => [0, 1, 2]],
            1 => ['qt' => new LiteralTemplate(null, 'eng'), 'matches' => [1, 3]],
            2 => [
                'qt'      => new LiteralTemplate(null, null, RDF::XSD_INTEGER),
                'matches' => [2]
            ],
            3 => ['qt' => new LiteralTemplate('1', 'eng'), 'matches' => [1]],
            4 => [
                'qt'      => new LiteralTemplate(1, null, RDF::XSD_INTEGER),
                'matches' => [2]
            ],
        ];
        foreach ($lt as $n => $i) {
            foreach ($l as $m => $j) {
                $expected = (int) in_array($m, $i['matches']);
                $this->assertEquals($expected, $i['qt']->equals($j), "equals() between LiteralTemplate $n and Literal $m failed");
            }
        }
    }
}
