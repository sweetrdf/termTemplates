# simpleRdf

[![Latest Stable Version](https://poser.pugx.org/sweetrdf/simple-rdf/v/stable)](https://packagist.org/packages/sweetrdf/simple-rdf)
![Build status](https://github.com/sweetrdf/simpleRdf/workflows/phpunit/badge.svg?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/sweetrdf/simpleRdf/badge.svg?branch=master)](https://coveralls.io/github/sweetrdf/simpleRdf?branch=master)
[![License](https://poser.pugx.org/sweetrdf/simple-rdf/license)](https://packagist.org/packages/sweetrdf/simple-rdf)

An RDF library for PHP implementing the https://github.com/sweetrdf/rdfInterface interface.

The aim was to provide as simple, short and clear implementation as possible. Performance wasn't really important.

It can be used as a baseline for testing performance of other libraries as well for testing interoperability of the rdfInterface implementations (e.g. making sure they work correctly with `rdfInterface\Term` objects created by other library).

## Installation

* Obtain the [Composer](https://getcomposer.org)
* Run `composer require sweetrdf/simple-rdf`
* Run `composer require sweetrdf/quick-rdf-io` to install parsers and serializers.

## Automatically generated documentation

https://sweetrdf.github.io/simpleRdf/

It's very incomplite but better than nothing.\
[RdfInterface](https://github.com/sweetrdf/rdfInterface/) documentation is included which explains the most important design decisions.

## Usage

```php
include 'vendor/autoload.php';

use simpleRdf\DataFactory as DF;

$graph = new simpleRdf\Dataset();
$parser = new quickRdfIo\TriGParser(new simpleRdf\DataFactory());
$stream = fopen('pathToTurtleFile', 'r');
$graph->add($parser->parseStream($stream));
fclose($stream);

// count edges in the graph
echo count($graph);

// go trough all edges in the graph
foreach ($graph as $i) {
  echo "$i\n";
}

// find all graph edges with a given subject
echo $graph->copy(DF::quadTemplate(DF::namedNode('http://mySubject')));

// find all graph edges with a given predicate
echo $graph->copy(DF::quadTemplate(null, DF::namedNode('http://myPredicate')));

// find all graph edges with a given object
echo $graph->copy(DF::quadTemplate(null, null, DF::literal('value', 'en')));

// replace an edge in the graph
$edge = DF::quad(DF::namedNode('http://edgeSubject'), DF::namedNode('http://edgePredicate'), DF::namedNode('http://edgeObject'));
$graph[$edge] = $edge->withObject(DF::namedNode('http://anotherObject'));

// find intersection with other graph
$graph->copy($otherGraph); // immutable
$graph->delete($otherGraph); // in-place

// compute union with other graph
$graph->union($otherGraph); // immutable
$graph->add($otherGraph); // in-place

// compute set difference with other graph
$graph->copyExcept($otherGraph); // immutable
$graph->delete($otherGraph); // in-place

$serializer = new quickRdfIo\TurtleSerializer();
$stream = fopen('pathToOutputTurtleFile', 'w');
$serializer->serializeStream($stream, $graph);
fclose($stream);
```
