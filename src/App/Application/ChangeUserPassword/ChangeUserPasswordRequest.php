<?php

declare(strict_types=1);

namespace App\Application\ChangeUserPassword;

class ChangeUserPasswordRequest
{
    /**
     * @var string
     */
    public readonly string $Username;
    /**
     * @var string
     */
    public readonly string $CurrentPassword;
    /**
     * @var string
     */
    public readonly string $NewPassword;

    /**
     * @param string $username
     * @param string $currentPassword
     * @param string $newPassword
     * @return void
     */
    public function __construct(string $username, string $currentPassword, string $newPassword)
    {
        $this->Username = $username;
        $this->CurrentPassword = $currentPassword;
        $this->NewPassword = $newPassword;
    }
}
