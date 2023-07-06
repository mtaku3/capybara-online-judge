<?php

declare(strict_types=1);

use App\Application\CreateUser\CreateUserRequest;
use App\Application\CreateUser\CreateUserUseCase;
use PHPUnit\Framework\TestCase;
use Test\Infrastructure\User\MockUserRepository;

// TODO: This should be deleted because of redundancy

class CreateUserUseCaseTest extends TestCase
{
    protected CreateUserUseCase $createUserUseCase;

    protected function setUp(): void
    {
        $mockUserRepository = new MockUserRepository();
        $this->createUserUseCase = new CreateUserUseCase($mockUserRepository);
    }

    public function test(): void
    {
        $createUserResponse = $this->createUserUseCase->handle(new CreateUserRequest("takumi", "takumi1234", true));
        $user = $createUserResponse->User;

        $this->assertEquals($user->getUsername(), "takumi");
        $this->assertEquals($user->getIsAdmin(), true);
    }
}
