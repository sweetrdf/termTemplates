# simpleRdf

[![Latest Stable Version](https://poser.pugx.org/sweetrdf/term-templates/v/stable)](https://packagist.org/packages/sweetrdf/term-templates)
![Build status](https://github.com/sweetrdf/termTemplates/workflows/phpunit/badge.svg?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/sweetrdf/termTemplates/badge.svg?branch=master)](https://coveralls.io/github/sweetrdf/termTemplates?branch=master)
[![License](https://poser.pugx.org/sweetrdf/term-templates/license)](https://packagist.org/packages/sweetrdf/term-templates)

Provides a set of term templates allowing match desired RDF named nodes, literals and quads.

Summary of when to use which class is provided below (`termTemplates` namespace skipped for clarity):

| Match by                     | Supported operators         | Match both named nodes and literals | Match only named nodes | Match only literals    | Remarks |
|------------------------------|-----------------------------|-------------------------------------|------------------------|------------------------|---------|
| term's string value          | ==, <, >, <=, >=, starts with, ends with, contains, any value | ValueTemplate | NamedNodeTemplate | LiteralTemplate [1] |     |
| term's numeric value         | ==, <, >, <=, >=, isnumeric | NA                                  | NA                     | NumericTemplate        | [2]     |
| regex on term's string value | regex match                 | RegexTemplate                       | NamedNodeRegexTemplate | LiteralRegexTemplate   |         |

[1] Supports filtering also by literal's lang (using *==* and *any* operators) and datatype (only using *==* operator).

[2] Supports both strict and non-strict mode. In the strict mode comparison with literals with non-numeric datatype returns `false` no matter their value.

## Installation

* Obtain the [Composer](https://getcomposer.org)
* Run `composer require sweetrdf/term-templates`

## Automatically generated documentation

https://sweetrdf.github.io/termTemplates/namespaces/termtemplates.html

It's very incomplite but better than nothing.\
[RdfInterface](https://github.com/sweetrdf/rdfInterface/) documentation is included which explains the most important design decisions.
