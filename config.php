<?php

/**
 * Benchmark subject configuration.
 *
 * Each entry in 'subjects' defines a library to benchmark.
 * The key (e.g., 'stable', 'beta') is used as:
 *   - The directory name under subjects/
 *   - The value returned by getSubjectKey() in benchmark classes
 *
 * 'shared' config is merged into every subject's composer.json.
 */

return [
    'subjects' => [
        'stable' => [
            'name'    => 'PhpDb Stable',
            'require' => [
                'peptolab/php-db' => '^0.6',
            ],
        ],
        'beta' => [
            'name'         => 'PhpDb Beta',
            'require'      => [
                'peptolab/php-db' => 'dev-feature-eager',
            ],
            'repositories' => [
                [
                    'type' => 'vcs',
                    'url'  => 'https://github.com/peptolab/php-db.git',
                ],
            ],
        ],
    ],

    'shared' => [
        'minimum-stability' => 'dev',
        'prefer-stable'     => true,
        'require'           => [
            'php' => '^8.2',
        ],
    ],
];
