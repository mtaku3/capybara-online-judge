<?php

declare(strict_types=1);

namespace App\Application\Session\ValueObject;

use Ramsey\Uuid\Uuid as RamseyUuid;

class SessionId
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

    /** @return SessionId  */
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
     * @param SessionId $uuid 
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
