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

use Exception;
use rdfInterface\BlankNodeInterface as iBlankNode;
use rdfInterface\DatasetInterface as iDataset;
use rdfInterface\DatasetListQuadPartsInterface as iDatasetListQuadParts;
use rdfInterface\DefaultGraphInterface as iDefaultGraph;
use rdfInterface\LiteralInterface as iLiteral;
use rdfInterface\NamedNodeInterface as iNamedNode;
use rdfInterface\QuadCompareInterface as iQuadCompare;
use rdfInterface\QuadIteratorInterface as iQuadIterator;
use rdfInterface\TermInterface as iTerm;
use rdfInterface\QuadInterface as iQuad;

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
    static private function getValues(iDataset $dataset,
                                      iQuadCompare | iQuadIterator | callable | null $filter,
                                      string $method): array {
        $values = [];
        foreach ($dataset->getIterator($filter) as $quad) {
            $values[] = $quad->$method()->getValue();
        }
        return $values;
    }

    static private function getSingle(iDataset $dataset,
                                      iQuadCompare | iQuadIterator | callable $filter = null): ?iQuad {
        $iterator = $dataset->getIterator($filter);
        return $iterator->valid() ? $iterator->current() : null;
    }

    static public function getSubject(iDataset $dataset,
                                      iQuadCompare | iQuadIterator | callable $filter = null): ?iTerm {
        return self::getSingle($dataset, $filter)?->getSubject();
    }

    static public function getSubjectValue(iDataset $dataset,
                                           iQuadCompare | iQuadIterator | callable $filter = null): mixed {
        return self::getSubject($dataset, $filter)?->getValue();
    }

    /**
     * 
     * @return array<iTerm>
     */
    static public function getSubjects(iDatasetListQuadParts $dataset,
                                       iQuadCompare | iQuadIterator | callable $filter = null): array {
        return iterator_to_array($dataset->listSubjects($filter));
    }

    /**
     * 
     * @return array<mixed>
     */
    static public function getSubjectValues(iDatasetListQuadParts $dataset,
                                            iQuadCompare | iQuadIterator | callable $filter = null): array {
        return self::getValues($dataset, $filter, 'getSubject');
    }

    static public function getPredicate(iDataset $dataset,
                                        iQuadCompare | iQuadIterator | callable $filter = null): ?iNamedNode {
        return self::getSingle($dataset, $filter)?->getPredicate();
    }

    static public function getPredicateUri(iDataset $dataset,
                                           iQuadCompare | iQuadIterator | callable $filter = null): ?string {
        return self::getPredicate($dataset, $filter)?->getValue();
    }

    /**
     * 
     * @return array<iNamedNode>
     */
    static public function getPredicates(iDatasetListQuadParts $dataset,
                                         iQuadCompare | iQuadIterator | callable $filter = null): array {
        return iterator_to_array($dataset->listPredicates($filter));
    }

    /**
     * 
     * @return array<string>
     */
    static public function getPredicateUris(iDatasetListQuadParts $dataset,
                                            iQuadCompare | iQuadIterator | callable $filter = null): array {
        return self::getValues($dataset, $filter, 'getPredicate');
    }

    static public function getObject(iDataset $dataset,
                                     iQuadCompare | iQuadIterator | callable $filter = null): ?iTerm {
        return self::getSingle($dataset, $filter)?->getObject();
    }

    static public function getObjectValue(iDataset $dataset,
                                          iQuadCompare | iQuadIterator | callable $filter = null): mixed {
        return self::getObject($dataset, $filter)?->getValue();
    }

    static public function getObjectLang(iDataset $dataset,
                                         iQuadCompare | iQuadIterator | callable $filter = null): ?string {
        $object = self::getObject($dataset, $filter);
        if ($object !== null && method_exists($object, 'getLang')) {
            return $object->getLang();
        } else {
            return null;
        }
    }

    static public function getObjectDatatype(iDataset $dataset,
                                             iQuadCompare | iQuadIterator | callable $filter = null): ?string {
        $object = self::getObject($dataset, $filter);
        if ($object !== null && method_exists($object, 'getDatatype')) {
            return $object->getDatatype();
        } else {
            return null;
        }
    }

    /**
     * 
     * @return array<iTerm>
     */
    static public function getObjects(iDatasetListQuadParts $dataset,
                                      iQuadCompare | iQuadIterator | callable $filter = null): array {
        return iterator_to_array($dataset->listObjects($filter));
    }

    static public function getLiteral(iDatasetListQuadParts $dataset,
                                      iQuadCompare | iQuadIterator | callable $filter = null): ?iLiteral {
        foreach ($dataset->getIterator($filter) as $quad) {
            $object = $quad->getObject();
            if ($object instanceof iLiteral) {
                return $object;
            }
        }
        return null;
    }

    static public function getLiteralValue(iDatasetListQuadParts $dataset,
                                           iQuadCompare | iQuadIterator | callable $filter = null): mixed {
        return self::getLiteral($dataset, $filter)?->getValue();
    }

    /**
     * 
     * @return array<iLiteral>
     */
    static public function getLiterals(iDatasetListQuadParts $dataset,
                                       iQuadCompare | iQuadIterator | callable $filter = null): array {
        $literals = [];
        foreach ($dataset->getIterator($filter) as $quad) {
            $object = $quad->getObject();
            if ($object instanceof iLiteral) {
                $literals[] = $object;
            }
        }
        return $literals;
    }

    /**
     * 
     * @return array<mixed>
     */
    static public function getLiteralValues(iDatasetListQuadParts $dataset,
                                            iQuadCompare | iQuadIterator | callable $filter = null): array {
        $values = [];
        foreach ($dataset->getIterator($filter) as $quad) {
            $object = $quad->getObject();
            if ($object instanceof iLiteral) {
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
    static public function getLiteralValuesByLang(iDatasetListQuadParts $dataset,
                                                  iQuadCompare | iQuadIterator | callable $filter = null): array {
        $values = [];
        foreach ($dataset->getIterator($filter) as $quad) {
            $object = $quad->getObject();
            if ($object instanceof iLiteral) {
                $values[$object->getLang() ?? ''] = $object->getValue();
            }
        }
        return $values;
    }

    /**
     * 
     * @return array<mixed>
     */
    static public function getObjectValues(iDatasetListQuadParts $dataset,
                                           iQuadCompare | iQuadIterator | callable $filter = null): array {
        return self::getValues($dataset, $filter, 'getObject');
    }

    static public function getGraph(iDataset $dataset,
                                    iQuadCompare | iQuadIterator | callable $filter = null): iNamedNode | iBlankNode | iDefaultGraph | null {
        return self::getSingle($dataset, $filter)?->getGraph();
    }

    static public function getGraphUri(iDataset $dataset,
                                       iQuadCompare | iQuadIterator | callable $filter = null): ?string {
        return self::getGraph($dataset, $filter)?->getValue();
    }

    /**
     * 
     * @return array<iNamedNode | iBlankNode | iDefaultGraph>
     */
    static public function getGraphs(iDatasetListQuadParts $dataset,
                                     iQuadCompare | iQuadIterator | callable $filter = null): array {
        return iterator_to_array($dataset->listGraphs($filter));
    }

    /**
     * 
     * @return array<string>
     */
    static public function getGraphUris(iDatasetListQuadParts $dataset,
                                        iQuadCompare | iQuadIterator | callable $filter = null): array {
        return self::getValues($dataset, $filter, 'getGraph');
    }
}
