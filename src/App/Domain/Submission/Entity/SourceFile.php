<?php

declare(strict_types=1);

namespace App\Domain\Submission\Entity;

use App\Domain\Submission\ValueObject\SourceFileId;
use App\Domain\Submission\ValueObject\SubmissionId;

class SourceFile
{
    /**
     * @var SourceFileId
     */
    private SourceFileId $Id;
    /**
     * @var string
     */
    private string $Path;

    /**
     * @param SourceFileId $id
     * @param string $path
     * @return void
     */
    public function __construct(SourceFileId $id, string $path)
    {
        $this->Id = $id;
        $this->Path = $path;
    }

    /**
     * @param string $path
     * @return SourceFile
     */
    public static function _create(SubmissionId $submissionId): SourceFile
    {
        return new SourceFile(SourceFileId::NextIdentity(), "/data/SourceFiles/" . (string)$submissionId . ".tar");
    }

    /** @return SourceFileId  */
    public function getId(): SourceFileId
    {
        return $this->Id;
    }

    /** @return string  */
    public function getPath(): string
    {
        return $this->Path;
    }
}
