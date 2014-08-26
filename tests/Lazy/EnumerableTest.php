<?php

namespace dooaki\Test\Container\Lazy;

use dooaki\Container\Lazy\Enumerable;
use dooaki\Container\Lazy\Enumerator;

class EnumerableTest extends \PHPUnit_Framework_TestCase {

    private $test_data = ['a' => 1, 'b' => 2, 'c' => 3];

    public function test_toArray() {
        $a = (new Enumerator($this->test_data))->toArray();
        $this->assertSame($this->test_data, $a);
    }

    public function test_select_findAll() {
        $e = new Enumerator(range(1,100));
        $a = $e->findAll(function ($v) {
            return $v <= 10;
        })->toArray();

        $this->assertSame(range(1,10), $a);
    }

    public function test_map() {
        $e = new Enumerator($this->test_data);
        $a = $e->map(function ($v) { return $v * 2; })->toArray();

        $this->assertSame(['a' => 2, 'b' => 4, 'c' => 6], $a);
    }

    public function test_mapKey() {
        $e = new Enumerator($this->test_data);
        $a = $e->mapKey(function ($k, $v) { return str_repeat($k, 2); })->toArray();

        $this->assertSame(['aa' => 1, 'bb' => 2, 'cc' => 3], $a);
    }

    public function test_mapKeyValue() {
        $e = new Enumerator($this->test_data);
        $a = $e->mapKeyValue(function ($k, $v) { return [str_repeat($k, 2) => $v * 2]; })->toArray();

        $this->assertSame(['aa' => 2, 'bb' => 4, 'cc' => 6], $a);
    }

    public function test_skip_offset() {
        $e = new Enumerator(range(1,100));
        $a = $e->offset(90)->toArray();

        $this->assertSame(range(91,100), $a);
    }

    public function test_take_limit() {
        $e = new Enumerator(range(1,100));
        $a = $e->limit(10)->toArray();

        $this->assertSame(range(1,10), $a);
    }

    public function test_tap() {
        $e = new Enumerator($this->test_data);
        $a = [];
        $e->tap(function ($v, $k) use (&$a) {
            $a[$k] = $v;
        })->toArray();

        $this->assertSame($this->test_data, $a);
    }

    public function test_transpose() {
        $e = new Enumerator([
            [1,2],
            [2,4],
            [5,6],
        ]);
        $a = $e->transpose()->toArray();
        $this->assertSame([
            [1,2,5],
            [2,4,6],
        ], $a);
    }

    public function test_transpose_with_key() {
        $e = new Enumerator([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'c' => 5, 'b' => 6],
            ['c' => 7, 'b' => 8, 'a' => 9],
        ]);
        $a = $e->transpose()->toArray();
        $this->assertSame([
            'a' => [1, 4, 9],
            'b' => [2, 6, 8],
            'c' => [3, 5, 7],
        ], $a);
    }

    public function test_first() {
        $this->assertSame(
            (new Enumerator($this->test_data))->first(),
            1
        );
    }

    public function test_first_empty() {
        $this->assertNull((new Enumerator([]))->first());
    }

    public function test_last() {
        $this->assertSame(
            (new Enumerator($this->test_data))->last(),
            3
        );
    }

    public function test_last_empty() {
        $this->assertNull((new Enumerator([]))->last());
    }

    public function test_any_true() {
        $this->assertTrue(
            (new Enumerator($this->test_data))->any(
                function ($v) {
                    return $v > 2;
                }
            )
        );
    }

    public function test_any_false() {
        $this->assertFalse(
            (new Enumerator($this->test_data))->any(
                function ($v) {
                    return $v > 3;
                }
            )
        );
    }

    public function test_all_true() {
        $this->assertTrue(
            (new Enumerator($this->test_data))->all(
                function ($v, $k) {
                    return is_string($k);
                }
            )
        );
    }

    public function test_all_false() {
        $this->assertFalse(
            (new Enumerator($this->test_data))->all(
                function ($v) {
                    return $v < 2;
                }
            )
        );
    }

}