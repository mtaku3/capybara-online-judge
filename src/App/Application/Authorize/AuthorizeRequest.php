<?php

declare(strict_types=1);

namespace App\Application\Authorize;

class AuthorizeRequest
{
    public readonly string $Username;
    public readonly string $Password;

    /**
     * @param string $username
     * @param string $password
     * @return void
     */
    public function __construct(string $username, string $password)
    {
        $this->Username = $username;
        $this->Password = $password;
    }
}
