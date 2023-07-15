<?php

declare(strict_types=1);

namespace Judger\DockerEngineAPI\Stream\DockerRawStream;

use Exception;
use Psr\Http\Message\StreamInterface;

class DockerRawStream
{
    public const HEADER = "application/vnd.docker.raw-stream";

    /**
     * @var StreamInterface
     */
    protected StreamInterface $stream;

    /**
     * @param StreamInterface $stream
     * @return void
     */
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    public function readAll(): Frame
    {
        $this->stream->rewind();

        $stdin = "";
        $stdout = "";
        $stderr = "";

        while (!$this->stream->eof()) {
            $frame = $this->read();
            $stdin .= $frame->Stdin;
            $stdout .= $frame->Stdout;
            $stderr .= $frame->Stderr;
        }

        return new Frame($stdin, $stdout, $stderr);
    }

    public function read(int $length = 1): Frame
    {
        $stdin = "";
        $stdout = "";
        $stderr = "";

        // Specification of application/vnd.docker.raw-stream is available at
        // https://docs.docker.com/engine/api/v1.43/#tag/Container/operation/ContainerAttach:~:text=%5BSTREAM%5D-,Stream%20format,-When%20the%20TTY
        for ($i = 0; $i < $length; $i++) {
            // Read a header, which is 8 bytes long
            $header = $this->stream->read(8);
            if (strlen($header) < 8) {
                break;
            }

            /**
             * Decoder the header
             * - First 1 byte is the type of the stream
             *   0: stdin (is written on stdout)
             *   1: stdout
             *   2: stderr
             * - Next 3 bytes means nothing
             * - Last 4 bytes is the length of the frame
             */
            $decoded = unpack("C1type/C3/N1size", $header);

            $output = $this->stream->read($decoded["size"]);

            if ($decoded["type"] === 0) {
                $stdin .= $output;
            } elseif ($decoded["type"] === 1) {
                $stdout .= $output;
            } elseif ($decoded["type"] === 2) {
                $stderr .= $output;
            } else {
                throw new Exception("Unknown stream type: {$decoded['type']}");
            }
        }

        return new Frame($stdin, $stdout, $stderr);
    }
}
