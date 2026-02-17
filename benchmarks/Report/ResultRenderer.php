<?php

declare(strict_types=1);

namespace Benchpress\Report;

use function array_keys;
use function array_map;
use function array_merge;
use function assert;
use function fclose;
use function fopen;
use function fputcsv;
use function is_resource;
use function json_encode;
use function max;
use function min;
use function rewind;
use function round;
use function rtrim;
use function sprintf;
use function str_pad;
use function str_repeat;
use function stream_get_contents;
use function strlen;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_UNICODE;

/**
 * @phpstan-type Stats array{
 *     mean: float,
 *     mode: float,
 *     min: float,
 *     max: float,
 *     stdev: float,
 *     rstdev: float,
 * }
 * @phpstan-type ResultSet array<string, non-empty-array<string, Stats>>
 */
class ResultRenderer
{
    /**
     * Render results as a CLI table.
     *
     * @param ResultSet    $results
     * @param list<string> $subjects
     */
    public function renderCli(array $results, array $subjects): string
    {
        $testWidth = max(4, ...array_map('strlen', array_keys($results)));
        $colWidth  = max(10, ...array_map('strlen', $subjects));

        $output = str_pad('Test', $testWidth);
        foreach ($subjects as $s) {
            $output .= '  ' . str_pad($s, $colWidth);
        }
        $output .= "\n";
        $output .= str_repeat('─', strlen(rtrim($output))) . "\n";

        foreach ($results as $method => $data) {
            /** @var non-empty-array<float> $times */
            $times   = array_map(fn($d) => $d['mean'], $data);
            $fastest = min($times);

            $row = str_pad($method, $testWidth);
            foreach ($subjects as $s) {
                if (isset($data[$s])) {
                    $time   = TimeFormatter::format($data[$s]['mean']);
                    $ratio  = $data[$s]['mean'] / $fastest;
                    $suffix = $ratio > 1.05 ? sprintf(' (%.1fx)', $ratio) : '';
                    $cell   = $time . $suffix;
                } else {
                    $cell = '-';
                }
                $row .= '  ' . str_pad($cell, $colWidth);
            }
            $output .= $row . "\n";
        }

        return $output;
    }

    /**
     * Render results as a Markdown table.
     *
     * @param ResultSet    $results
     * @param list<string> $subjects
     */
    public function renderMarkdown(array $results, array $subjects): string
    {
        $output = '| Test |';
        foreach ($subjects as $s) {
            $output .= " {$s} |";
        }
        $output .= "\n|------|";
        foreach ($subjects as $s) {
            $output .= str_repeat('-', strlen($s) + 2) . '|';
        }
        $output .= "\n";

        foreach ($results as $method => $data) {
            /** @var non-empty-array<float> $times */
            $times   = array_map(fn($d) => $d['mean'], $data);
            $fastest = min($times);

            $output .= "| {$method} |";
            foreach ($subjects as $s) {
                if (isset($data[$s])) {
                    $time    = TimeFormatter::format($data[$s]['mean']);
                    $ratio   = $data[$s]['mean'] / $fastest;
                    $suffix  = $ratio > 1.05 ? sprintf(' (%.1fx)', $ratio) : '';
                    $output .= " {$time}{$suffix} |";
                } else {
                    $output .= ' - |';
                }
            }
            $output .= "\n";
        }

        return $output;
    }

    /**
     * Render results as CSV.
     *
     * @param ResultSet    $results
     * @param list<string> $subjects
     */
    public function renderCsv(array $results, array $subjects): string
    {
        $stream = fopen('php://memory', 'r+');
        assert(is_resource($stream));
        fputcsv($stream, array_merge(['test'], $subjects));

        foreach ($results as $method => $data) {
            $row = [$method];
            foreach ($subjects as $s) {
                $row[] = isset($data[$s]) ? round($data[$s]['mean'], 2) : '';
            }
            fputcsv($stream, $row);
        }

        rewind($stream);
        $content = stream_get_contents($stream);
        fclose($stream);

        return $content !== false ? $content : '';
    }

    /**
     * Render results as JSON.
     *
     * @param ResultSet    $results
     * @param list<string> $subjects
     */
    public function renderJson(array $results, array $subjects): string
    {
        $output = [
            'subjects' => $subjects,
            'results'  => [],
        ];

        foreach ($results as $method => $data) {
            $entry = ['test' => $method];
            foreach ($subjects as $s) {
                if (isset($data[$s])) {
                    $entry[$s] = $data[$s];
                }
            }
            $output['results'][] = $entry;
        }

        return json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
}
