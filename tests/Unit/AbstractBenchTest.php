<?php

declare(strict_types=1);

namespace BenchpressTest\Unit;

use Benchpress\AbstractBench;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class AbstractBenchTest extends TestCase
{
    #[Test]
    public function setUpThrowsWhenAutoloaderIsMissing(): void
    {
        $bench = new class extends AbstractBench {
            protected function getSubjectKey(): string
            {
                return 'nonexistent_subject';
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('nonexistent_subject');
        $bench->setUp();
    }

    #[Test]
    public function exceptionMessageIncludesSubjectKey(): void
    {
        $key   = 'my_custom_key';
        $bench = new class ($key) extends AbstractBench {
            public function __construct(private string $key)
            {
            }

            protected function getSubjectKey(): string
            {
                return $this->key;
            }
        };

        try {
            $bench->setUp();
            self::fail('Expected RuntimeException was not thrown');
        } catch (RuntimeException $e) {
            self::assertStringContainsString($key, $e->getMessage());
            self::assertStringContainsString('composer setup', $e->getMessage());
        }
    }
}
