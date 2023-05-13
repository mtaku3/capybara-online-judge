<?php

declare(strict_types=1);

namespace App\Application\Session\Entity;

use App\Application\Session\ValueObject\RefreshToken;
use App\Application\Session\ValueObject\SessionId;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;
use DateInterval;
use DateTimeImmutable;
use Exception;

class Session
{
    public const ExpiresIn = "30 day";

    /**
     * @var SessionId
     */
    private SessionId $Id;
    /**
     * @var UserId
     */
    private UserId $UserId;
    /**
     * @var RefreshToken
     */
    private RefreshToken $RefreshToken;
    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $ExpiresAt;

    /**
     * @param SessionId $id
     * @param UserId $userId
     * @param RefreshToken $refreshToken
     * @param DateTimeImmutable $expiresAt
     * @return void
     */
    public function __construct(SessionId $id, UserId $userId, RefreshToken $refreshToken, DateTimeImmutable $expiresAt)
    {
        $this->Id = $id;
        $this->UserId = $userId;
        $this->RefreshToken = $refreshToken;
        $this->ExpiresAt = $expiresAt;
    }

    /**
     * @param User $user
     * @return Session
     * @throws Exception
     */
    public static function Create(User $user)
    {
        return new Session(SessionId::NextIdentity(), $user->getId(), RefreshToken::Create(), (new DateTimeImmutable())->add(new DateInterval(self::ExpiresIn)));
    }

    /** @return SessionId  */
    public function getId(): SessionId
    {
        return $this->Id;
    }

    /** @return UserId  */
    public function getUserId(): UserId
    {
        return $this->UserId;
    }

    /** @return RefreshToken  */
    public function getRefreshToken(): RefreshToken
    {
        return $this->RefreshToken;
    }

    /** @return DateTimeImmutable  */
    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->ExpiresAt;
    }
}
