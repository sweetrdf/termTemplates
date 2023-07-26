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

use rdfInterface\BlankNodeInterface;
use rdfInterface\DatasetInterface;
use rdfInterface\DefaultGraphInterface;
use rdfInterface\LiteralInterface;
use rdfInterface\NamedNodeInterface;
use rdfInterface\QuadCompareInterface;
use rdfInterface\QuadIteratorInterface;
use rdfInterface\TermInterface;
use rdfInterface\QuadInterface;

/**
 * Provides shorthand methods for extracting values from a Dataset without
 * a need to bother with iterators
 *
 * @author zozlak
 */
class DatasetExtractors {

    /**
     * 
     * @return array<mixed>
     */
    static private function getValues(DatasetInterface $dataset,
                                      QuadCompareInterface | QuadIteratorInterface | callable | null $filter,
                                      string $method): array {
        $values = [];
        foreach ($dataset->getIterator($filter) as $quad) {
            $values[] = $quad->$method()->getValue();
        }
        return $values;
    }

    static private function getSingle(DatasetInterface $dataset,
                                      QuadCompareInterface | QuadIteratorInterface | callable $filter = null): ?QuadInterface {
        $iterator = $dataset->getIterator($filter);
        return $iterator->valid() ? $iterator->current() : null;
    }

    static public function getSubject(DatasetInterface $dataset,
                                      QuadCompareInterface | QuadIteratorInterface | callable $filter = null): ?TermInterface {
        return self::getSingle($dataset, $filter)?->getSubject();
    }

    static public function getSubjectValue(DatasetInterface $dataset,
                                           QuadCompareInterface | QuadIteratorInterface | callable $filter = null): mixed {
        return self::getSubject($dataset, $filter)?->getValue();
    }

    /**
     * 
     * @return array<TermInterface>
     */
    static public function getSubjects(DatasetInterface $dataset,
                                       QuadCompareInterface | QuadIteratorInterface | callable $filter = null): array {
        return iterator_to_array($dataset->listSubjects($filter));
    }

    /**
     * 
     * @return array<mixed>
     */
    static public function getSubjectValues(DatasetInterface $dataset,
                                            QuadCompareInterface | QuadIteratorInterface | callable $filter = null): array {
        return self::getValues($dataset, $filter, 'getSubject');
    }

    static public function getPredicate(DatasetInterface $dataset,
                                        QuadCompareInterface | QuadIteratorInterface | callable $filter = null): ?NamedNodeInterface {
        return self::getSingle($dataset, $filter)?->getPredicate();
    }

    static public function getPredicateUri(DatasetInterface $dataset,
                                           QuadCompareInterface | QuadIteratorInterface | callable $filter = null): ?string {
        return self::getPredicate($dataset, $filter)?->getValue();
    }

    /**
     * 
     * @return array<NamedNodeInterface>
     */
    static public function getPredicates(DatasetInterface $dataset,
                                         QuadCompareInterface | QuadIteratorInterface | callable $filter = null): array {
        return iterator_to_array($dataset->listPredicates($filter));
    }

    /**
     * 
     * @return array<string>
     */
    static public function getPredicateUris(DatasetInterface $dataset,
                                            QuadCompareInterface | QuadIteratorInterface | callable $filter = null): array {
        return self::getValues($dataset, $filter, 'getPredicate');
    }

    static public function getObject(DatasetInterface $dataset,
                                     QuadCompareInterface | QuadIteratorInterface | callable $filter = null): ?TermInterface {
        return self::getSingle($dataset, $filter)?->getObject();
    }

    static public function getObjectValue(DatasetInterface $dataset,
                                          QuadCompareInterface | QuadIteratorInterface | callable $filter = null): mixed {
        return self::getObject($dataset, $filter)?->getValue();
    }

    static public function getObjectLang(DatasetInterface $dataset,
                                         QuadCompareInterface | QuadIteratorInterface | callable $filter = null): ?string {
        $object = self::getObject($dataset, $filter);
        if ($object !== null && method_exists($object, 'getLang')) {
            return $object->getLang();
        } else {
            return null;
        }
    }

    static public function getObjectDatatype(DatasetInterface $dataset,
                                             QuadCompareInterface | QuadIteratorInterface | callable $filter = null): ?string {
        $object = self::getObject($dataset, $filter);
        if ($object !== null && method_exists($object, 'getDatatype')) {
            return $object->getDatatype();
        } else {
            return null;
        }
    }

    /**
     * 
     * @return array<TermInterface>
     */
    static public function getObjects(DatasetInterface $dataset,
                                      QuadCompareInterface | QuadIteratorInterface | callable $filter = null): array {
        return iterator_to_array($dataset->listObjects($filter));
    }

    static public function getLiteral(DatasetInterface $dataset,
                                      QuadCompareInterface | QuadIteratorInterface | callable $filter = null): ?LiteralInterface {
        foreach ($dataset->getIterator($filter) as $quad) {
            $object = $quad->getObject();
            if ($object instanceof LiteralInterface) {
                return $object;
            }
        }
        return null;
    }

    static public function getLiteralValue(DatasetInterface $dataset,
                                           QuadCompareInterface | QuadIteratorInterface | callable $filter = null): mixed {
        return self::getLiteral($dataset, $filter)?->getValue();
    }

    /**
     * 
     * @return array<LiteralInterface>
     */
    static public function getLiterals(DatasetInterface $dataset,
                                       QuadCompareInterface | QuadIteratorInterface | callable $filter = null): array {
        $literals = [];
        foreach ($dataset->getIterator($filter) as $quad) {
            $object = $quad->getObject();
            if ($object instanceof LiteralInterface) {
                $literals[] = $object;
            }
        }
        return $literals;
    }

    /**
     * 
     * @return array<mixed>
     */
    static public function getLiteralValues(DatasetInterface $dataset,
                                            QuadCompareInterface | QuadIteratorInterface | callable $filter = null): array {
        $values = [];
        foreach ($dataset->getIterator($filter) as $quad) {
            $object = $quad->getObject();
            if ($object instanceof LiteralInterface) {
                $values[] = $object->getValue();
            }
        }
        return $values;
    }

    /**
     * Returns an array of all object literal values with literals' language
     * taken as an array keys and literals' string value taken as values.
     * 
     * If there are many object literals with the same language tag, the last
     * one is stored under the given language key.
     * 
     * No-language tag object literal value is stored under the empty string key.
     * @return array<mixed>
     */
    static public function getLiteralValuesByLang(DatasetInterface $dataset,
                                                  QuadCompareInterface | QuadIteratorInterface | callable $filter = null): array {
        $values = [];
        foreach ($dataset->getIterator($filter) as $quad) {
            $object = $quad->getObject();
            if ($object instanceof LiteralInterface) {
                $values[$object->getLang() ?? ''] = $object->getValue();
            }
        }
        return $values;
    }

    /**
     * 
     * @return array<mixed>
     */
    static public function getObjectValues(DatasetInterface $dataset,
                                           QuadCompareInterface | QuadIteratorInterface | callable $filter = null): array {
        return self::getValues($dataset, $filter, 'getObject');
    }

    static public function getGraph(DatasetInterface $dataset,
                                    QuadCompareInterface | QuadIteratorInterface | callable $filter = null): NamedNodeInterface | BlankNodeInterface | DefaultGraphInterface | null {
        return self::getSingle($dataset, $filter)?->getGraph();
    }

    static public function getGraphUri(DatasetInterface $dataset,
                                       QuadCompareInterface | QuadIteratorInterface | callable $filter = null): ?string {
        return self::getGraph($dataset, $filter)?->getValue();
    }

    /**
     * 
     * @return array<NamedNodeInterface | BlankNodeInterface | DefaultGraphInterface>
     */
    static public function getGraphs(DatasetInterface $dataset,
                                     QuadCompareInterface | QuadIteratorInterface | callable $filter = null): array {
        return iterator_to_array($dataset->listGraphs($filter));
    }

    /**
     * 
     * @return array<string>
     */
    static public function getGraphUris(DatasetInterface $dataset,
                                        QuadCompareInterface | QuadIteratorInterface | callable $filter = null): array {
        return self::getValues($dataset, $filter, 'getGraph');
    }
}
