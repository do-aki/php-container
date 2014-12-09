<?php
namespace dooaki\Container\Lazy;

/**
 *  lazy evaluation using each method
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
     * @param unknown $n
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
     * @return \dooaki\Container\Lazy\Enumerator|Generator
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
     * @return \dooaki\Container\Lazy\Enumerator|Generator
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
     * @return mixd last element of each()
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

    private function _toArray(&$result, $k, $v) {
        if ($v instanceof self) {
            foreach ($v->each() as $kk => $vv) {
                $this->_toArray($result, $kk, $vv);
            }
        } else {
            $result[$k] = $v;
        }
    }


    private function _toArrayValues(&$result, $v) {
        if ($v instanceof self) {
            foreach ($v->each() as $vv) {
                $this->_toArrayValues($result, $vv);
            }
        } else {
            $result[] = $v;
        }
    }

}
