<?php

declare(strict_types=1);

namespace BenchpressTest\Unit\Setup;

use Benchpress\Setup\ComposerJsonBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ComposerJsonBuilderTest extends TestCase
{
    #[Test]
    public function nameIsGeneratedFromKey(): void
    {
        $result = ComposerJsonBuilder::build('stable', [], []);

        self::assertSame('benchmark-subject/stable', $result['name']);
    }

    #[Test]
    public function descriptionUsesKeyWhenNoNameProvided(): void
    {
        $result = ComposerJsonBuilder::build('stable', [], []);

        self::assertSame('Benchmark subject: stable', $result['description']);
    }

    #[Test]
    public function descriptionUsesExplicitName(): void
    {
        $result = ComposerJsonBuilder::build('stable', ['name' => 'Stable Release'], []);

        self::assertSame('Benchmark subject: Stable Release', $result['description']);
    }

    #[Test]
    public function sharedRequireMergedWithSubjectRequire(): void
    {
        $shared  = ['require' => ['php' => '^8.2']];
        $subject = ['require' => ['vendor/package' => '^1.0']];

        $result = ComposerJsonBuilder::build('test', $subject, $shared);

        self::assertIsArray($result['require']);
        self::assertSame('^8.2', $result['require']['php']);
        self::assertSame('^1.0', $result['require']['vendor/package']);
    }

    #[Test]
    public function subjectRequireOverridesSharedRequire(): void
    {
        $shared  = ['require' => ['php' => '^8.1']];
        $subject = ['require' => ['php' => '^8.3']];

        $result = ComposerJsonBuilder::build('test', $subject, $shared);

        self::assertIsArray($result['require']);
        self::assertSame('^8.3', $result['require']['php']);
    }

    #[Test]
    public function minimumStabilityIncludedFromShared(): void
    {
        $shared = ['minimum-stability' => 'dev'];

        $result = ComposerJsonBuilder::build('test', [], $shared);

        self::assertSame('dev', $result['minimum-stability']);
    }

    #[Test]
    public function minimumStabilityOmittedWhenNotInShared(): void
    {
        $result = ComposerJsonBuilder::build('test', [], []);

        self::assertArrayNotHasKey('minimum-stability', $result);
    }

    #[Test]
    public function preferStableIncludedFromShared(): void
    {
        $shared = ['prefer-stable' => true];

        $result = ComposerJsonBuilder::build('test', [], $shared);

        self::assertTrue($result['prefer-stable']);
    }

    #[Test]
    public function repositoriesIncludedFromSubject(): void
    {
        $subject = [
            'repositories' => [
                ['type' => 'vcs', 'url' => 'https://github.com/user/repo.git'],
            ],
        ];

        $result = ComposerJsonBuilder::build('test', $subject, []);

        self::assertIsArray($result['repositories']);
        self::assertCount(1, $result['repositories']);
        self::assertIsArray($result['repositories'][0]);
        self::assertSame('vcs', $result['repositories'][0]['type']);
    }

    #[Test]
    public function repositoriesOmittedWhenNotInSubject(): void
    {
        $result = ComposerJsonBuilder::build('test', [], []);

        self::assertArrayNotHasKey('repositories', $result);
    }

    #[Test]
    public function autoloadIncludedFromSubject(): void
    {
        $subject = [
            'autoload' => [
                'psr-4' => ['App\\' => 'src/'],
            ],
        ];

        $result = ComposerJsonBuilder::build('test', $subject, []);

        self::assertSame(['psr-4' => ['App\\' => 'src/']], $result['autoload']);
    }

    #[Test]
    public function sortPackagesConfigAlwaysPresent(): void
    {
        $result = ComposerJsonBuilder::build('test', [], []);

        self::assertIsArray($result['config']);
        self::assertTrue($result['config']['sort-packages']);
    }

    #[Test]
    public function fullIntegrationBuild(): void
    {
        $shared = [
            'minimum-stability' => 'dev',
            'prefer-stable'     => true,
            'require'           => ['php' => '^8.2'],
        ];

        $subject = [
            'name'         => 'Full Test',
            'require'      => ['vendor/package' => '^2.0'],
            'repositories' => [
                ['type' => 'path', 'url' => '/local/package'],
            ],
            'autoload'     => [
                'psr-4' => ['Test\\' => 'src/'],
            ],
        ];

        $result = ComposerJsonBuilder::build('full', $subject, $shared);

        self::assertSame('benchmark-subject/full', $result['name']);
        self::assertSame('Benchmark subject: Full Test', $result['description']);
        self::assertIsArray($result['require']);
        self::assertSame('^8.2', $result['require']['php']);
        self::assertSame('^2.0', $result['require']['vendor/package']);
        self::assertSame('dev', $result['minimum-stability']);
        self::assertTrue($result['prefer-stable']);
        self::assertIsArray($result['repositories']);
        self::assertCount(1, $result['repositories']);
        self::assertSame(['psr-4' => ['Test\\' => 'src/']], $result['autoload']);
        self::assertIsArray($result['config']);
        self::assertTrue($result['config']['sort-packages']);
    }
}
