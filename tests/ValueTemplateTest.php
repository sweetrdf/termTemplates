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
class ValueTemplateTest extends \PHPUnit\Framework\TestCase {

    public function testLiteralTemplateToString(): void {
        $this->assertIsString((string) new LiteralTemplate('foo'));
    }

    public function testLiteralTemplateEquals(): void {
        $l = [
            0 => DF::literal('1'),
            1 => DF::literal('1', 'eng'),
            2 => DF::literal('1', null, RDF::XSD_INTEGER),
            3 => DF::literal('foo', 'eng'),
            4 => DF::blankNode(),
        ];

        $lt = [
            [
                'qt'      => new LiteralTemplate('1'),
                'matches' => [0, 1, 2]
            ],
            [
                'qt'      => new LiteralTemplate(null, ValueTemplate::ANY, 'eng'),
                'matches' => [1, 3]
            ],
            [
                'qt'      => new LiteralTemplate(null, ValueTemplate::ANY, null, RDF::XSD_INTEGER),
                'matches' => [2]
            ],
            [
                'qt'      => new LiteralTemplate('1', ValueTemplate::EQUALS, 'eng'),
                'matches' => [1]
            ],
            [
                'qt'      => new LiteralTemplate((string) 1, ValueTemplate::EQUALS, null, RDF::XSD_INTEGER),
                'matches' => [2]
            ],
            [
                'qt'      => new LiteralTemplate(null, ValueTemplate::ANY, ''),
                'matches' => [1, 3]
            ],
        ];
        foreach ($lt as $n => $i) {
            foreach ($l as $m => $j) {
                $expected = (int) in_array($m, $i['matches']);
                $this->assertEquals($expected, $i['qt']->equals($j), "equals() between LiteralTemplate $n and Literal $m failed");
            }
        }
    }

    public function testValueTemplate(): void {
        $literals = [
            DF::literal('Lorem ipsum', 'lat'),
            DF::namedNode('http://ipsum.dolor/sit#amet'),
            DF::literal('foo bar'),
        ];

        // find all terms containing 'ipsum'
        $tmplt  = new ValueTemplate('ipsum', ValueTemplate::CONTAINS);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, true, false], $result);

        // find all literals containing 'ipsum'
        $tmplt  = new LiteralTemplate('ipsum', ValueTemplate::CONTAINS);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, false], $result);

        // find all named nodes containing 'ipsum'
        $tmplt  = new NamedNodeTemplate('ipsum', ValueTemplate::CONTAINS);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, true, false], $result);

        // find all literals in latin
        $tmplt  = new LiteralTemplate(lang: 'lat');
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, false], $result);

        // find all terms with string value lower than 'http'
        $tmplt  = new LiteralTemplate('http', ValueTemplate::LOWER);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, true], $result);

        // find all terms matching a 'Lorem|foo' regular expression
        $tmplt  = new ValueTemplate('/Lorem|foo/', ValueTemplate::REGEX);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, true], $result);
    }

    public function testNumericTemplate(): void {
        $literals = [
            DF::literal('2'),
            DF::literal(3, null, 'http://www.w3.org/2001/XMLSchema#int'),
            DF::literal('1foo'),
            DF::literal('2', null, 'http://www.w3.org/2001/XMLSchema#int'),
            DF::namedNode('http://ipsum.dolor/sit#amet'),
        ];

        // find terms with a value of 2
        $tmplt  = new NumericTemplate(2);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, false, true, false], $result);

        // find terms with a value of 2 and enforce an RDF numeric type
        $tmplt  = new NumericTemplate(2, strict: true);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, false, false, true, false], $result);

        // find terms with a value greate or equal than 3
        $tmplt  = new NumericTemplate(3, ValueTemplate::GREATER_EQUAL);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, true, false, false, false], $result);

        // find all terms with numeric values
        $tmplt  = new NumericTemplate(matchMode: ValueTemplate::ANY);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, true, false, true, false], $result);

        // find all numeric terms with values not equal 2
        $tmplt  = new NumericTemplate(2, ValueTemplate::NOT_EQUALS);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, true, false, false, false], $result);
    }

    public function testNotTemplate(): void {
        $literals = [
            DF::literal('2'),
            DF::literal(3, null, 'http://www.w3.org/2001/XMLSchema#int'),
            DF::literal('1foo'),
            DF::literal('2', null, 'http://www.w3.org/2001/XMLSchema#int'),
            DF::namedNode('http://ipsum.dolor/sit#amet'),
        ];
        // find all numeric terms with values not equal 2
        $tmplt    = new NotTemplate(new NumericTemplate(2));
        $result   = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, true, true, false, true], $result);
    }

    public function testAnyOfTemplate(): void {
        $literals = [
            DF::literal('2'),
            DF::literal(3, null, 'http://www.w3.org/2001/XMLSchema#int'),
            DF::literal('1foo'),
            DF::literal('2', null, 'http://www.w3.org/2001/XMLSchema#int'),
            DF::namedNode('http://ipsum.dolor/sit#amet'),
        ];

        $tmplt  = new AnyOfTemplate([
            new NumericTemplate(2),
            new NamedNodeTemplate()
        ]);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, false, true, true], $result);
    }
}
