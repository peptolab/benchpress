<?php

declare(strict_types=1);

namespace Benchpress\Report;

use function sprintf;

class TimeFormatter
{
    /**
     * Format nanoseconds into a human-readable time string.
     */
    public static function format(float $nanoseconds): string
    {
        if ($nanoseconds >= 1_000_000) {
            return sprintf('%.2fms', $nanoseconds / 1_000_000);
        }

        if ($nanoseconds >= 1_000) {
            return sprintf('%.2fμs', $nanoseconds / 1_000);
        }

        return sprintf('%.0fns', $nanoseconds);
    }
}
