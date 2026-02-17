<?php

declare(strict_types=1);

namespace BenchpressTest\Unit\Report;

use Benchpress\Report\TimeFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TimeFormatterTest extends TestCase
{
    /**
     * @return array<string, array{float, string}>
     */
    public static function timeProvider(): array
    {
        return [
            'zero nanoseconds'       => [0.0, '0ns'],
            '1 nanosecond'           => [1.0, '1ns'],
            '500 nanoseconds'        => [500.0, '500ns'],
            '999 nanoseconds'        => [999.0, '999ns'],
            'boundary: 1000ns = 1μs' => [1_000.0, '1.00μs'],
            '1500 nanoseconds'       => [1_500.0, '1.50μs'],
            '999999 nanoseconds'     => [999_999.0, '1000.00μs'],
            'boundary: 1ms'          => [1_000_000.0, '1.00ms'],
            '1.5 milliseconds'       => [1_500_000.0, '1.50ms'],
            '100 milliseconds'       => [100_000_000.0, '100.00ms'],
            '1 second'               => [1_000_000_000.0, '1000.00ms'],
        ];
    }

    #[Test]
    #[DataProvider('timeProvider')]
    public function formatReturnsExpectedString(float $nanoseconds, string $expected): void
    {
        self::assertSame($expected, TimeFormatter::format($nanoseconds));
    }
}
