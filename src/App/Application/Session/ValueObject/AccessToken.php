<?php

declare(strict_types=1);

namespace App\Application\Session\ValueObject;

use App\Domain\User\Entity\User;
use DateInterval;
use DateTimeImmutable;
use DomainException;
use Firebase\JWT\JWT;

class AccessToken
{
    public const ExpiresIn = "1 day";

    private string $Value;

    /**
     * @param string $value
     * @return void
     */
    public function __construct(string $value)
    {
        $this->Value = $value;
    }

    /**
     * @param User $user
     * @return string
     * @throws DomainException
     */
    public static function Create(User $user)
    {
        return JWT::encode([
            "UserId" => (string)$user->getId(),
            "exp" => (new DateTimeImmutable())->add(new DateInterval(self::ExpiresIn))->getTimestamp(),
            "iat" => (new DateTimeImmutable())->getTimestamp()
        ], $_ENV["JWT_SECRET"], "HS256");
    }

    /** @return string  */
    public function getValue(): string
    {
        return $this->Value;
    }

    /**
     * @param AccessToken $accessToken
     * @return bool
     */
    public function equals(AccessToken $accessToken): bool
    {
        return $this->Value === $accessToken->getValue();
    }

    /** @return string  */
    public function __tostring(): string
    {
        return $this->Value;
    }
}
