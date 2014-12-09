dooaki\Container
=============

[![Build Status](https://travis-ci.org/do-aki/php-container.svg?branch=master)](https://travis-ci.org/do-aki/php-container)
[![Coverage Status](https://img.shields.io/coveralls/do-aki/php-container.svg)](https://coveralls.io/r/do-aki/php-container?branch=master)

Data containers operation utilities.

Only include utilities for lazy evaluation, Now.

Requirements
-------------
* PHP 5.5 or later

Installation
-------------

you can install the script with Composer.
in your `composer.json` file:
```
{
    "require": {
        "dooaki/container": "0.0.*"
    }
}
```
and run `composer install`.


Reference
-------------

dooaki\Container\Lazy\Enumerable
=============

###Synopsis
```php
<?php

use dooaki\Container\Lazy\Enumerable;

class CountUp
{
    use Enumerable;

    public function each()
    {
        $i=0;
        while(1) {
            yield ++$i;
        }
    }
}

print_r((new CountUp())->take(3)->toArray());
// Array
// (
//     [0] => 1
//     [1] => 2
//     [2] => 3
// )

```

dooaki\Container\Lazy\Enumerator
=============

###Synopsis
```php
<?php

use dooaki\Container\Lazy\Enumerator;

function infinity() {
    $i = 0;
    while (++$i) {
        yield $i;
    }
}

$e = new Enumerator(function () { return infinity(); });

// Enumerator use Enumerable
$e->skip(10)
    ->select(function ($i) { return $i % 2; })
    ->take(5)
    ->each(function ($i) { echo $i, ' '; }); // 11 13 15 17 19


$a = Enumerator::from([1,2,3])
    ->map(functino($v) { return $v * 2 })
    ->toArray();
print_r($a);
/*
Array
(
    [0] => 2
    [1] => 4
    [2] => 6
)
*/

```

Document
------------
see [API Documentation](http://do-aki.github.io/php-container/namespaces/dooaki.Container.Lazy.html)

License
------------
MIT License

