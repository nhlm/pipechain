<?php declare(strict_types=1);
/**
 * This file is part of the PIPECHAIN Project.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace PipeChain\InvokerProcessors;


use PipeChain\InvokerProcessorInterface;
use PipeChain\InvokerProcessors\Traits\StackProcessingTrait;

class CommonInvokerProcessor implements InvokerProcessorInterface
{
    use StackProcessingTrait;

    /**
     * processes a single stage and its optionally associated fallback with the provided payload.
     *
     * @param mixed $payload
     * @param callable $stage
     * @param callable|null $fallback
     * @throws \Throwable
     * @return mixed payload
     */
    public function process($payload, callable $stage, callable $fallback = null)
    {
        try {
            return $stage($payload);
        }
        catch ( \Throwable $exception ) {
            if ( is_callable($fallback) ) {
                return $fallback($payload);
            }
            else {
                throw $exception;
            }
        }
    }

}