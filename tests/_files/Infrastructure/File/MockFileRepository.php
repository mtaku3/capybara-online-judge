<?php

declare(strict_types=1);

namespace Test\Infrastructure\File;

use App\Domain\File\IFileRepository;
use App\Domain\File\Entity\File;
use App\Domain\Problem\Entity\TestCase;
use App\Domain\Submission\Entity\SourceFile;
use App\Domain\Submission\Entity\Submission;
use App\Infrastructure\Repository\file\Exception\FileMustBeArchivedAsTarballException;

class MockFileRepository implements IFileRepository
{
    /**
     * @var File[]
     */
    private array $records = [];

    /**
     * @param string $src
     * @param Submission $submission
     * @return void
     */
    public function moveSourceCode(string $src, Submission $submission): void
    {
    }

    /**
     * @param string $src
     * @param TestCase $testCase
     * @return void
     */
    public function moveInputFile(string $src, TestCase $testCase): void
    {
    }

    /**
     * @param string $src
     * @param TestCase $testCase
     * @return void
     */
    public function moveOutputFile(string $src, TestCase $testCase): void
    {
    }

    /**
     * @param string $src
     * @return void
     */
    public static function ValidateTarball(string $src): void
    {
    }

    /**
     * @param string $src
     * @return int
     */
    public static function SumContentLengthsUp(string $src): int
    {
        return 1;
    }

    /**
     * @param SourceFile $sourceFile
     * @return void
     */
    public function deleteSourceFile(SourceFile $sourceFile): void
    {
    }
}
