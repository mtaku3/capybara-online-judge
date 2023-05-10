<?php

declare(strict_types=1);

namespace App\Domain\Problem\Factory;

class TestCaseDTO
{
    public readonly string $Title;
    /**
     * @var array<ExecutionRuleDTO>
     */
    public readonly array $ExecutionRuleDTOs;

    /**
     * @param string $title
     * @param bool $isDisabled
     * @param array<ExecutionRuleDTO> $executionRuleDTOs
     * @return void
     */
    public function __construct(string $title, array $executionRuleDTOs)
    {
        $this->Title = $title;
        $this->ExecutionRuleDTOs = $executionRuleDTOs;
    }
}
