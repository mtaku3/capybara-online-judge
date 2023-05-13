<?php

declare(strict_types=1);

namespace App\Domain\Submission\Entity;

use App\Domain\Submission\ValueObject\SourceFileId;
use App\Domain\Submission\ValueObject\SubmissionId;

class SourceFile
{
    private SourceFileId $Id;
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
        return new SourceFile(SourceFileId::NextIdentity(), __DIR__ . "/data/source/" . $submissionId);
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
