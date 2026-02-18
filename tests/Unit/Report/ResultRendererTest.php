<?php

declare(strict_types=1);

namespace BenchpressTest\Unit\Report;

use Benchpress\Report\ResultRenderer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function json_decode;
use function str_contains;

/**
 * @phpstan-import-type ResultSet from ResultRenderer
 * @phpstan-import-type MemorySet from ResultRenderer
 */
class ResultRendererTest extends TestCase
{
    private ResultRenderer $renderer;

    /** @var ResultSet */
    private array $results;

    /** @var list<string> */
    private array $subjects;

    /** @var MemorySet */
    private array $memoryResults;

    protected function setUp(): void
    {
        $this->renderer      = new ResultRenderer();
        $this->subjects      = ['Alpha', 'Beta'];
        $this->results       = [
            'benchSelect' => [
                'Alpha' => [
                    'mean'   => 1_000.0,
                    'mode'   => 1_000.0,
                    'min'    => 900.0,
                    'max'    => 1_100.0,
                    'stdev'  => 50.0,
                    'rstdev' => 5.0,
                ],
                'Beta'  => [
                    'mean'   => 2_000.0,
                    'mode'   => 2_000.0,
                    'min'    => 1_800.0,
                    'max'    => 2_200.0,
                    'stdev'  => 100.0,
                    'rstdev' => 5.0,
                ],
            ],
        ];
        $this->memoryResults = [
            'benchSelect' => [
                'Alpha' => 1_048_576.0,
                'Beta'  => 2_097_152.0,
            ],
        ];
    }

    #[Test]
    public function renderCliContainsHeaderAndData(): void
    {
        $output = $this->renderer->renderCli($this->results, $this->subjects);

        self::assertStringContainsString('Test', $output);
        self::assertStringContainsString('Alpha', $output);
        self::assertStringContainsString('Beta', $output);
        self::assertStringContainsString('benchSelect', $output);
    }

    #[Test]
    public function renderCliShowsRatioForSlowerSubject(): void
    {
        $output = $this->renderer->renderCli($this->results, $this->subjects);

        self::assertStringContainsString('2.0x', $output);
    }

    #[Test]
    public function renderMarkdownProducesValidTable(): void
    {
        $output = $this->renderer->renderMarkdown($this->results, $this->subjects);

        self::assertStringContainsString('| Test |', $output);
        self::assertStringContainsString('|------|', $output);
        self::assertStringContainsString('| benchSelect |', $output);
    }

    #[Test]
    public function renderMarkdownShowsRatioForSlowerSubject(): void
    {
        $output = $this->renderer->renderMarkdown($this->results, $this->subjects);

        self::assertStringContainsString('2.0x', $output);
    }

    #[Test]
    public function renderCsvProducesValidOutput(): void
    {
        $output = $this->renderer->renderCsv($this->results, $this->subjects);

        self::assertStringContainsString('test,Alpha,Beta', $output);
        self::assertStringContainsString('benchSelect', $output);
    }

    #[Test]
    public function renderJsonProducesValidStructure(): void
    {
        $output = $this->renderer->renderJson($this->results, $this->subjects);

        /** @var array{subjects: list<string>, results: list<array<string, mixed>>} $data */
        $data = json_decode($output, true);

        self::assertArrayHasKey('subjects', $data);
        self::assertArrayHasKey('results', $data);
        self::assertSame(['Alpha', 'Beta'], $data['subjects']);
        self::assertCount(1, $data['results']);
        self::assertSame('benchSelect', $data['results'][0]['test']);
    }

    #[Test]
    public function renderJsonIncludesFullStatistics(): void
    {
        $output = $this->renderer->renderJson($this->results, $this->subjects);

        /** @var array{subjects: list<string>, results: list<array<string, mixed>>} $data */
        $data = json_decode($output, true);

        /** @var array<string, float> $alphaStats */
        $alphaStats = $data['results'][0]['Alpha'];
        self::assertArrayHasKey('mean', $alphaStats);
        self::assertArrayHasKey('mode', $alphaStats);
        self::assertArrayHasKey('min', $alphaStats);
        self::assertArrayHasKey('max', $alphaStats);
        self::assertArrayHasKey('stdev', $alphaStats);
        self::assertArrayHasKey('rstdev', $alphaStats);
    }

    #[Test]
    public function missingSubjectDataRendersDash(): void
    {
        /** @var ResultSet $results */
        $results = [
            'benchInsert' => [
                'Alpha' => [
                    'mean'   => 500.0,
                    'mode'   => 500.0,
                    'min'    => 450.0,
                    'max'    => 550.0,
                    'stdev'  => 25.0,
                    'rstdev' => 5.0,
                ],
            ],
        ];

        $cliOutput = $this->renderer->renderCli($results, $this->subjects);
        self::assertTrue(str_contains($cliOutput, '-'));

        $mdOutput = $this->renderer->renderMarkdown($results, $this->subjects);
        self::assertStringContainsString(' - |', $mdOutput);
    }

    #[Test]
    public function ratioOmittedForFastestSubject(): void
    {
        /** @var ResultSet $results */
        $results = [
            'benchEqual' => [
                'Alpha' => [
                    'mean'   => 1_000.0,
                    'mode'   => 1_000.0,
                    'min'    => 900.0,
                    'max'    => 1_100.0,
                    'stdev'  => 50.0,
                    'rstdev' => 5.0,
                ],
                'Beta'  => [
                    'mean'   => 1_000.0,
                    'mode'   => 1_000.0,
                    'min'    => 900.0,
                    'max'    => 1_100.0,
                    'stdev'  => 50.0,
                    'rstdev' => 5.0,
                ],
            ],
        ];

        $output = $this->renderer->renderCli($results, $this->subjects);

        self::assertStringNotContainsString('x)', $output);
    }

    #[Test]
    public function renderCliIncludesMemorySection(): void
    {
        $output = $this->renderer->renderCli($this->results, $this->subjects, $this->memoryResults);

        self::assertStringContainsString('Peak Memory', $output);
        self::assertStringContainsString('1.00MB', $output);
        self::assertStringContainsString('2.00MB', $output);
    }

    #[Test]
    public function renderCliMemoryShowsRatioForHigherUsage(): void
    {
        $output = $this->renderer->renderCli($this->results, $this->subjects, $this->memoryResults);

        self::assertStringContainsString('2.0x', $output);
    }

    #[Test]
    public function renderCliOmitsMemorySectionWhenEmpty(): void
    {
        $output = $this->renderer->renderCli($this->results, $this->subjects, []);

        self::assertStringNotContainsString('Peak Memory', $output);
    }

    #[Test]
    public function renderMarkdownIncludesMemorySection(): void
    {
        $output = $this->renderer->renderMarkdown($this->results, $this->subjects, $this->memoryResults);

        self::assertStringContainsString('### Peak Memory', $output);
        self::assertStringContainsString('1.00MB', $output);
        self::assertStringContainsString('2.00MB', $output);
    }

    #[Test]
    public function renderCsvIncludesMemorySection(): void
    {
        $output = $this->renderer->renderCsv($this->results, $this->subjects, $this->memoryResults);

        self::assertStringContainsString('mem_peak', $output);
        self::assertStringContainsString('1048576', $output);
        self::assertStringContainsString('2097152', $output);
    }

    #[Test]
    public function renderJsonIncludesMemoryData(): void
    {
        $output = $this->renderer->renderJson($this->results, $this->subjects, $this->memoryResults);

        /** @var array{subjects: list<string>, results: list<array<string, mixed>>} $data */
        $data = json_decode($output, true);

        /** @var array<string, mixed> $alphaStats */
        $alphaStats = $data['results'][0]['Alpha'];
        self::assertArrayHasKey('mem_peak', $alphaStats);
        self::assertEquals(1_048_576, $alphaStats['mem_peak']);
    }
}
