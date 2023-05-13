<?php

declare(strict_types=1);

namespace App\Libs;

class Response
{
    /**
     * @var int
     */
    private int $status = 200;

    /**
     * @param int $code
     * @return Response
     */
    public function status(int $code): self
    {
        $this->status = $code;
        http_response_code($this->status);
        return $this;
    }

    /**
     * @param array $data
     * @return void
     */
    public function toJSON(array $data = []): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * @param string $body
     * @return void
     */
    public function send(string $body = ""): void
    {
        echo $body;
    }
}
