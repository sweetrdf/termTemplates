# termTemplates

[![Latest Stable Version](https://poser.pugx.org/sweetrdf/term-templates/v/stable)](https://packagist.org/packages/sweetrdf/term-templates)
![Build status](https://github.com/sweetrdf/termTemplates/workflows/phpunit/badge.svg?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/sweetrdf/termTemplates/badge.svg?branch=master)](https://coveralls.io/github/sweetrdf/termTemplates?branch=master)
[![License](https://poser.pugx.org/sweetrdf/term-templates/license)](https://packagist.org/packages/sweetrdf/term-templates)

Provides:

* A set of term templates allowing to match desired RDF named nodes, literals and quads.
  To be used mainly as `rdfInterface\Dataset` methods `$filter` parameter.
* A convenient methods for extracting single terms and/or their values from `rdfInterface\Dataset`.

## Quad-matching classes

* `termTemplates\QuadTemplate`
* `termTemplates\PredicateTemplate` - a `termTemplates\QuadTemplate` variant skipping the subject
  (convenient for filtering the `rdfInterface\DatasetNode`)

## Term-matching classes

(all classes in the `termTemplate` namespace)

| Match by                     | Supported matchModes            | Match both named nodes and literals | Match only named nodes   | Match only literals    | Remarks |
|------------------------------|---------------------------------|-------------------------------------|--------------------------|------------------------|---------|
| term's string value          | ==, !=, <, >, <=, >=, starts, ends, contains, regex, any | `ValueTemplate` | `NamedNodeTemplate` | `LiteralTemplate` [1]  |         |
| term's numeric value         | ==, !=, <, >, <=, >=, any       | NA                                  | NA                       | `NumericTemplate`      | [2]     |

[1] Supports filtering also by literal's lang (using *==* and *any* operators) and datatype (only using *==* operator).

[2] Supports both strict and non-strict mode. In the strict mode comparison with literals with non-numeric datatype returns `false` no matter their value.

## Other classes

* `termTemplates\NotTemplate` - negates the result of the `equals()` operation on a given `rdfInterface\TermCompare` object.
* `termTemplates\AnyOfTemplate` - matches terms being equal to any of given list of `rdfInterface\TermCompare` objects.
  as single `rdfInterface\Term`, single value, array of `rdfInterface\Term` or array of values.

## Installation

* Obtain the [Composer](https://getcomposer.org)
* Run `composer require sweetrdf/term-templates`

## Automatically generated documentation

https://sweetrdf.github.io/termTemplates/namespaces/termtemplates.html

It's very incomplete but better than nothing.\
[RdfInterface](https://github.com/sweetrdf/rdfInterface/) documentation is included which explains the most important design decisions.

## Usage examples

Remarks:

* **The comparison with term templates isn't symmetric!**
  You should always call `$termTemplate->equals($termToBeCompared)` and never `$termToBeCompared->equals($termTemplate)`.
  The latter one will always return false.
* See also [here](https://github.com/sweetrdf/rdfInterface/blob/master/EasyRdfReadme.md) for examples of using term templates in conjuction with an RDF datasets.
* To run this examples an RDF terms factory is needed. Here we will use the one from the [quickRdf](https://github.com/sweetrdf/quickRdf) library.
  You can install it with `composer require sweetrdf/quick-rdf`.
* In the examples below the comparison operators are specified by stating corresponding `termTemplates\ValueTemplate` class constants
  but you can also use corresponding constants values as listed in the table above (e.g. `>=` or `regex`).
* If you are tired with the long code, define class aliases, e.g. `use termTemplates\QuadTemplate as QT;`, `use termTemplates\ValueTemplate as VT;`, etc.

### String values comparison

```php
$df = new quickRdf\DataFactory();

$literals = [
    $df::literal('Lorem ipsum', 'lat'),
    $df::namedNode('http://ipsum.dolor/sit#amet'),
    $df::literal('foo bar'),
];

// find all terms containing 'ipsum'
// true, true, false
$tmplt = new termTemplates\ValueTemplate('ipsum', termTemplates\ValueTemplate::CONTAINS);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

// find all literals containing 'ipsum'
// true, false, false
$tmplt = new termTemplates\LiteralTemplate('ipsum', termTemplates\ValueTemplate::CONTAINS);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

// find all named nodes containing 'ipsum'
// false, true, false
$tmplt = new termTemplates\NamedNodeTemplate('ipsum', termTemplates\ValueTemplate::CONTAINS);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

// find all literals in latin
// true, false, false
$tmplt = new termTemplates\LiteralTemplate(lang: 'lat');
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

// find all terms with string value lower than 'http'
// true, false, true
$tmplt = new termTemplates\LiteralTemplate('http', termTemplates\ValueTemplate::LOWER);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

// find all terms matching a 'Lorem|foo' regular expression
// true, false, true
$tmplt = new termTemplates\ValueTemplate('/Lorem|foo/', termTemplates\ValueTemplate::REGEX);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

// ValueTemplate, NamedNodeTemplate and LiteralTemplate can be passed multiple values
// In such a case condition needs to be fulfilled on any value
// true, true, false
$tmplt = new termTemplates\ValueTemplate(['Lorem ipsum', 'http://ipsum.dolor/sit#amet']);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));
```

### Numeric values comparison

```php
$df = new quickRdf\DataFactory();

$literals = [
    $df->literal('2'),
    $df->literal(3, null, 'http://www.w3.org/2001/XMLSchema#int'),
    $df->literal('1foo'),
    $df->literal('2', null, 'http://www.w3.org/2001/XMLSchema#int'),
    $df->namedNode('http://ipsum.dolor/sit#amet'),
];

// find terms with a value of 2
// true, false, false, true, false
$tmplt = new termTemplates\NumericTemplate(2);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

// find terms with a value of 2 and enforce an RDF numeric type
// false, false, false, true, false
$tmplt = new termTemplates\NumericTemplate(2, strict: true);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

// find terms with a value greate or equal than 3
// false, true, false, false, false
$tmplt = new termTemplates\NumericTemplate(3, termTemplates\ValueTemplate::GREATER_EQUAL);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

// find all terms with numeric values
// true, true, false, true, false
$tmplt = new termTemplates\NumericTemplate(matchMode: termTemplates\ValueTemplate::ANY);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

// find all numeric terms with values not equal 2
// false, true, false, false, false
$tmplt  = new termTemplates\NumericTemplate(2, termTemplates\ValueTemplate::NOT_EQUALS);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));
```

### Negation and aggregation

```php
$literals = [
    $df->literal('2'),
    $df->literal(3, null, 'http://www.w3.org/2001/XMLSchema#int'),
    $df->literal('1foo'),
    $df->literal('2', null, 'http://www.w3.org/2001/XMLSchema#int'),
    $df->namedNode('http://ipsum.dolor/sit#amet'),
];

// find all terms which value is not equal 2 (also non-numeric and non-literal ones)
// false, true, true, false, true
$tmplt = new termTemplates\NumericTemplate(2);
$tmplt = new termTemplates\NotTemplate($tmplt);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

// find all terms with numeric value of 2 and all named nodes
// true, false, false, true, true
$tmplt = new termTemplates\AnyOfTemplate([
    new termTemplates\NumericTemplate(2),
    new termTemplates\NamedNodeTemplate()
]);
print_r(array_map(fn($x) => $tmplt->equals($x), $literals));

```

### Quads comparison

```php
$df = new quickRdf\DataFactory();

// find all quads with a literal value containing 'foo'
$tmplt = new termTemplates\QuadTemplate(object: new termTemplates\LiteralTemplate('foo', termTemplates\ValueTemplate::CONTAINS));

// find all quads with a given subject within a given graph
$tmplt = new termTemplates\QuadTemplate(
    subject: 'http://desired/subject',
    graph: 'http://desired/graph'
);

// find all quads with a given predicate
$tmplt = new termTemplates\QuadTemplate(predicate: 'http://desired/predicate');
//or
$tmplt = new termTemplates\PredicateTemplate('http://desired/predicate');

// both QuadTemplate and PredicateTemplate support negation, e.g.
// find all quads with subject different than 'http://unwanted/subject'
$tmplt = new termTemplates\QuadTemplate('http://desired/predicate', negate: true);
```
