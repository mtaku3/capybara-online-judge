<?php

declare(strict_types=1);

namespace App\Domain\Common\Entity;

interface IFile
{
    /** @return string  */
    public function getPath(): string;
}
