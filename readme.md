# Dewey Decimal Classification tools #

[![Build Status](https://travis-ci.org/malantonio/dewey.svg?branch=travis-support)](https://travis-ci.org/malantonio/dewey)

a WIP collection of tools to work w/ Dewey Decimal call numbers

## some terminology explanation ##

To the best of my (admittedly lacking) knowledge, there is no set terminology to differentiate the whole numbers from the decimals in a Dewey Decimal System call number. Internally, the following terms are used to refer to the different parts:

For the call number `741.4372 A123b c.2 v.3 2004`:

    term     | value
-------------|------
`major`      | `741`
`minor`      | `4372`
`cutter`     | `A123b`
`additional` | `c.2 v.3 2004`
`call number`| `741.4372`

What's also a bit confusing is the differentiation between a call number and a CallNumber object. Unless specifically referencing the CallNumber object's $callNumber field, the camel-cased CallNumber refers to the object and lower-cased `call number` refers to the major/minor combination.

Please add an issue if you've got some suggestions re: clearing the code up!

## usage ##

The `Dewey` class has a few static methods for general use:

### `Dewey::calculateRange($rangeString)` ###

Calculate the range of DDS call numbers using an `*` to specify where the range takes place.
Returns a tuple array of `[$min, $max]`

#### example ####

```php
$drawing = Dewey::calculateRange("74*");
// returns `array("740", "750")`
```

range string | returned array
-------------|---------------
`7**`        | `["700", "800"]`
`74*`        | `["740", "750"]`
`741.*`      | `["741", "742"]`
`741.4*`     | `["741.4", "741.5"]`

### `Dewey::compare($input, $comparison, $operator)` ###

Compare `$input` to `$comparison` using `$operator`. Accepts: `<`, `>`, `<=`, `>=`, `==`, `===`. Returns a boolean.

#### example ####

```php
var_dump(Dewey::compare("741.4372 A123x", "750", "<"));
// bool(true)
```

### `Dewey::parseCallNumber($callNumberString)` ###

Builds a `Dewey\CallNumber` object from the provided string.

#### example ####

```php
var_dump(Dewey::parseCallNumber("741.4372 A123x"));
// object(Dewey\CallNumber)#2 (3) {
//  ["callNumber":protected]=>
//  string(7) "741.4372"
//  ["cutter":protected]=>
//  string(5) "A123x"
//  ["additional":protected]=>
//  string(0) ""
// }
```

### `Dewey\CallNumber` ###

A `CallNumber` object is a representation of a DDS call number.

An object can be constructed by using `Dewey::parseCallNumber` or by instantiating a `new Dewey\CallNumber` with a DDS call number string as a parameter (which uses the former under the hood anyway).

The following methods are available on an instantiated object:

#### `Dewey\CallNumber::compare($comparisonAgainst, $operator)` ####

Compare the instantiated object against another DDS call number (or `Dewey\CallNumber` object). Returns a bool. The following wrappers are provided too:

* `Dewey\CallNumber::equalTo($comp[, $deepEqual = false])`
* `Dewey\CallNumber::greaterThan($comp)`
* `Dewey\CallNumber::greaterThanEqualTo($comp)`
* `Dewey\CallNumber::lessThan($comp)`
* `Dewey\CallNumber::lessThanEqualTo($comp)`

#### `Dewey\CallNumber::inRange($range[, $lessThanEqualTo = true])` ####

Whether the instantiated object falls within the range provided as a range string (see `Dewey::calculateRange` for examples) or a min/max tuple. If `$lessThanEqualTo` is `false`, does not include the `$max` value as `true`.

## license ##

MIT
