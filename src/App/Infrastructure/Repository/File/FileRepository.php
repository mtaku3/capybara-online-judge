<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\File;

use App\Domain\File\IFileRepository;
use App\Domain\Submission\Entity\Submission;
use App\Domain\Problem\Entity\TestCase;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Submission\ValueObject\SubmissionType;
use App\Infrastructure\Repository\File\Exception\FileMustBeArchivedAsTarballException;
use PharData;
use RecursiveIteratorIterator;

class FileRepository implements IFileRepository
{
    /**
     * @param string $src
     * @param Submission $submission
     * @return void
     */
    public function moveSourceCode(string $src, Submission $submission): void
    {
        $dest = $submission->getSourceFile()->getPath();

        if (!file_exists(dirname($dest))) {
            mkdir($dest, recursive: true);
        }

        if ($submission->getSubmissionType() === SubmissionType::SourceCode) {
            $tar = new PharData($dest);

            $tar->addFile($src, self::RetrievePreferedFileNameFromLanguage($submission->getLanguage()));
        } else {
            self::ValidateTarball($src);

            move_uploaded_file($src, $dest);
        }
    }

    public function moveInputFile(string $src, TestCase $testCase): void
    {
        $dest = $testCase->getInputFile()->getPath();

        if (!file_exists(dirname($dest))) {
            mkdir($dest, recursive: true);
        }

        self::ValidateTarball($src);

        move_uploaded_file($src, $dest);
    }

    public function moveOutputFile(string $src, TestCase $testCase): void
    {
        $dest = $testCase->getOutputFile()->getPath();

        if (!file_exists(dirname($dest))) {
            mkdir($dest, recursive: true);
        }

        self::ValidateTarball($src);

        move_uploaded_file($src, $dest);
    }

    /**
     * @param Language $language
     * @return string
     */
    private static function RetrievePreferedFileNameFromLanguage(Language $language): string
    {
        switch ($language) {
            case Language::C:
                return "main.c";
                break;
            case Language::CPP:
                return "main.cpp";
                break;
        }
    }

    /**
     * @param string $src
     * @return void
     */
    public static function ValidateTarball(string $src): void
    {
        $tar = new PharData($src);

        if ($tar->getExtension() !== "tar") {
            throw new FileMustBeArchivedAsTarballException();
        }
    }

    /**
     * @param string $src
     * @return int
     */
    public static function SumContentLengthsUp(string $src): int
    {
        $tar = new PharData($src);

        $length = 0;
        foreach (new RecursiveIteratorIterator($tar) as $file) {
            $length += $file->getSize();
        }

        return $length;
    }
}
