<?php

declare(strict_types=1);

namespace App\Domain\File;

use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\Entity\TestCase;
use App\Domain\Submission\Entity\Submission;

interface IFileRepository
{
    /**
     * @param string $src
     * @param Submission $submission
     * @return void
     */
    public function moveSourceCode(string $src, Submission $submission): void;

    /**
     * @param string $src
     * @param TestCase $testCase
     * @return void
     */
    public function moveInputFile(string $src, TestCase $testCase): void;

    /**
     * @param string $src
     * @param TestCase $testCase
     * @return void
     */
    public function moveOutputFile(string $src, TestCase $testCase): void;

    /**
     * @param string $src
     * @return void
     */
    public static function ValidateTarball(string $src): void;

    /**
     * @param string $src
     * @return int
     */
    public static function SumContentLengthsUp(string $src): int;
}
