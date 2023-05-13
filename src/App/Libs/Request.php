<?php

declare(strict_types=1);

namespace App\Libs;

class Request
{
    /**
     * @var array
     */
    public array $params;
    /**
     * @var string
     */
    public string $reqMethod;
    /**
     * @var string
     */
    public string $contentType;

    /**
     * @param array $params
     * @return void
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
        $this->reqMethod = trim($_SERVER['REQUEST_METHOD']);
        $this->contentType = !empty($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    }

    /** @return array  */
    public function getBody(): array
    {
        if ($this->reqMethod !== 'POST') {
            return '';
        }

        $body = [];
        foreach ($_POST as $key => $value) {
            $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $body;
    }

    /** @return mixed  */
    public function getJSON(): mixed
    {
        if ($this->reqMethod !== 'POST') {
            return [];
        }

        if (strcasecmp($this->contentType, 'application/json') !== 0) {
            return [];
        }

        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content);

        return $decoded;
    }
}
