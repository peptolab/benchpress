<?php

declare(strict_types=1);

namespace Benchpress;

use PhpBench\Attributes as Bench;
use RuntimeException;

use function dirname;
use function file_exists;
use function sprintf;

#[Bench\BeforeMethods('setUp')]
abstract class AbstractBench
{
    /**
     * Return the subject key from config.php (e.g., 'stable', 'beta').
     */
    abstract protected function getSubjectKey(): string;

    /**
     * Hook called after the subject's autoloader is loaded.
     * Override to set up platform objects, adapters, fixtures, etc.
     */
    protected function init(): void
    {
    }

    public function setUp(): void
    {
        $autoloader = dirname(__DIR__) . '/subjects/' . $this->getSubjectKey() . '/vendor/autoload.php';

        if (! file_exists($autoloader)) {
            throw new RuntimeException(sprintf(
                'Subject "%s" not installed. Run: composer setup',
                $this->getSubjectKey(),
            ));
        }

        require_once $autoloader;
        $this->init();
    }
}
