<?php

declare(strict_types=1);

namespace App\Domain\Problem\Entity;

use App\Domain\Problem\ValueObject\OutputFileId;
use App\Domain\Problem\ValueObject\TestCaseId;

class OutputFile
{
    private OutputFileId $Id;
    private string $Path;

    /**
     * @param OutputFileId $id
     * @param string $path
     * @return void
     */
    public function __construct(OutputFileId $id, string $path)
    {
        $this->Id = $id;
        $this->Path = $path;
    }

    /**
     * @param TestCaseId $testCaseId
     * @return OutputFile
     */
    public static function _create(TestCaseId $testCaseId): OutputFile
    {
        return new OutputFile(OutputFileId::nextIdentity(), __DIR__ . "/data/outputs/" . (string)$testCaseId);
    }

    /** @return OutputFileId  */
    public function getId(): OutputFileId
    {
        return $this->Id;
    }

    /** @return string  */
    public function getPath(): string
    {
        return $this->Path;
    }
}