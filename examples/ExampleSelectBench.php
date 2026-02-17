<?php

/**
 * Example: How to write a benchmark class for a subject.
 *
 * Copy this into benchmarks/Bench/ and customize for your library.
 *
 * Key points:
 *   - Extend AbstractBench (handles autoloader loading)
 *   - Implement getSubjectKey() returning a key from config.php
 *   - Optionally override init() to set up library-specific state
 *   - Write bench* methods with PHPBench attributes
 */

declare(strict_types=1);

namespace Benchpress\Bench;

use Benchpress\AbstractBench;
use PhpBench\Attributes as Bench;

class ExampleStableSelectBench extends AbstractBench
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
