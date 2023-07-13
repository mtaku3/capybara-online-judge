<?php

declare(strict_types=1);

namespace App\Presentation;

class CustomTwigFilter
{
    /**
     * @var \Twig\TwigFilter[]
     */
    public array $filters = [];

    public function __construct()
    {
        $this->filters[] = new \Twig\TwigFilter("format_miliseconds", function (mixed $value): string {
            $res = (float)$value;

            $second = 1000;
            $minute = 60 * $second;
            $hour = 60 * $minute;

            if ($res < $second) {
                return $res . " ms";
            } elseif ($res < $minute) {
                return sprintf("%.1f", $res / $second) . " s";
            } elseif ($res < $hour) {
                return sprintf("%.1f", $res / $minute) . " m";
            } else {
                return sprintf("%.1f", $res / $hour) . " h";
            }
        });
        $this->filters[] = new \Twig\TwigFilter("format_bytes", function (mixed $value): string {
            $res = (float)$value;

            $kilobytes = 1024;
            $megabytes = 1024 * $kilobytes;
            $gigabytes = 1024 * $megabytes;
            $terabytes = 1024 * $gigabytes;

            if ($res < $kilobytes) {
                return $res . " B";
            } elseif ($res < $megabytes) {
                return sprintf("%.1f", $res / $kilobytes) . " KB";
            } elseif ($res < $gigabytes) {
                return sprintf("%.1f", $res / $megabytes) . " MB";
            } elseif ($res < $terabytes) {
                return sprintf("%.1f", $res / $gigabytes) . " GB";
            } else {
                return sprintf("%.1f", $res / $terabytes) . " TB";
            }
        });
        $this->filters[] = new \Twig\TwigFilter("format_kilobytes", function (mixed $value): string {
            $res = (float)$value;

            $megabytes = 1024;
            $gigabytes = 1024 * $megabytes;
            $terabytes = 1024 * $gigabytes;

            if ($res < $megabytes) {
                return $res . " KB";
            } elseif ($res < $gigabytes) {
                return sprintf("%.1f", $res / $megabytes) . " MB";
            } elseif ($res < $terabytes) {
                return sprintf("%.1f", $res / $gigabytes) . " GB";
            } else {
                return sprintf("%.1f", $res / $terabytes) . " TB";
            }
        });
    }
}
