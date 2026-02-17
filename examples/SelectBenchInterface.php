<?php

/**
 * Example: Define a benchmark interface to enforce a contract.
 *
 * Every subject's benchmark class must implement this interface,
 * guaranteeing the same set of tests runs against each library.
 *
 * Copy this into benchmarks/Bench/ alongside your concrete classes.
 */

declare(strict_types=1);

namespace Benchpress\Bench;

interface SelectBenchInterface
{
    public function benchSimpleSelect(): void;

    public function benchMediumSelect(): void;

    public function benchComplexSelect(): void;
}
