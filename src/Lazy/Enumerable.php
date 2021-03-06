<?php
namespace dooaki\Container\Lazy;

/**
 *  lazy evaluation using each method
 *
 * @method \Generator each()
 */
trait Enumerable
{
    /**
     * Return Enumerator which $predicate returns a true value
     *
     * @param callable $predicate
     * @return \dooaki\Container\Lazy\Enumerator
     */
    public function select(callable $predicate)
    {
        return new Enumerator(
            function () use($predicate) {
                foreach ($this->each() as $k => $v) {
                    if (call_user_func($predicate, $v, $k)) {
                        yield $k => $v;
                    }
                }
            }
        );
    }

    /**
     * Same as select method
     *
     * @param callable $predicate
     * @return \dooaki\Container\Lazy\Enumerator
     */
    public function findAll(callable $predicate)
    {
        return $this->select($predicate);
    }

    /**
     * Return Enumerator which convert values
     *
     * @param callable $converter
     * @return \dooaki\Container\Lazy\Enumerator
     */
    public function map(callable $converter)
    {
        return new Enumerator(
            function () use($converter) {
                foreach ($this->each() as $k => $v) {
                    $v = call_user_func($converter, $v, $k);
                    yield $k => $v;
                }
            }
        );
    }

    /**
     * Return Enumerator which convert keys
     *
     * @param callable $converter
     * @return \dooaki\Container\Lazy\Enumerator
     */
    public function mapKey(callable $converter)
    {
        return new Enumerator(
           function () use($converter) {
                foreach ($this->each() as $k => $v) {
                    $k = call_user_func($converter, $k, $v);
                    yield $k => $v;
                }
            }
        );
    }

    /**
     * Return Enumerator which convert keys and values
     *
     * @param callable $converter
     * @return \dooaki\Container\Lazy\Enumerator
     */
    public function mapKeyValue(callable $converter)
    {
        return new Enumerator(
            function () use($converter) {
                foreach ($this->each() as $k => $v) {
                    $r = call_user_func($converter, $k, $v);
                    yield key($r) => current($r);
                }
            }
        );
    }

    /**
     * Return Enumerator which first $n elements
     *
     * @param int $n
     * @return \dooaki\Container\Lazy\Enumerator
     */
    public function skip($n)
    {
        return new Enumerator(
            function () use($n) {
                foreach ($this->each() as $v) {
                    if (0 < $n) {
                        -- $n;
                    } else {
                        yield $v;
                    }
                }
            }
        );
    }

    /**
     * Same as skip method
     *
     * @param integer $n
     * @return \dooaki\Container\Lazy\Enumerator
     */
    public function offset($n)
    {
        return $this->skip($n);
    }

    /**
     * Return Enumerator which first $n elements
     *
     * @param integer $n
     * @return \dooaki\Container\Lazy\Enumerator
     */
    public function take($n)
    {
        return new Enumerator(
            function () use($n) {
                foreach ($this->each() as $k => $v) {
                    yield $k => $v;
                    if (-- $n < 1)
                        return;
                }
            }
        );
    }

    /**
     * Same as take method
     *
     * @param integer $n
     * @return \dooaki\Container\Lazy\Enumerator
     */
    public function limit($n)
    {
        return $this->take($n);
    }

    /**
     * Return Enumerator which calls $action
     *
     * @param callable $action
     * @return \dooaki\Container\Lazy\Enumerator
     */
    public function tap(callable $action)
    {
        return new Enumerator(
            function () use($action) {
                foreach ($this->each() as $k => $v) {
                    call_user_func($action, $v, $k);
                    yield $k => $v;
                }
            }
        );
    }

    /**
     * Return Enumerator which returns transposed Array
     *
     * @return \dooaki\Container\Lazy\Enumerator|\Generator
     */
    public function transpose()
    {
        return new Enumerator(
            function () {
                $ret = [];
                foreach ($this->each() as $row) {
                    foreach ($row as $k => $col) {
                        $ret[$k][] = $col;
                    }
                }

                foreach ($ret as $k => $r) {
                    yield $k => $r;
                }
            }
        );
    }

    /**
     * Return Enumerator which flatten values
     *
     * @return \dooaki\Container\Lazy\Enumerator|\Generator
     */
    public function flatten()
    {
        return new Enumerator(
            function () {
                foreach ($this->each() as $k => $v) {
                    if (($v instanceof \Traversable) || is_array($v)) {
                        yield $k => Enumerator::from($v)->flatten();
                    } else {
                        yield $k => $v;
                    }
                }
            }
        );
    }

    /**
     * Return Enumerator which returns unique values
     *
     * @param callable $func
     * @return Enumerator
     */
    public function unique(callable $func = null)
    {
        return new Enumerator(
            function () use ($func) {
                $exists = [];
                foreach ($this->each() as $k => $v) {
                    $comp = $func ? $func($v) : $v;
                    if (!in_array($comp, $exists, true)) {
                        $exists[] = $comp;
                        yield $k => $v;
                    }
                }
            }
        );
    }

    /**
     * Return Enumerator which drop keys
     *
     * @return \dooaki\Container\Lazy\Enumerator
     */
    public function values()
    {
        return new Enumerator(
            function () {
                foreach ($this->each() as $v) {
                    yield $v;
                }
            }
        );
    }

    /**
     * Return first element of each()
     *
     * @return mixed first element of each()
     */
    public function first()
    {
        foreach ($this->each() as $v) {
            return $v;
        }
        return null;
    }

    /**
     * Return last element of each()
     *
     * @return mixed last element of each()
     */
    public function last()
    {
        $last = null;
        foreach ($this->each() as $v) {
            $last = $v;
        }
        return $last;
    }

    /**
     * Return true if any $predicate returns value is true
     *
     * @param callable $predicate
     * @return boolean
     */
    public function any(callable $predicate)
    {
        foreach ($this->each() as $k => $v) {
            if ($predicate($v, $k)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return true if all $predicate returns value is true
     *
     * @param callable $predicate
     * @return boolean
     */
    public function all(callable $predicate)
    {
        foreach ($this->each() as $k => $v) {
            if (! $predicate($v, $k)) {
                return false;
            }
        }
        return true;
    }

    /**
     * apply $action to all elements
     *
     * @param callable $action
     * @return void
     */
    public function apply(callable $action)
    {
        foreach ($this->each() as $k => $v) {
            call_user_func($action, $v, $k);
        }
    }

    /**
     * Return an array which aggregated by func
     *
     * @param callable $func
     * @return array
     */
    public function groupBy(callable $func)
    {
        $result = [];
        foreach ($this->each() as $k => $v) {
            $this->_groupBy($result, $k, $v, $func);
        }
        return $result;
    }

    /**
     * call by groupBy method
     *
     * @see groupBy
     * @param array    &$result
     * @param mixed    $k
     * @param mixed    $v
     * @param callable $func
     * @return void
     */
    private function _groupBy(&$result, $k, $v, $func)
    {
        if ($v instanceof self) {
            foreach ($v->each() as $kk => $vv) {
                $this->_groupBy($result, $kk, $vv, $func);
            }
        } else {
            $aggregate_key = call_user_func($func, $v, $k);
            $result[$aggregate_key][] = $v;
        }
    }

    /**
     * Return an array overwrite if found same key containing the elements in each()
     *
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach ($this->each() as $k => $v) {
            $this->_toArray($result, $k, $v);
        }
        return $result;
    }

    /**
     * call by toArray method
     *
     * @see toArray
     * @param array &$result
     * @param mixed $k
     * @param mixed $v
     * @return void
     */
    private function _toArray(&$result, $k, $v)
    {
        if ($v instanceof self) {
            foreach ($v->each() as $kk => $vv) {
                $this->_toArray($result, $kk, $vv);
            }
        } else {
            $result[$k] = $v;
        }
    }

    /**
     * Return an array containing the elements in each()
     *
     * @return array
     */
    public function toArrayValues()
    {
        $result = [];
        foreach ($this->each() as $v) {
            $this->_toArrayValues($result, $v);
        }
        return $result;
    }

    /**
     * call by toArrayValues method
     *
     * @see toArray
     * @param array  &$result
     * @param mixed  $v
     * @return void
     */
    private function _toArrayValues(&$result, $v)
    {
        if ($v instanceof self) {
            foreach ($v->each() as $vv) {
                $this->_toArrayValues($result, $vv);
            }
        } else {
            $result[] = $v;
        }
    }

}
