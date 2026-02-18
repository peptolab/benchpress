<?php

declare(strict_types=1);

namespace Benchpress\Report;

use function sprintf;

class MemoryFormatter
{
    /**
     * Format bytes into a human-readable memory string.
     */
    public static function format(float $bytes): string
    {
        if ($bytes >= 1_048_576) {
            return sprintf('%.2fMB', $bytes / 1_048_576);
        }

        if ($bytes >= 1_024) {
            return sprintf('%.2fKB', $bytes / 1_024);
        }

        return sprintf('%.0fB', $bytes);
    }
}
