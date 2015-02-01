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

    /**
     * apply $action or return iterable object
     *
     * @param callable $action
     * @return null|\Traversable|array
     */
    public function each(callable $action = null)
    {
        if ($action === null) {
            $each = call_user_func($this->each);
            if (!self::isIterable($each)) {
                throw new \RuntimeException("return value is not iterable object");
            }
            return $each;
        } else {
            $this->apply($action);
        }

        return null;
    }

    /**
     * Finds whether a variable is an array or a traversable object
     *
     * @param mixed $target The variable being evaluated
     * @return bool Return true if $target is an array or a traversable object
     */
    private static function isIterable($target) {
        return ($target instanceof \Traversable) || is_array($target);
    }

    /**
     * make Enumerable object from array or Traversable
     *
     *  @param mixed $array_or_traversable array or Traversable
     *  @return \dooaki\Container\Lazy\Enumerator
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
