<?php

declare(strict_types=1);

use App\Application\Authorize\AuthorizeRequest;
use App\Application\Authorize\AuthorizeUseCase;
use App\Application\Exception\WrongPasswordException;
use App\Domain\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Test\Infrastructure\Session\MockSessionRepository;
use Test\Infrastructure\User\MockUserRepository;

class AuthorizeUseCaseTest extends TestCase
{
    protected AuthorizeUseCase $authorizeUseCase;
    protected MockUserRepository $mockUserRepository;
    protected MockSessionRepository $mockSessionRepository;

    protected function setUp(): void
    {
        $_ENV["JWT_SECRET"] = 'SECRET';
        $this->mockUserRepository = new MockUserRepository();
        $this->mockSessionRepository = new MockSessionRepository();
        $this->authorizeUseCase = new AuthorizeUseCase($this->mockUserRepository, $this->mockSessionRepository);
        $user = User::Create('username', 'Password', false);
        $this->mockUserRepository->save($user);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_authorize_with_valid_credential(): void
    {
        $request = new AuthorizeRequest('username', 'Password');
        $response = $this->authorizeUseCase->handle($request);
    }

    public function test_authorize_with_invalid_credential(): void
    {
        $this->expectException(WrongPasswordException::class);
        $request = new AuthorizeRequest('username', 'Password1');
        $response = $this->authorizeUseCase->handle($request);
    }
}
