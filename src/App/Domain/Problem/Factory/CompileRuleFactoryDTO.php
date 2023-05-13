<?php

declare(strict_types=1);

namespace App\Domain\Problem\Factory;

use App\Domain\Common\ValueObject\Language;

class CompileRuleFactoryDTO
{
    /**
     * @var Language
     */
    public readonly Language $Language;
    /**
     * @var string
     */
    public readonly string $SourceCodeCompileCommand;
    /**
     * @var string
     */
    public readonly string $FileCompileCommand;

    /**
     * @param Language $language
     * @param string $sourceCodeCompileCommand
     * @param string $fileCompileCommand
     * @return void
     */
    public function __construct(Language $language, string $sourceCodeCompileCommand, string $fileCompileCommand)
    {
        $this->Language = $language;
        $this->SourceCodeCompileCommand = $sourceCodeCompileCommand;
        $this->FileCompileCommand = $fileCompileCommand;
    }
}
