<?php declare(strict_types=1);
/**
 * This file is part of the PIPECHAIN Project.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace PipeChain\Collections;


use PipeChain\PipeChainCollectionInterface;
use Traversable;

/**
 * Class PipeChainCollection
 * @package PipeChain\Collections
 */
class PipeChainCollection implements PipeChainCollectionInterface
{
    /**
     * @var \SplObjectStorage
     */
    protected $storage;

    /**
     * PipeChainCollection constructor.
     */
    public function __construct()
    {
        $this->storage = new \SplObjectStorage();
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable|\Generator An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): \Generator
    {
        foreach ( $this->storage as $object ) {
            yield $object => $this->storage[$object]['fallback'];
        }
    }

    /**
     * Attaches a stage and assigns a fallback to the attached stage.
     *
     * @param callable $stage
     * @param callable|null $fallback
     * @return void
     */
    public function attach(callable $stage, callable $fallback = null)
    {
        $this->storage->attach(
            $this->marshalClosure($stage),
            [
                'fallback' => is_callable($fallback) ? $this->marshalClosure($fallback) : null
            ]
        );
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return $this->storage->count();
    }

    protected function marshalClosure(callable $callback): \Closure
    {
        if ( method_exists(\Closure::class, 'fromCallable') ) {
            return \Closure::fromCallable($callback);
        }

        is_callable($callback, true, $target);

        if ( false === strpos('::', $target) || $callback instanceof \Closure ) {
            return (new \ReflectionFunction($target))->getClosure();
        }
        else {
            list($class, $method) = explode('::', $target);
            return (new \ReflectionMethod($class, $method))
                ->getClosure(
                    is_array($callback) && is_object($callback[0])
                        ? $callback[0]
                        : null
                )
            ;
        }
    }
}