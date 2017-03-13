<?php declare(strict_types=1);
/**
 * This file is part of the PIPECHAIN Project.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace PipeChain;


use PipeChain\Collections\PipeChainCollection;
use PipeChain\InvokerProcessors\CommonInvokerProcessor;

/**
 * Class PipeChain
 * @package PipeChain
 */
class PipeChain implements PipeChainInterface
{
    /**
     * @var PipeChainCollectionInterface
     */
    protected $stack;

    /**
     * @var InvokerProcessorInterface|CommonInvokerProcessor
     */
    protected $processor;

    /**
     * @var PipeChainInterface|null
     */
    protected $next;

    /**
     * PipeChain constructor.
     * @param InvokerProcessorInterface|null $invokerProcessor
     */
    final public function __construct(InvokerProcessorInterface $invokerProcessor = null)
    {
        $this->stack = new PipeChainCollection();
        $this->processor = $this->boot($this->stack, $invokerProcessor);
    }

    /**
     * pipes a stage with an optional associated fallback.
     *
     * @param callable $stage
     * @param callable $fallback
     * @return PipeChainInterface
     */
    final public function pipe(callable $stage, callable $fallback = null): PipeChainInterface
    {
        $this->stack->attach($stage, $fallback);

        return $this;
    }

    /**
     * processes a payload.
     *
     * @param $payload
     * @return mixed the payload
     */
    final public function process($payload)
    {
        $payload = $this->processor->processStack($payload, $this->stack);

        if ( $this->next instanceof PipeChainInterface ) {
            $payload = $this->next->process($payload);
        }

        return $payload;
    }

    /**
     * appends the provided pipe to the end of the chain.
     *
     * @param PipeChainInterface $chain
     * @return PipeChainInterface
     */
    final public function chain(PipeChainInterface $chain): PipeChainInterface
    {
        if ( $this->next instanceof PipeChainInterface ) {
            $this->next->chain($chain);
        }
        else {
            $this->next = $chain;
        }

        return $this;
    }

    /**
     * factory method.
     *
     * @param InvokerProcessorInterface|null $invoker
     * @return PipeChainInterface
     */
    public static function create(InvokerProcessorInterface $invoker = null): PipeChainInterface
    {
        return new static($invoker);
    }

    /**
     * Boots the PipeChainCollection.
     *
     * This method should be overwritten for own PipeChain implementations pre-filled with pipes.
     *
     * @param PipeChainCollectionInterface $container
     * @return InvokerProcessorInterface
     */
    protected function boot(PipeChainCollectionInterface $container, InvokerProcessorInterface $processor = null): InvokerProcessorInterface
    {
        return $processor ?? new CommonInvokerProcessor();
    }
}