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


interface PipeChainInterface
{
    /**
     * pipes a stage with an optional associated fallback.
     *
     * @param callable $stage
     * @param callable $fallback
     * @return PipeChainInterface
     */
    public function pipe(callable $stage, callable $fallback = null): PipeChainInterface;

    /**
     * processes a payload.
     *
     * @param $payload
     * @return mixed the payload
     */
    public function process($payload);

    /**
     * appends the provided pipe to the end of the chain.
     *
     * @param PipeChainInterface $chain
     * @return PipeChainInterface
     */
    public function chain(PipeChainInterface $chain): PipeChainInterface;

    /**
     * factory method.
     *
     * @param InvokerProcessorInterface|null $invoker
     * @return PipeChainInterface
     */
    public static function create(InvokerProcessorInterface $invoker = null): PipeChainInterface;
}