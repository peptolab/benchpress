# Benchpress

A config-driven [PHPBench](https://phpbench.readthedocs.io/) harness for comparing multiple PHP library implementations side-by-side.

Designed for cases where you need to benchmark N versions or forks of the same library — each gets its own isolated Composer root, so even packages with identical namespaces can coexist.

## Quick Start

```bash
git clone git@github.com:peptolab/benchpress.git
cd benchpress
composer install
make setup    # generates subjects/ from config.php
make bench    # runs benchmarks, prints comparison table
```

## How It Works

1. **`config.php`** defines "subjects" — the libraries to compare, with their Composer dependencies
2. **`make setup`** auto-generates a separate `subjects/{name}/` directory with its own `composer.json` and `vendor/` for each subject
3. **You write benchmark classes** in `benchmarks/Bench/`, extending `AbstractBench`. Each class specifies which subject it benchmarks via `getSubjectKey()`
4. **PHPBench runs with process isolation** — each benchmark iteration is a separate PHP process, loading only its subject's autoloader. No namespace collisions.

## Adding a Library

1. Add an entry to `config.php`:

```php
'my-fork' => [
    'name'         => 'My Fork',
    'require'      => [
        'myvendor/my-package' => 'dev-main',
    ],
    'repositories' => [
        [
            'type' => 'vcs',
            'url'  => 'https://github.com/myvendor/my-package.git',
        ],
    ],
],
```

2. Create a benchmark class in `benchmarks/Bench/`:

```php
<?php

namespace Benchpress\Bench;

use Benchpress\AbstractBench;
use PhpBench\Attributes as Bench;

class MyForkSelectBench extends AbstractBench
{
    protected function getSubjectKey(): string
    {
        return 'my-fork';
    }

    #[Bench\Revs(1000)]
    #[Bench\Iterations(10)]
    public function benchSomething(): void
    {
        // Your benchmark using the subject's library
    }
}
```

3. Run `make setup && make bench`.

See `examples/ExampleSelectBench.php` for a fuller example.

## Available Commands

| Command | Description |
|---------|-------------|
| `make setup` | Generate subjects from config + install deps |
| `make bench` | Run all benchmarks with comparison report |
| `make bench-store` | Run + store results as baseline |
| `make compare` | Run + compare against stored baseline |
| `make report-md` | Generate Markdown report |
| `make report-csv` | Export as CSV |
| `make clean` | Remove subjects, stored results, reports |
| `make help` | Show available targets |

## Project Structure

```
benchpress/
├── config.php          # Subject definitions (THE config)
├── benchmarks/
│   ├── AbstractBench.php         # Thin base class (autoloader + init hook)
│   ├── Bench/                    # Your benchmark classes go here
│   └── bootstrap.php             # PHPBench bootstrap
├── subjects/                     # Auto-generated (gitignored)
│   ├── stable/vendor/
│   └── beta/vendor/
├── examples/                     # Reference examples
├── reports/                      # Generated output
├── bin/setup                     # Subject generator script
├── composer.json                 # Harness deps (phpbench only)
├── phpbench.json                 # PHPBench config
└── Makefile
```

## Requirements

- PHP 8.2+
- Composer
