<?php

declare(strict_type=1);

use App\Domain\Common\Exception\InvalidDTOException;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use PHPUnit\Framework\TestCase;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\Exception\AtLeastOneEnabledTestCaseRequiredException;
use App\Domain\Problem\Exception\InvalidMemoryConstraintException;
use App\Domain\Problem\Exception\InvalidTimeConstraintException;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;

class ProblemTest extends TestCase
{
    public function test_timeConstraint_overflow()
    {
        $this->expectException(InvalidTimeConstraintException::class);
        $langueage  = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueage, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO($langueage, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        Problem::Create('rightTitle', 'rightBoby', 10001, 1, $compileRuleDTOs, $testCaseDTOs);
    }

    public function test_timeConstraint_underflow()
    {
        $this->expectException(InvalidTimeConstraintException::class);
        $langueage = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueage, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO($langueage, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        Problem::Create('rightTitle', 'rightBoby', 0, 1, $compileRuleDTOs, $testCaseDTOs);
    }

    public function test_memoryConstraint_overflow()
    {
        $this->expectException(InvalidMemoryConstraintException::class);
        $langueage = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueage, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO($langueage, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        Problem::Create('rightTitle', 'rightBoby', 1, 2 * 1024 * 1024+1, $compileRuleDTOs, $testCaseDTOs);
    }

    public function test_memoryConstraint_underflow()
    {
        $this->expectException(InvalidMemoryConstraintException::class);
        $langueage = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueage, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO($langueage, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        Problem::Create('rightTitle', 'rightBoby', 1, 0, $compileRuleDTOs, $testCaseDTOs);
    }



    public function test_is_disableFlag_faluse_whenProblemCreate()
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

    public function test_one_language_have_one_compileRule()
    {
        $this->expectException(InvalidDTOException::class);

        $langueageC = Language::C;
        // １つの言語につき、2つのcompileRuleがあるため例外が投げられる
        $compileRuleDTOs = array(
            new CompileRuleFactoryDTO($langueageC, 'rightComand1', 'rightComand1'),
            new CompileRuleFactoryDTO($langueageC, 'rightComand2', 'rightComand2')
        );

        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO($langueageC, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        $problem = Problem::Create('rightTitle', 'rightBoby', 1, 200, $compileRuleDTOs, $testCaseDTOs);
    }
    public function test_one_language_have_one_executionRule()
    {
        $this->expectException(InvalidDTOException::class);
        $langueageC = Language::C;
        $langueageCPP = Language::CPP;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueageC, 'rightComand1', 'rightComand1'));
        // １つの言語につき、2つのExecutionRuleがあるため例外が投げられる
        $ExecutionRuleDTOs = array(
            new ExecutionRuleFactoryDTO($langueageC, 'right1', 'right1', 'right1', 'right1'),
            new ExecutionRuleFactoryDTO($langueageC, 'right2', 'right2', 'right2', 'right2'),
        );
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        $problem = Problem::Create('rightTitle', 'rightBoby', 1, 200, $compileRuleDTOs, $testCaseDTOs);
    }


    public function test_atLeastOneEnabledTestCase_whenCeate()
    {
        $this->expectException(AtLeastOneEnabledTestCaseRequiredException::class);
        $langueageC = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($langueageC, 'rightComand1', 'rightComand1'));
        $testCaseDTOs = array();
        $problem = Problem::Create('rightTitle', 'rightBoby', 1, 200, $compileRuleDTOs, $testCaseDTOs);
    }

    // public function test_atLeastOneEnabledTestCase_whenDisable()
    // {
    // }

    public function test_excutionRule_inTestCase_registered_allSubmittableLanguage()
    {
        $this->expectException(InvalidDTOException::class);
        $langueageC = Language::C;
        $langueageCPP = Language::CPP;
        // 提出可能な言語をcとcppにしている
        $compileRuleDTOs = array(
            new CompileRuleFactoryDTO($langueageC, 'rightComand1', 'rightComand1'),
            new CompileRuleFactoryDTO($langueageCPP, 'rightComand1', 'rightComand1'),

        );

        // 提出可能な言語がcとcppの2つであるが、cにしかExecutionRuleがないため例外が投げられる.
        $ExecutionRuleDTOs = array(
            new ExecutionRuleFactoryDTO($langueageC, 'right1', 'right1', 'right1', 'right1'),
        );
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));
        $problem = Problem::Create('rightTitle', 'rightBoby', 1, 200, $compileRuleDTOs, $testCaseDTOs);
    }
}
