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
use PipeChain\InvokerProcessors\Exceptions\InvokerProcessorException;
use PipeChain\InvokerProcessors\Traits\StackProcessingTrait;

/**
 * Class LoyalInterfaceInvoker
 * @package PipeChain\InvokerProcessors
 */
class LoyalInterfaceInvokerProcessor extends CommonInvokerProcessor implements InvokerProcessorInterface
{
    use StackProcessingTrait;

    /**
     *
     */
    const INTERRUPT_ON_MISMATCH = 0;
    /**
     *
     */
    const RESET_ON_MISMATCH = 1;

    /**
     * @var string
     */
    protected $interface;
    /**
     * @var int
     */
    protected $mode;

    /**
     * LoyalInterfaceInvoker constructor.
     * @param string $interface
     * @param int $mode
     * @throws InvokerProcessorException
     */
    public function __construct(string $interface, int $mode = 1)
    {
        $this->interface = $interface;
        $this->mode = $mode;

        if ( ! in_array($mode, [static::INTERRUPT_ON_MISMATCH, static::RESET_ON_MISMATCH]) ) {
            throw new InvokerProcessorException(
                'Unknown mode: '.$mode
            );
        }
    }

    /**
     * processes a single stage and its optionally associated fallback with the provided payload.
     *
     * @param mixed $payload
     * @param callable $stage
     * @param callable|null $fallback
     * @throws InvokerProcessorException|\Throwable
     * @return mixed payload
     */
    public function process($payload, callable $stage, callable $fallback = null)
    {
        try {
            $newPayload = parent::process($payload, $stage, $fallback);

            return $this->sanitizeLoyalty($newPayload);
        }
        catch ( \Throwable $exception ) {
            if ( $this->mode === static::INTERRUPT_ON_MISMATCH ) {
                throw $exception;
            }

            return $payload;
        }
    }

    /**
     * validates the loyalty of the payload.
     *
     * @param $payload
     * @throws InvokerProcessorException when the payload is not loyal
     */
    protected function validateLoyalty($payload)
    {
        if ( ! is_a($payload, $this->interface) ) {
            throw new InvokerProcessorException(
                'processed payload has incompatible interface, got '
                .get_class($payload).', expecting '.$this->interface
            );
        }
    }

    /**
     * Sanitizes the loyalty of the payload.
     *
     * @param $payload
     * @return mixed
     */
    private function sanitizeLoyalty($payload)
    {
        $this->validateLoyalty($payload);

        return $payload;
    }

}