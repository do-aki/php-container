<?php
namespace dooaki\Test\Container\Lazy;

use dooaki\Container\Lazy\Enumerator;

class EnumeratorTest extends \PHPUnit_Framework_TestCase
{

    private $test_data = [1, 2, 3, 5];

    public function toArtray($each)
    {
        $ret = [];
        foreach ($each as $v) {
            $ret[] = $v;
        }

        return $ret;
    }

    public function test_each_with_action()
    {
        $e = new Enumerator($this->test_data);
        $a = [];
        $e->each(function ($v, $k) use(&$a) {
            $a[$k] = $v;
        });

        $this->assertSame($this->test_data, $a);
    }

    public function test_array()
    {
        $e = new Enumerator($this->test_data);
        $this->assertSame($this->test_data, $this->toArtray($e->each()));
    }

    public function test_traversable()
    {
        $e = new Enumerator(new \ArrayIterator($this->test_data));
        $this->assertSame($this->test_data, $this->toArtray($e->each()));
    }

    public function generator()
    {
        foreach ($this->test_data as $v) {
            yield $v;
        }
    }

    public function test_generator()
    {
        $e = new Enumerator($this->generator());
        $this->assertSame($this->test_data, $this->toArtray($e->each()));
    }

    public function test_callable_array()
    {
        $e = new Enumerator(function () {
            return $this->test_data;
        });
        $this->assertSame($this->test_data, $this->toArtray($e->each()));
    }

    public function test_callable_traversable()
    {
        $e = new Enumerator(function () {
            return new \ArrayIterator($this->test_data);
        });
        $this->assertSame($this->test_data, $this->toArtray($e->each()));
    }

    public function test_callable_generator()
    {
        $e = new Enumerator(array($this,'generator'));
        $this->assertSame($this->test_data, $this->toArtray($e->each()));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_invalid_constructor_param()
    {
        new Enumerator(null);
    }

    /**
     * @expectedException RuntimeException
     */
    public function test_invalid_callable_return()
    {
        $e = new Enumerator(function () {});
        $e->each();
    }
}
