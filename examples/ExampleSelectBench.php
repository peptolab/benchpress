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
 * Copy this and SelectBenchInterface.php into benchmarks/Bench/ and customise.
 *
 * Example init() usage:
 *
 *     protected function init(): void
 *     {
 *         $this->platform = new \PhpDb\Adapter\Platform\Sql92();
 *     }
 *
 * Example benchmark method:
 *
 *     public function benchSimpleSelect(): void
 *     {
 *         $s = new \PhpDb\Sql\Select('users');
 *         $s->columns(['id', 'name']);
 *         $s->where(['active' => 1]);
 *         $s->getSqlString($this->platform);
 *     }
 */

declare(strict_types=1);

namespace Benchpress\Bench;

use Benchpress\AbstractBench;
use PhpBench\Attributes as Bench;

class ExampleStableSelectBench extends AbstractBench implements SelectBenchInterface
{
    protected function getSubjectKey(): string
    {
        return 'stable';
    }

    protected function init(): void
    {
    }

    #[Bench\Revs(1000)]
    #[Bench\Iterations(10)]
    #[Bench\Groups(['select', 'simple'])]
    public function benchSimpleSelect(): void
    {
    }

    #[Bench\Revs(1000)]
    #[Bench\Iterations(10)]
    #[Bench\Groups(['select', 'medium'])]
    public function benchMediumSelect(): void
    {
    }

    #[Bench\Revs(1000)]
    #[Bench\Iterations(10)]
    #[Bench\Groups(['select', 'complex'])]
    public function benchComplexSelect(): void
    {
    }
}
