# Benchpress

A config-driven [PHPBench](https://phpbench.readthedocs.io/) harness for comparing multiple PHP library implementations side-by-side.

Designed for cases where you need to benchmark N versions or forks of the same library — each gets its own isolated Composer root, so even packages with identical namespaces can coexist.

## Quick Start

```bash
git clone git@github.com:peptolab/benchpress.git
cd benchpress
composer install
composer setup    # generates subjects/ from config.php
composer bench    # runs benchmarks, prints comparison table
```

## How It Works

1. **`config.php`** defines "subjects" — the libraries to compare, with their Composer dependencies
2. **`composer setup`** auto-generates a separate `subjects/{name}/` directory with its own `composer.json` and `vendor/` for each subject
3. **You write benchmark classes** in `benchmarks/Bench/`, extending `AbstractBench`. Each class specifies which subject it benchmarks via `getSubjectKey()`
4. **PHPBench runs with process isolation** — each benchmark iteration is a separate PHP process, loading only its subject's autoloader. No namespace collisions.

## Usage

### 1. Define subjects in `config.php`

Each subject is a library to benchmark, with its Composer dependencies:

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

### 2. Define a benchmark interface

Create an interface in `benchmarks/Bench/` that defines the contract all subjects must honour. This guarantees every subject runs the same set of tests:

```php
namespace Benchpress\Bench;

interface SelectBenchInterface
{
    public function benchSimpleSelect(): void;
    public function benchMediumSelect(): void;
    public function benchComplexSelect(): void;
}
```

### 3. Write a concrete class per subject

Each class extends `AbstractBench` (handles autoloader isolation) and implements your interface (enforces the contract):

```php
namespace Benchpress\Bench;

use Benchpress\AbstractBench;
use PhpBench\Attributes as Bench;

class MyForkSelectBench extends AbstractBench implements SelectBenchInterface
{
    protected function getSubjectKey(): string
    {
        return 'my-fork'; // matches key in config.php
    }

    #[Bench\Revs(1000)]
    #[Bench\Iterations(10)]
    public function benchSimpleSelect(): void
    {
        // Your benchmark using the subject's library
    }

    // ... benchMediumSelect(), benchComplexSelect()
}
```

### 4. Run

```bash
composer setup && composer bench
```

See `examples/` for a complete working example.

## Available Commands

| Command | Description |
|---------|-------------|
| `composer setup` | Generate subjects from config + install deps |
| `composer bench` | Run all benchmarks (PHPBench default output) |
| `composer bench:store` | Run + store results as baseline |
| `composer bench:compare` | Run + compare against stored baseline |
| `composer report` | Run + display side-by-side comparison table |
| `composer report:md` | Run + output Markdown comparison table |
| `composer report:csv` | Run + output CSV |
| `composer report:json` | Run + output JSON |
| `composer clean` | Remove subjects, stored results, reports |

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
├── bin/report                    # Comparison report generator
├── composer.json                 # Harness deps (phpbench only)
└── phpbench.json                 # PHPBench config
```

## Requirements

- PHP 8.2+
- Composer
