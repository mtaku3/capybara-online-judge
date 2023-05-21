<?php

declare(strict_types=1);

use App\Domain\Common\ValueObject\Language;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client([
    "base_uri" => "http:/v1.42/",
    "curl" => [
        CURLOPT_UNIX_SOCKET_PATH => "/var/run/docker.sock"
    ],
    "http_errors" => false
]);

$baselineDir = __DIR__ . "/../src/Judger/Baseline";

foreach (Language::cases() as $language) {
    $tmpfname = tempnam(sys_get_temp_dir(), "");
    $phar = new PharData($tmpfname . ".tar");
    $phar->buildFromDirectory($baselineDir . "/" . $language->name);

    $res = $client->post("build", [
        RequestOptions::QUERY => [
            "t" => strtolower("COJ-" . $language->name)
        ],
        RequestOptions::HEADERS => [
            "Content-Type" => "application/x-tar"
        ],
        RequestOptions::BODY => fopen($tmpfname . ".tar", "r")
    ]);

    unlink($tmpfname);

    if ($res->getStatusCode() !== 200) {
        throw new Exception("Failed to build an image: " . (string)$res->getBody());
    }
}
