<?php
/**
 * This file is part of the PIPECHAIN Project.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace PipeChain;


interface PipeChainAwareInterface
{
    /**
     * appends the provided pipe to the end of the chain of the used pipeline.
     *
     * @param PipeChainInterface $pipeline
     * @return PipeChainInterface
     */
    public function chain(PipeChainInterface $pipeline): PipeChainInterface;
}