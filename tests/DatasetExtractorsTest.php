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
use simpleRdf\Dataset;
use simpleRdf\DataFactory as DF;
use termTemplates\DatasetExtractors as DE;

/**
 * Description of TermsTest
 *
 * @author zozlak
 */
class DatasetExtractorsTest extends \PHPUnit\Framework\TestCase {

    private function compare(array $expected, mixed $actual): void {
        $multipleValues = true;
        if (!is_array($actual)) {
            $actual         = [$actual];
            $multipleValues = false;
        }
        $actualStr = '';
        foreach ($actual as $a) {
            foreach ($expected as $e) {
                if ($a->equals($e)) {
                    $actualStr .= $a . ' ';
                    continue 2;
                }
            }
            $this->assertTrue(false, "Unexpected $a");
        }
        if ($multipleValues) {
            foreach ($expected as $e) {
                foreach ($actual as $a) {
                    if ($e->equals($a)) {
                        continue 2;
                    }
                }
                $this->assertTrue(false, "$e not found among $actualStr");
            }
        }
        $this->assertTrue(true);
    }

    public function testEmpty(): void {
        $d = new Dataset();
        $this->assertNull(DE::getSubject($d));
        $this->assertNull(DE::getSubjectValue($d));
        $this->assertNull(DE::getPredicate($d));
        $this->assertNull(DE::getPredicateUri($d));
        $this->assertNull(DE::getObject($d));
        $this->assertNull(DE::getObjectValue($d));
        $this->assertNull(DE::getObjectLang($d));
        $this->assertNull(DE::getObjectDatatype($d));
        $this->assertNull(DE::getLiteral($d));
        $this->assertNull(DE::getLiteralValue($d));
        $this->assertNull(DE::getGraph($d));
        $this->assertNull(DE::getGraphUri($d));
        $this->assertEquals([], DE::getSubjects($d));
        $this->assertEquals([], DE::getSubjectValues($d));
        $this->assertEquals([], DE::getPredicates($d));
        $this->assertEquals([], DE::getPredicateUris($d));
        $this->assertEquals([], DE::getObjects($d));
        $this->assertEquals([], DE::getLiterals($d));
        $this->assertEquals([], DE::getLiteralValues($d));
        $this->assertEquals([], DE::getLiteralValuesByLang($d));
        $this->assertEquals([], DE::getObjectValues($d));
        $this->assertEquals([], DE::getGraphs($d));
        $this->assertEquals([], DE::getGraphUris($d));
    }

    public function testDatasetExtractorsSingle(): void {
        $d   = new Dataset();
        $s   = DF::namedNode('s');
        $p   = DF::namedNode('p');
        $o   = DF::literal('o', 'en', RDF::RDF_LANG_STRING);
        $g   = DF::namedNode('g');
        $d[] = DF::quad($s, $p, $o, $g);

        $this->assertEquals($s, DE::getSubject($d));
        $this->assertEquals('s', DE::getSubjectValue($d));
        $this->assertEquals($p, DE::getPredicate($d));
        $this->assertEquals('p', DE::getPredicateUri($d));
        $this->assertEquals($o, DE::getObject($d));
        $this->assertEquals('o', DE::getObjectValue($d));
        $this->assertEquals('en', DE::getObjectLang($d));
        $this->assertEquals(RDF::RDF_LANG_STRING, DE::getObjectDatatype($d));
        $this->assertEquals($o, DE::getLiteral($d));
        $this->assertEquals('o', DE::getLiteralValue($d));
        $this->assertEquals($g, DE::getGraph($d));
        $this->assertEquals('g', DE::getGraphUri($d));
        $this->assertEquals([$s], DE::getSubjects($d));
        $this->assertEquals(['s'], DE::getSubjectValues($d));
        $this->assertEquals([$p], DE::getPredicates($d));
        $this->assertEquals(['p'], DE::getPredicateUris($d));
        $this->assertEquals([$o], DE::getObjects($d));
        $this->assertEquals([$o], DE::getLiterals($d));
        $this->assertEquals(['o'], DE::getLiteralValues($d));
        $this->assertEquals(['en' => 'o'], DE::getLiteralValuesByLang($d));
        $this->assertEquals(['o'], DE::getObjectValues($d));
        $this->assertEquals([$g], DE::getGraphs($d));
        $this->assertEquals(['g'], DE::getGraphUris($d));
    }

    public function testDatasetExtractorsMultiple(): void {
        $d   = new Dataset();
        $s1  = DF::namedNode('s1');
        $p1  = DF::namedNode('p1');
        $o1  = DF::literal('l1', 'en', RDF::RDF_LANG_STRING);
        $g1  = DF::namedNode('g1');
        $s2  = DF::namedNode('s2');
        $p2  = DF::namedNode('p2');
        $o2  = DF::namedNode('o2');
        $g2  = DF::namedNode('g2');
        $d[] = DF::quad($s1, $p1, $o1, $g1);
        $d[] = DF::quad($s2, $p2, $o2, $g2);

        $this->compare([$s1, $s2], DE::getSubject($d));
        $this->assertContains(DE::getSubjectValue($d), ['s1', 's2']);
        $this->compare([$p1, $p2], DE::getPredicate($d));
        $this->assertContains(DE::getPredicateUri($d), ['p1', 'p2']);
        $this->compare([$o1, $o2], DE::getObject($d));
        $this->assertContains(DE::getObjectValue($d), ['l1', 'o2']);
        $this->assertContains(DE::getObjectLang($d), ['en', null]);
        $this->assertContains(DE::getObjectDatatype($d), [RDF::RDF_LANG_STRING, null]);
        $this->compare([$o1, null], DE::getLiteral($d));
        $this->assertContains(DE::getLiteralValue($d), ['l1', 'o2']);
        $this->compare([$g1, $g2], DE::getGraph($d));
        $this->assertContains(DE::getGraphUri($d), ['g1', 'g2']);
        $this->compare([$s1, $s2], DE::getSubjects($d));
        $this->assertEqualsCanonicalizing(['s1', 's2'], DE::getSubjectValues($d));
        $this->compare([$p1, $p2], DE::getPredicates($d));
        $this->assertEqualsCanonicalizing(['p1', 'p2'], DE::getPredicateUris($d));
        $this->compare([$o1, $o2], DE::getObjects($d));
        $this->compare([$o1], DE::getLiterals($d));
        $this->assertEquals(['l1'], DE::getLiteralValues($d));
        $this->assertEquals(['en' => 'l1'], DE::getLiteralValuesByLang($d));
        $this->assertEqualsCanonicalizing(['l1', 'o2'], DE::getObjectValues($d));
        $this->compare([$g1, $g2], DE::getGraphs($d));
        $this->assertEqualsCanonicalizing(['g1', 'g2'], DE::getGraphUris($d));
    }
    
    public function testDatasetExtractorsNamedNodeObject(): void {
        $d   = new Dataset();
        $s   = DF::namedNode('s');
        $p   = DF::namedNode('p');
        $o   = DF::namedNode('o');
        $g   = DF::namedNode('g');
        $d[] = DF::quad($s, $p, $o, $g);
        
        $this->assertNull(DE::getObjectLang($d));
        $this->assertNull(DE::getObjectDatatype($d));
        $this->assertNull(DE::getLiteral($d));
        $this->assertEquals([], DE::getLiterals($d));
        $this->assertEquals([], DE::getLiteralValues($d));
        $this->assertEquals([], DE::getLiteralValuesByLang($d));
    }
}
