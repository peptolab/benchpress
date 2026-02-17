<?php

/**
 * Benchmark subject configuration.
 *
 * Each entry in 'subjects' defines a library to benchmark.
 * The key (e.g., 'larry', 'moe') is used as:
 *   - The directory name under subjects/
 *   - The value returned by getSubjectKey() in benchmark classes
 *
 * 'shared' config is merged into every subject's composer.json.
 *
 * Example subjects (uncomment and customise):
 *
 * 'larry' => [
 *     'name'    => 'Larry (Stable)',
 *     'require' => [
 *         'vendor/package' => '^1.0',
 *     ],
 * ],
 *
 * 'moe' => [
 *     'name'         => 'Moe (Beta)',
 *     'require'      => [
 *         'vendor/package' => 'dev-feature-branch',
 *     ],
 *     'repositories' => [
 *         [
 *             'type' => 'vcs',
 *             'url'  => 'https://github.com/youruser/package.git',
 *         ],
 *     ],
 * ],
 *
 * 'curly' => [
 *     'name'         => 'Curly (Local)',
 *     'require'      => [
 *         'vendor/package' => '@dev',
 *     ],
 *     'repositories' => [
 *         [
 *             'type' => 'path',
 *             'url'  => '/absolute/path/to/local/package',
 *         ],
 *     ],
 * ],
 */

return [
    'subjects' => [
    ],

    /**
     * Shared Composer config applied to all subjects.
     */
    'shared' => [
        'minimum-stability' => 'dev',
        'prefer-stable'     => true,
        'require'           => [
            'php' => '^8.2',
        ],
    ],
];
