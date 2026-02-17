<?php

/**
 * Example: A concrete benchmark class for the "stable" subject.
 *
 * Key points:
 *   - Extends AbstractBench (handles autoloader loading via getSubjectKey)
 *   - Implements SelectBenchInterface (enforces the benchmark contract)
 *   - Override init() to set up library-specific state after autoloader loads
 *   - PHPBench attributes go on the concrete methods
 *
 * Copy this and SelectBenchInterface.php into benchmarks/Bench/ and customize.
 */

declare(strict_types=1);

namespace Benchpress\Bench;

use Benchpress\AbstractBench;
use PhpBench\Attributes as Bench;

class ExampleStableSelectBench extends AbstractBench implements SelectBenchInterface
{
    // private $platform;

    protected function getSubjectKey(): string
    {
        return 'stable'; // matches key in config.php
    }

    protected function init(): void
    {
        // Called after the subject's autoloader is loaded.
        // Set up any library-specific objects here:
        //
        // $this->platform = new \PhpDb\Adapter\Platform\Sql92();
    }

    #[Bench\Revs(1000)]
    #[Bench\Iterations(10)]
    #[Bench\Groups(['select', 'simple'])]
    public function benchSimpleSelect(): void
    {
        // Build + render a simple query using the subject's library:
        //
        // $s = new \PhpDb\Sql\Select('users');
        // $s->columns(['id', 'name']);
        // $s->where(['active' => 1]);
        // $s->getSqlString($this->platform);
    }

    #[Bench\Revs(1000)]
    #[Bench\Iterations(10)]
    #[Bench\Groups(['select', 'medium'])]
    public function benchMediumSelect(): void
    {
        // Build + render a medium-complexity query
    }

    #[Bench\Revs(1000)]
    #[Bench\Iterations(10)]
    #[Bench\Groups(['select', 'complex'])]
    public function benchComplexSelect(): void
    {
        // Build + render a complex query with joins, subqueries, etc.
    }
}
