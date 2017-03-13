<?php
/**
 * This file is part of the PIPECHAIN Project.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace PipeChain\InvokerProcessors;


use PipeChain\InvokerProcessors\Exceptions\InvokerProcessorException;

class LoyalTypeInvokerProcessor extends LoyalInterfaceInvokerProcessor
{
    public function __construct(string $type, $mode = 1)
    {
        parent::__construct($type, $mode);
    }

    /**
     * validates the loyalty of the payload.
     *
     * @param $payload
     * @throws InvokerProcessorException when the payload is not loyal
     */
    protected function validateLoyalty($payload)
    {
        if ( gettype($payload) !== $this->interface ) {
            throw new InvokerProcessorException(
                'processed payload has incompatible type, got '
                .gettype($payload).', expecting '.$this->interface
            );
        }
    }

}