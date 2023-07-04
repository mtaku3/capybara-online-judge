<?php

declare(strict_type=1);

use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\Entity\ExecutionRule;
use App\Domain\User\Entity\User;
use App\Domain\Problem\Entity\Problem;
use PHPUnit\Framework\TestCase;
use App\Domain\Submission\Entity\SourceFile;
use App\Domain\Submission\Entity\Submission;
use App\Domain\Submission\Entity\TestResult;
use App\Domain\Submission\Exception\AlreadyJudgedException;
use App\Domain\Submission\Exception\TestResultForGivenTestCaseAlreadyExistsException;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use App\Domain\Submission\ValueObject\SubmissionType;

class SubmissionTest extends TestCase
{
    protected $obj;
    protected function setUp(): void
    {
    }

    public function test_success_submission()
    {
        $language = Language::C;
        $compileRuleDTOs = array (new CompileRuleFactoryDTO($language,'rightComand', 'rightComand' ));
        $ExecutionRuleDTOs = array (new ExecutionRuleFactoryDTO($language, 'right', 'right', 'right', 'right'));
        $TestcaseDTOs = array (new TestcasefactoryDTO('rightComand',$ExecutionRuleDTOs ));
        $obj1 = User::Create('bavoiub', 'baviuhvfeqir', false);
        $obj2 = Problem::Create('test', 'testbody', 130, 150, $compileRuleDTOs, $TestcaseDTOs);
        $this->assertInstanceOf(Submission::class, Submission::Create($obj1, $obj2, $language, SubmissionType::SourceCode, 100));
    }

    public function test_success_testresult()
    {
        $this->assertInstanceOf(TestResult::class, TestResult::compar));
    }

    public function test_invalid_testresult_judge()
    {
        $submission = new Submission();
        $submission->getJudgeResult();

    }

    public function test_invalid_username_character()
    {
        $this->expectException(AlreadyJudgedException::class);

        User::Create('ああああああ', 'rightPassword', false);
        User::Create('+-~\\{{}}[', 'rightPassword', false);
        User::Create('];=::;<>?\\/', 'rightPassword', false);
        User::Create('!@#$%^&*', 'rightPassword', false);
    }
    public function test_invalid_username_length()
    {
        $this->expectException(InvalidUsernameException::class);
        User::Create('san', 'rightPassword', false);
        User::Create('nizyuumozi01234567890', 'rightPassword', false);
    }

    public function test_invalid_password_character()
    {
        $this->expectException(InvalidPasswordException::class);

        User::Create('rightname', '全角はちもじいじょう', false);
        User::Create('rightname', '+-~\\{{}}[];=::;<>?\\/', false);
    }

    public function test_invalid_password_length()
    {
        $this->expectException(InvalidPasswordException::class);

        User::Create('rightname', 'hatimoz', false);
        User::Create('rightname', 'sanzyuumozi17697516974516943548549849849498949887512409', false);
    }
}
