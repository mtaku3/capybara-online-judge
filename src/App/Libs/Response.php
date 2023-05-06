<?php

declare(strict_types=1);

namespace App\Libs;

class Response
{
    private int $status = 200;

    public function status(int $code): self
    {
        $this->status = $code;
        http_response_code($this->status);
        return $this;
    }

    public function toJSON(array $data = []): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function send(string $body = ""): void
    {
        echo $body;
    }
}
