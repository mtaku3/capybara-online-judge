<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use Ramsey\Uuid\Uuid as RamseyUuid;

class UserId
{
    /**
     * @var string
     */
    private string $Value;

    /**
     * @param string $value
     * @return void
     */
    public function __construct(string $value)
    {
        $this->Value = $value;
    }

    /** @return UserId  */
    public static function NextIdentity(): self
    {
        return new self((string)RamseyUuid::uuid4());
    }

    /** @return string  */
    public function getValue(): string
    {
        return $this->Value;
    }

    /**
     * @param UserId $uuid
     * @return bool
     */
    public function equals(self $uuid): bool
    {
        return $this->Value === $uuid->getValue();
    }

    /** @return string  */
    public function __toString(): string
    {
        return $this->Value;
    }
}
