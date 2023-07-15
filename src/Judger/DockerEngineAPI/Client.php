<?php

declare(strict_types=1);

namespace Judger\DockerEngineAPI;

use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Judger\DockerEngineAPI\Exception\DockerEngineAPIException;
use Judger\DockerEngineAPI\Stream\DockerRawStream\DockerRawStream;
use Judger\DockerEngineAPI\Stream\DockerRawStream\Frame;
use RuntimeException;

class Client
{
    /**
     * @var GuzzleHttpClient
     */
    private readonly GuzzleHttpClient $Client;

    /** @return void  */
    public function __construct()
    {
        $this->Client = new GuzzleHttpClient([
            "base_uri" => "http:/v1.42/",
            "curl" => [
                CURLOPT_UNIX_SOCKET_PATH => "/var/run/docker.sock"
            ],
            "http_errors" => false,
            RequestOptions::HEADERS => [
                "Content-Type" => "application/json"
            ]
        ]);
    }

    /**
     * @param array $body
     * @return array
     * @throws GuzzleException
     * @throws DockerEngineAPIException
     */
    public function createContainer(array $body): array
    {
        $res = $this->Client->post("containers/create", [
            RequestOptions::JSON => $body
        ]);

        $body = json_decode((string)$res->getBody(), true);
        if ($res->getStatusCode() !== 201) {
            throw new DockerEngineAPIException($body["message"], $res->getStatusCode());
        }

        return $body;
    }

    /**
     * @param string $containerId
     * @return void
     * @throws GuzzleException
     * @throws DockerEngineAPIException
     */
    public function startContainer(string $containerId): void
    {
        $res = $this->Client->post("containers/{$containerId}/start");

        $body = json_decode((string)$res->getBody(), true);
        if ($res->getStatusCode() !== 204 && $res->getStatusCode() !== 304) {
            throw new DockerEngineAPIException($body["message"], $res->getStatusCode());
        }
    }

    /**
     * @param string $containerId
     * @return void
     * @throws GuzzleException
     * @throws DockerEngineAPIException
     */
    public function stopContainer(string $containerId): void
    {
        $res = $this->Client->post("containers/{$containerId}/stop");

        $body = json_decode((string)$res->getBody(), true);
        if ($res->getStatusCode() !== 204 && $res->getStatusCode() !== 304) {
            throw new DockerEngineAPIException($body["message"], $res->getStatusCode());
        }
    }

    public function removeContainer(string $containerId): void
    {
        $res = $this->Client->delete("containers/{$containerId}");

        $body = json_decode((string)$res->getBody(), true);
        if ($res->getStatusCode() !== 204) {
            throw new DockerEngineAPIException($body["message"], $res->getStatusCode());
        }
    }

    /**
     * @param string $containerId
     * @param string $pathToExtract
     * @param mixed $fp
     * @return void
     * @throws GuzzleException
     * @throws DockerEngineAPIException
     */
    public function extractArchiveInContainer(string $containerId, string $pathToExtract, mixed $fp): void
    {
        $res = $this->Client->put("containers/{$containerId}/archive", [
            RequestOptions::QUERY => [
                "path" => $pathToExtract
            ],
            RequestOptions::BODY => $fp,
            RequestOptions::HEADERS => [
                "Content-Type" => "application/x-tar"
            ],
        ]);

        $body = json_decode((string)$res->getBody(), true);
        if ($res->getStatusCode() !== 200) {
            throw new DockerEngineAPIException($body["message"], $res->getStatusCode());
        }
    }

    /**
     * @param string $containerId
     * @param array $body
     * @return array
     * @throws GuzzleException
     * @throws DockerEngineAPIException
     */
    public function createExecInstance(string $containerId, array $body): array
    {
        $res = $this->Client->post("containers/{$containerId}/exec", [
            RequestOptions::JSON => $body
        ]);

        $body = json_decode((string)$res->getBody(), true);
        if ($res->getStatusCode() !== 201) {
            throw new DockerEngineAPIException($body["message"], $res->getStatusCode());
        }

        return $body;
    }

    /**
     * @param string $execId
     * @return null|Frame
     * @throws GuzzleException
     * @throws DockerEngineAPIException
     * @throws RuntimeException
     * @throws Exception
     */
    public function startExecInstance(string $execId): ?Frame
    {
        $res = $this->Client->post("exec/{$execId}/start");

        $body = json_decode((string)$res->getBody(), true);
        if ($res->getStatusCode() !== 200) {
            throw new DockerEngineAPIException($body["message"], $res->getStatusCode());
        }

        if ($res->getHeaderLine("Content-Type") === DockerRawStream::HEADER) {
            $stream = new DockerRawStream($res->getBody());
            return $stream->readAll();
        } else {
            return null;
        }
    }

    /**
     * @param string $execId
     * @return array
     * @throws GuzzleException
     * @throws DockerEngineAPIException
     */
    public function inspectExecInstance(string $execId): array
    {
        $res = $this->Client->get("exec/{$execId}/json");

        $body = json_decode((string)$res->getBody(), true);
        if ($res->getStatusCode() !== 200) {
            throw new DockerEngineAPIException($body["message"], $res->getStatusCode());
        }

        return $body;
    }
}
