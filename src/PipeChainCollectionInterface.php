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


interface PipeChainCollectionInterface extends \IteratorAggregate, \Countable
{
    /**
     * Attaches a stage and assigns a fallback to the attached stage.
     *
     * @param callable $stage
     * @param callable|null $fallback
     * @return void
     */
    public function attach(callable $stage, callable $fallback = null);
}