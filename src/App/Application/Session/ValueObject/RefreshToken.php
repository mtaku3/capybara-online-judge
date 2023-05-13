<?php

declare(strict_types=1);

namespace App\Application\Session\ValueObject;

class RefreshToken
{
    public const Length = 64;

    private string $Value;

    /**
     * @param string $value
     * @return void
     */
    public function __construct(string $value)
    {
        $this->Value = $value;
    }

    public static function Create(): RefreshToken
    {
        return new RefreshToken(RefreshToken::RandomBytes(self::Length));
    }

    private static function RandomBytes(int $length = 64): string
    {
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length-($length%2))/2));
    }

    /** @return string  */
    public function getValue(): string
    {
        return $this->Value;
    }

    /**
     * @param RefreshToken $refreshToken
     * @return bool
     */
    public function equals(self $refreshToken): bool
    {
        return $this->Value === $refreshToken->getValue();
    }

    /** @return string  */
    public function __toString(): string
    {
        return $this->Value;
    }
}
