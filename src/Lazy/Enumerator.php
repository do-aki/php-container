<?php
namespace dooaki\Container\Lazy;

/**
 *  make Enumerable object from callable returns array or Traversable
 *
 */
class Enumerator
{
    use Enumerable;

    private $each;

    public function __construct(callable $callable)
    {
        $this->each = $callable;
    }

    public function each(callable $action = null)
    {
        if ($action !== null) {
            $this->apply($action);
        } else {
            $each = call_user_func($this->each);
            if (!self::isIterable($each)) {
                throw new \RuntimeException("return value is not iterable object");
            }
            return $each;
        }
    }

    private static function isIterable($target) {
        return ($target instanceof \Traversable) || is_array($target);
    }

    /**
     * make Enumerable object from array or Traversable
     *
     *  @param mixed $array_or_traversable array or Traversable
     *  @return dooaki\Container\Lazy\Enumerator
     */
    public static function from($array_or_traversable) {
        if (!self::isIterable($array_or_traversable)) {
            throw new \InvalidArgumentException("argument is not iterable object");
        }

        return new self(function () use ($array_or_traversable) {
            return $array_or_traversable;
        });
    }

}