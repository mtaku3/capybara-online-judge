<?php

declare(strict_type=1);

use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use PHPUnit\Framework\TestCase;
use App\Domain\User\Entity\User;
use App\Domain\User\Exception\InvalidPasswordException;
use App\Domain\User\Exception\InvalidUsernameException;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\Exception\InvalidMemoryConstraintException;
use App\Domain\Problem\Exception\InvalidTimeConstraintException;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;

class ProblemTest extends TestCase
{
    public function test_timeConstraint_over()
    {
        $this->expectException(InvalidTimeConstraintException::class);
        $langueage  = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueage, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO($langueage, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        Problem::Create('rightTitle', 'rightBoby', 10001, 1, $compileRuleDTOs, $testCaseDTOs);
    }

    public function test_timeConstraint_under()
    {
        $this->expectException(InvalidTimeConstraintException::class);
        $langueage = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueage, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO($langueage, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        Problem::Create('rightTitle', 'rightBoby', 0, 1, $compileRuleDTOs, $testCaseDTOs);
    }

    public function test_memoryConstraint_under()
    {
        $this->expectException(InvalidMemoryConstraintException::class);
        $langueage = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueage, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO($langueage, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        Problem::Create('rightTitle', 'rightBoby', 1, 0, $compileRuleDTOs, $testCaseDTOs);
    }

    public function test_memoryConstraint_over()
    {
        $this->expectException(InvalidMemoryConstraintException::class);
        $langueage = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueage, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO($langueage, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        Problem::Create('rightTitle', 'rightBoby', 1, 2 * 1024 * 1024+1, $compileRuleDTOs, $testCaseDTOs);
    }

    public function test_is_disableFlag_faluse()
    {
        $langueageC = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueageC, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO($langueageC, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        $problem = Problem::Create('rightTitle', 'rightBoby', 1, 200, $compileRuleDTOs, $testCaseDTOs);
        $testCases = $problem->getTestCases();
        foreach ($testCases as $testCase) {
            $this->assertFalse($testCase->getIsDisabled());
        }
    }

    public function test_problem_has_disableFlag()
    {
        $this->expectNotToPerformAssertions();
        $langueageC = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueageC, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO($langueageC, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        $problem = Problem::Create('rightTitle', 'rightBoby', 1, 200, $compileRuleDTOs, $testCaseDTOs);
        $testCases = $problem->getTestCases();
        foreach ($testCases as $testCase) {
            $testCase->_disable();
        }
    }
}
