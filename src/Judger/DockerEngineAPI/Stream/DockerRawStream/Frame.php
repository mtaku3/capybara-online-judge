<?php

declare(strict_types=1);

namespace Judger\DockerEngineAPI\Stream\DockerRawStream;

class Frame
{
    /**
     * @var string
     */
    public readonly string $Stdin;

    /**
     * @var string
     */
    public readonly string $Stdout;

    /**
     * @var string
     */
    public readonly string $Stderr;

    public function __construct(string $stdin = "", string $stdout = "", string $stderr = "")
    {
        $this->Stdin = $stdin;
        $this->Stdout = $stdout;
        $this->Stderr = $stderr;
    }
}
