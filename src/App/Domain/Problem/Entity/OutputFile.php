<?php

declare(strict_types=1);

namespace App\Domain\Problem\Entity;

use App\Domain\Common\Entity\IFile;
use App\Domain\Problem\ValueObject\OutputFileId;
use App\Domain\Problem\ValueObject\TestCaseId;

class OutputFile implements IFile
{
    /**
     * @var OutputFileId
     */
    private OutputFileId $Id;
    /**
     * @var string
     */
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
        return new OutputFile(OutputFileId::NextIdentity(), "/data/OutputFiles/" . (string)$testCaseId . ".tar");
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
