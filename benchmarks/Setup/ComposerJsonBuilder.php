<?php

declare(strict_types=1);

namespace Benchpress\Setup;

use function array_merge;

class ComposerJsonBuilder
{
    /**
     * Build a composer.json array for a benchmark subject.
     *
     * @param string               $key     Subject key (e.g. 'stable', 'beta')
     * @param array<string, mixed> $subject Subject config from config.php
     * @param array<string, mixed> $shared  Shared config from config.php
     * @return array<string, mixed>
     */
    public static function build(string $key, array $subject, array $shared): array
    {
        /** @var string $name */
        $name = $subject['name'] ?? $key;

        /** @var array<string, string> $sharedRequire */
        $sharedRequire = $shared['require'] ?? [];
        /** @var array<string, string> $subjectRequire */
        $subjectRequire = $subject['require'] ?? [];

        $composerJson = [
            'name'        => "benchmark-subject/{$key}",
            'description' => "Benchmark subject: {$name}",
            'require'     => array_merge($sharedRequire, $subjectRequire),
        ];

        if (isset($shared['minimum-stability'])) {
            $composerJson['minimum-stability'] = $shared['minimum-stability'];
        }

        if (isset($shared['prefer-stable'])) {
            $composerJson['prefer-stable'] = $shared['prefer-stable'];
        }

        if (isset($subject['repositories'])) {
            $composerJson['repositories'] = $subject['repositories'];
        }

        if (isset($subject['autoload'])) {
            $composerJson['autoload'] = $subject['autoload'];
        }

        $composerJson['config'] = [
            'sort-packages' => true,
        ];

        return $composerJson;
    }
}
