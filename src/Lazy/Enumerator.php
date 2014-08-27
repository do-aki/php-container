<?php
namespace dooaki\Container\Lazy;

class Enumerator
{
    use Enumerable;

    private $each;

    public function __construct($array_or_callable)
    {
        if (!is_callable($array_or_callable) && !($array_or_callable instanceof \Traversable) && !is_array($array_or_callable)) {
            throw new \InvalidArgumentException("cannot each object");
        }

        $this->each = $array_or_callable;
    }

    public function each(callable $action = null)
    {
        if (is_callable($this->each)) {
            $each = call_user_func($this->each);
            if (!($each instanceof \Traversable) && !is_array($each)) {
                throw new \RuntimeException("cannot each object");
            }
        } else {
            $each = $this->each;
        }

        if ($action === null) {
            return $each;
        }

        foreach ($each as $k => $v) {
            call_user_func($action, $v, $k);
        }
    }
}
