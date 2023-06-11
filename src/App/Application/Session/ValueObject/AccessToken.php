<?php

declare(strict_types=1);

namespace App\Application\Session\ValueObject;

use App\Application\Session\Exception\InvalidAccessTokenException;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;
use DateInterval;
use DateTimeImmutable;
use DomainException;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class AccessToken
{
    public const ExpiresIn = "P1D";
    public const Algorithm = "HS256";

    /**
     * @var string
     */
    private string $Value;
    /**
     * @var UserId
     */
    private UserId $UserId;
    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $ExpiresAt;
    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $IssuedAt;

    /**
     * @param string $value
     * @return void
     * @throws InvalidAccessTokenException
     */
    public function __construct(string $value)
    {
        $this->Value = $value;

        try {
            $decoded = (array)JWT::decode($this->Value, new Key($_ENV["JWT_SECRET"], self::Algorithm));
        } catch (Throwable $e) {
            throw new InvalidAccessTokenException();
        }

        if (!isset($decoded["UserId"]) || !isset($decoded["exp"]) || !isset($decoded["iat"])) {
            throw new InvalidAccessTokenException();
        }

        $this->UserId = new UserId($decoded["UserId"]);
        $this->ExpiresAt = (new DateTimeImmutable())->setTimestamp(intval($decoded["exp"]));
        $this->IssuedAt = (new DateTimeImmutable())->setTimestamp(intval($decoded["iat"]));
    }

    /**
     * @param User $user
     * @return AccessToken
     * @throws DomainException
     */
    public static function Create(User $user): AccessToken
    {
        return new AccessToken(JWT::encode([
            "UserId" => (string)$user->getId(),
            "exp" => (new DateTimeImmutable())->add(new DateInterval(self::ExpiresIn))->getTimestamp(),
            "iat" => (new DateTimeImmutable())->getTimestamp()
        ], $_ENV["JWT_SECRET"], self::Algorithm));
    }

    /** @return string  */
    public function getValue(): string
    {
        return $this->Value;
    }

    /** @return UserId  */
    public function getUserId(): UserId
    {
        return $this->UserId;
    }

    /** @return DateTimeImmutable  */
    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->ExpiresAt;
    }

    /** @return DateTimeImmutable  */
    public function getIssuedAt(): DateTimeImmutable
    {
        return $this->IssuedAt;
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
    public function __toString(): string
    {
        return $this->Value;
    }
}
