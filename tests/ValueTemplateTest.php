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

        // find all terms not equal 'foo bar'
        $tmplt  = new ValueTemplate('foo bar', ValueTemplate::NOT_EQUALS);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, true, false], $result);

        // find all terms containing 'ipsum'
        $tmplt  = new ValueTemplate('ipsum', ValueTemplate::CONTAINS);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, true, false], $result);
        $this->assertEquals('[v contains ipsum]', (string) $tmplt);

        // find all literals containing 'ipsum'
        $tmplt  = new LiteralTemplate('ipsum', ValueTemplate::CONTAINS);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, false], $result);

        // find all terms starting with 'Lorem'
        $tmplt  = new ValueTemplate('Lorem', ValueTemplate::STARTS);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, false], $result);

        // find all terms ending with 'amet'
        $tmplt  = new ValueTemplate('amet', ValueTemplate::ENDS);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, true, false], $result);

        // find all terms with string value greater than 'foo bar'
        $tmplt  = new ValueTemplate('foo bar', ValueTemplate::GREATER);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, true, false], $result);

        // find all terms with string value greater or equal than 'http'
        $tmplt  = new ValueTemplate('http', ValueTemplate::GREATER_EQUAL);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, true, false], $result);

        // find all terms with string value lower or equal than 'foo bar'
        $tmplt  = new ValueTemplate('foo bar', ValueTemplate::LOWER_EQUAL);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, true], $result);

        // find all named nodes containing 'ipsum'
        $tmplt  = new NamedNodeTemplate('ipsum', ValueTemplate::CONTAINS);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, true, false], $result);
        $this->assertEquals('[nn contains ipsum]', (string) $tmplt);

        // find all literals in latin
        $tmplt  = new LiteralTemplate(lang: 'lat');
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, false], $result);
        $this->assertEquals('[l any ""@lat^^]', (string) $tmplt);

        // find all terms with string value lower than 'http'
        $tmplt  = new LiteralTemplate('http', ValueTemplate::LOWER);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, true], $result);

        // find all terms matching a 'Lorem|foo' regular expression
        $tmplt  = new ValueTemplate('/Lorem|foo/', ValueTemplate::REGEX);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, true], $result);
        
        // match multiple values
        $tmplt  = new ValueTemplate(['Lorem', 'bar'], ValueTemplate::CONTAINS);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, true], $result);
        $this->assertEquals('[v contains [Lorem, bar]]', (string) $tmplt);
    }

    public function testWrongMatchMode(): void {
        try {
            new ValueTemplate('foo', 'bar');
            $this->assertTrue(false);
        } catch (TermTemplatesException $e) {
            $this->assertEquals("Unknown match mode", $e->getMessage());
        }
        try {
            new NumericTemplate(4, ValueTemplate::REGEX);
            $this->assertTrue(false);
        } catch (TermTemplatesException $e) {
            $this->assertEquals("Unknown match mode", $e->getMessage());
        }
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
        $this->assertEquals('[n == 2]', (string) $tmplt);

        // find terms with a value of 2 and enforce an RDF numeric type
        $tmplt  = new NumericTemplate(2, strict: true);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, false, false, true, false], $result);

        // find terms with a value greater or equal than 3
        $tmplt  = new NumericTemplate(3, ValueTemplate::GREATER_EQUAL);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, true, false, false, false], $result);

        // find terms with a value greater or equal than 2
        $tmplt  = new NumericTemplate(2, ValueTemplate::GREATER);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([false, true, false, false, false], $result);

        // find terms with a value lower or equal than 2
        $tmplt  = new NumericTemplate(2, ValueTemplate::LOWER_EQUAL);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, false, true, false], $result);

        // find terms with a value lower than 3
        $tmplt  = new NumericTemplate(3, ValueTemplate::LOWER);
        $result = array_map(fn($x) => $tmplt->equals($x), $literals);
        $this->assertEquals([true, false, false, true, false], $result);

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
        $this->assertEquals('[not [n == 2]]', (string) $tmplt);
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
        $this->assertEquals('[any {[n == 2], [nn any ]}]', (string) $tmplt);
    }
}
