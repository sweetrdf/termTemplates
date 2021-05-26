# termTemplates

[![Latest Stable Version](https://poser.pugx.org/sweetrdf/term-templates/v/stable)](https://packagist.org/packages/sweetrdf/term-templates)
![Build status](https://github.com/sweetrdf/termTemplates/workflows/phpunit/badge.svg?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/sweetrdf/termTemplates/badge.svg?branch=master)](https://coveralls.io/github/sweetrdf/termTemplates?branch=master)
[![License](https://poser.pugx.org/sweetrdf/term-templates/license)](https://packagist.org/packages/sweetrdf/term-templates)

Provides:

* A set of term templates allowing to match desired RDF named nodes, literals and quads.
  To be used mainly as `rdfInterface\Dataset` methods `$filter` parameter.
* A convenient methods for extracting single terms and/or their values from `rdfInterface\Dataset`.

Usage examples can be found [here](https://github.com/sweetrdf/rdfInterface/blob/master/EasyRdfReadme.md) (search for rdfInterface usage examples).

## Term-matching classes

(all classes in the `termTemplate` namespace)

| Match by                     | Supported operators         | Match both named nodes and literals | Match only named nodes   | Match only literals    | Remarks |
|------------------------------|-----------------------------|-------------------------------------|--------------------------|------------------------|---------|
| term's string value          | ==, <, >, <=, >=, starts with, ends with, contains, any value | `ValueTemplate` | `NamedNodeTemplate` | `LiteralTemplate` [1] | |
| term's numeric value         | ==, <, >, <=, >=, isnumeric | NA                                  | NA                       | `NumericTemplate`      | [2]     |
| regex on term's string value | regex match                 | `RegexTemplate`                     | `NamedNodeRegexTemplate` | `LiteralRegexTemplate` |         |

[1] Supports filtering also by literal's lang (using *==* and *any* operators) and datatype (only using *==* operator).

[2] Supports both strict and non-strict mode. In the strict mode comparison with literals with non-numeric datatype returns `false` no matter their value.

## Other classes

* `termTemplate\AnyOfTemplate` - matches terms being equal to any of given array of `rdfInterface\TermCompare` objects.
* `termTemplate\DatasetExtractors` - provides a set of static methods for extracting `rdfInterface\Dataset` quad elements
  as single `rdfInterface\Term`, single value, array of `rdfInterface\Term` or array of values.  

## Installation

* Obtain the [Composer](https://getcomposer.org)
* Run `composer require sweetrdf/term-templates`

## Automatically generated documentation

https://sweetrdf.github.io/termTemplates/namespaces/termtemplates.html

It's very incomplete but better than nothing.\
[RdfInterface](https://github.com/sweetrdf/rdfInterface/) documentation is included which explains the most important design decisions.
