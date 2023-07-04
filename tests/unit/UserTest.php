<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Domain\User\Entity\User;
use App\Domain\User\Exception\InvalidPasswordException;
use App\Domain\User\Exception\InvalidUsernameException;

class UserTest extends TestCase
{
    protected $obj;
    protected function setUp(): void
    {
    }

    public function testsuccess()
    {
        $this->assertInstanceOf(User::class, User::Create('bavoiub', 'baviuhvfeqir', false));
    }


    public function test_invalid_username_character()
    {
        $this->expectException(InvalidUsernameException::class);

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
