<?php

declare(strict_types=1);

namespace BenchpressTest\Unit\Report;

use Benchpress\Report\MemoryFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MemoryFormatterTest extends TestCase
{
    /**
     * @return array<string, array{float, string}>
     */
    public static function memoryProvider(): array
    {
        return [
            'zero bytes'    => [0.0, '0B'],
            '1 byte'        => [1.0, '1B'],
            '512 bytes'     => [512.0, '512B'],
            '1023 bytes'    => [1_023.0, '1023B'],
            'boundary: 1KB' => [1_024.0, '1.00KB'],
            '1.5 kilobytes' => [1_536.0, '1.50KB'],
            '100 kilobytes' => [102_400.0, '100.00KB'],
            'boundary: 1MB' => [1_048_576.0, '1.00MB'],
            '1.5 megabytes' => [1_572_864.0, '1.50MB'],
            '10 megabytes'  => [10_485_760.0, '10.00MB'],
        ];
    }

    #[Test]
    #[DataProvider('memoryProvider')]
    public function formatReturnsExpectedString(float $bytes, string $expected): void
    {
        self::assertSame($expected, MemoryFormatter::format($bytes));
    }
}
