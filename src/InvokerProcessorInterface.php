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


interface InvokerProcessorInterface
{
    /**
     * processes a PipeChainCollection with the provided payload.
     *
     * @param mixed $payload
     * @param PipeChainCollectionInterface $pipeChainCollection
     * @return mixed payload
     */
    public function processStack($payload, PipeChainCollectionInterface $pipeChainCollection);

    /**
     * processes a single stage and its optionally associated fallback with the provided payload.
     *
     * @param mixed $payload
     * @param callable $stage
     * @param callable|null $fallback
     * @return mixed payload
     */
    public function process($payload, callable $stage, callable $fallback = null);
}