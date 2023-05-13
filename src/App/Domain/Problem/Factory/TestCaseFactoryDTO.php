<?php

declare(strict_types=1);

namespace App\Domain\Problem\Factory;

class TestCaseFactoryDTO
{
    public readonly string $Title;
    /**
     * @var ExecutionRuleFactoryDTO[]
     */
    public readonly array $ExecutionRuleDTOs;

    /**
     * @param string $title
     * @param bool $isDisabled
     * @param ExecutionRuleFactoryDTO[] $executionRuleDTOs
     * @return void
     */
    public function __construct(string $title, array $executionRuleDTOs)
    {
        $this->Title = $title;
        $this->ExecutionRuleDTOs = $executionRuleDTOs;
    }
}
