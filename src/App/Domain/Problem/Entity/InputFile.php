<?php

declare(strict_types=1);

namespace App\Domain\Problem\Entity;

use App\Domain\Problem\ValueObject\InputFileId;
use App\Domain\Problem\ValueObject\TestCaseId;

class InputFile
{
    /**
     * @var InputFileId
     */
    private InputFileId $Id;
    /**
     * @var string
     */
    private string $Path;

    /**
     * @param InputFileId $id
     * @param string $path
     * @return void
     */
    public function __construct(InputFileId $id, string $path)
    {
        $this->Id = $id;
        $this->Path = $path;
    }

    /**
     * @param TestCaseId $testCaseId
     * @return InputFile
     */
    public static function _create(TestCaseId $testCaseId): InputFile
    {
        return new InputFile(InputFileId::NextIdentity(), "/data/InputFiles" . (string)$testCaseId . ".tar");
    }

    /** @return InputFileId  */
    public function getId(): InputFileId
    {
        return $this->Id;
    }

    /** @return string  */
    public function getPath(): string
    {
        return $this->Path;
    }
}
