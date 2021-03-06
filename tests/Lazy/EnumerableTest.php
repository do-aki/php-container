<?php
namespace dooaki\Test\Container\Lazy;

use dooaki\Container\Lazy\Enumerator;

class EnumerableTest extends \PHPUnit_Framework_TestCase
{
    public function getTestData() {
        return [
            'a' => 1,
            'b' => 2,
            'c' => 3
        ];
    }

    public function test_toArray()
    {
        $a = (new Enumerator([$this, 'getTestData']))->toArray();
        $this->assertSame($this->getTestData(), $a);
    }

    public function test_select_findAll()
    {
        $a = Enumerator::from(range(1, 100))
            ->findAll(function ($v) { return $v <= 10; })
            ->toArray();

        $this->assertSame(range(1, 10), $a);
    }

    public function test_map()
    {
        $a = (new Enumerator([$this, 'getTestData']))
            ->map(function ($v) { return $v * 2; })
            ->toArray();

        $this->assertSame([
            'a' => 2,
            'b' => 4,
            'c' => 6
        ], $a);
    }

    public function test_mapKey()
    {
        $a = (new Enumerator([$this, 'getTestData']))
            ->mapKey(function ($k, $v) { return str_repeat($k, 2); })
            ->toArray();

        $this->assertSame(
            [
                'aa' => 1,
                'bb' => 2,
                'cc' => 3
            ],
            $a
        );
    }

    public function test_mapKeyValue()
    {
        $a = (new Enumerator([$this, 'getTestData']))
            ->mapKeyValue(
                function ($k, $v) {
                    return [str_repeat($k, 2) => $v * 2];
                }
            )->toArray();

        $this->assertSame(
            [
                'aa' => 2,
                'bb' => 4,
                'cc' => 6
            ],
            $a
        );
    }

    public function test_skip_offset()
    {
        $a = Enumerator::from(range(1, 100))->offset(90)->toArray();

        $this->assertSame(range(91, 100), $a);
    }

    public function test_take_limit()
    {
        $a = Enumerator::from(range(1, 100))->limit(10)->toArray();

        $this->assertSame(range(1, 10), $a);
    }

    public function test_take_limit_orver_range()
    {
        $a = Enumerator::from(range(1, 5))->limit(10)->toArray();

        $this->assertSame(range(1, 5), $a);
    }

    public function test_tap()
    {
        $e = new Enumerator([$this, 'getTestData']);

        $a = [];
        $e = $e->tap(function ($v, $k) use(&$a) { $a[$k] = $v; });

        $this->assertSame([], $a);

        $e->toArray();
        $this->assertSame($this->getTestData(), $a);
    }

    public function test_transpose()
    {
        $e = Enumerator::from(
            [
                [1,2],
                [2,4],
                [5,6]
            ]
        );
        $a = $e->transpose()->toArray();
        $this->assertSame(
            [
                [1, 2, 5],
                [2, 4, 6]
            ],
            $a
        );
    }

    public function test_transpose_with_key()
    {
        $e = Enumerator::from(
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['a' => 4, 'c' => 5, 'b' => 6],
                ['c' => 7, 'b' => 8, 'a' => 9],
            ]
        );
        $a = $e->transpose()->toArray();
        $this->assertSame(
            [
                'a' => [1, 4, 9],
                'b' => [2, 6, 8],
                'c' => [3, 5, 7],
            ],
            $a
        );
    }

    public function test_flatten()
    {
        $e = Enumerator::from(
            [
                1,
                [2,3],
                [[4], 5]
            ]
        );
        $a = $e->flatten()->toArrayValues();
        $this->assertSame([1,2,3,4,5], $a);
    }

    public function test_flatten_with_key()
    {
        $e = Enumerator::from(
            [
                'a' => 1,
                'b' => ['c' => 2, 'd' => 3],
                'd' => ['e' => ['f' => 4], 'g' => 5]
            ]
        );
        $a = $e->flatten()->toArray();
        $this->assertSame(
            [
                'a' => 1,
                'c' => 2, 'd' => 3,
                'f' => 4, 'g' => 5,
            ], $a);
    }

    public function test_unique()
    {
        $e = Enumerator::from([1,2,3,3,3,4,5,4,3,2,1]);
        $a = $e->unique()->toArray();
        $this->assertSame([0=>1,1=>2,2=>3,5=>4,6=>5], $a);
    }

    public function test_unique_with_func()
    {
        $e = Enumerator::from([1,2,3,4,5,6,7,8,9]);
        $a = $e->unique(
            function ($v) {
                return $v % 3;
            }
        )->toArray();
        $this->assertSame([0=>1,1=>2,2=>3], $a);
    }

    public function test_values()
    {
        $e = Enumerator::from([1,2,3,3,3,4,5,4,3,2,1]);
        $a = $e->unique()->values()->toArray();
        $this->assertSame([1,2,3,4,5], $a);
    }

    public function test_first()
    {
        $this->assertSame(Enumerator::from($this->getTestData())->first(), 1);
    }

    public function test_first_empty()
    {
        $this->assertNull(Enumerator::from([])->first());
    }

    public function test_last()
    {
        $this->assertSame(Enumerator::from($this->getTestData())->last(), 3);
    }

    public function test_last_empty()
    {
        $this->assertNull(Enumerator::from([])->last());
    }

    public function test_any_true()
    {
        $this->assertTrue(Enumerator::from($this->getTestData())->any(function ($v) { return $v > 2; }));
    }

    public function test_any_false()
    {
        $this->assertFalse(Enumerator::from($this->getTestData())->any(function ($v) { return $v > 3; }));
    }

    public function test_all_true()
    {
        $this->assertTrue(Enumerator::from($this->getTestData())->all(function ($v, $k) { return is_string($k); }));
    }

    public function test_all_false()
    {
        $this->assertFalse(Enumerator::from($this->getTestData())->all(function ($v) { return $v < 2; }));
    }

    public function test_groupBy()
    {
        $a = Enumerator::from([1,2,3,4,5,6,7,8,9])
            ->groupBy(function ($v) { return $v % 2 === 0 ? 'even' : 'odd'; });

        $this->assertArrayHasKey('odd', $a);
        $this->assertSame([1,3,5,7,9], $a['odd']);
        $this->assertArrayHasKey('even', $a);
        $this->assertSame([2,4,6,8], $a['even']);
    }

    public function test_groupBy_with_key()
    {
        $a = Enumerator::from(['a' => 1, 'B'=> 2, 'c' => 3])
        ->groupBy(function ($v, $k) { return ctype_upper($k) ? 'U' : 'L'; });

        $this->assertArrayHasKey('U', $a);
        $this->assertSame([2], $a['U']);
        $this->assertArrayHasKey('L', $a);
        $this->assertSame([1,3], $a['L']);
    }

    public function test_groupBy_include_enumerator()
    {
        $a = Enumerator::from([[1,4,8], [2,4,6], [3,2,1]])->map(
            function ($v) {
                return Enumerator::from($v)->take(2);
            }
        )->groupBy(function ($v) { return 0; });

        $this->assertSame([[1,4,2,4,3,2]], $a);
    }

    public function test_apply()
    {
        $e = new Enumerator([$this, 'getTestData']);
        $a = [];
        $e->apply(function ($v, $k) use(&$a) { $a[$k] = $v; });

        $this->assertSame($this->getTestData(), $a);
    }
}
