<?php

declare(strict_types=1);

namespace App\Utils;

class UploadErrorUtils
{
    /**
     * @param null|int $err
     * @return string
     */
    public static function getMessage(?int $err): string
    {
        if (!isset($err)) {
            return "error code is null";
        }

        switch ($err) {
            case 0:
                return "There is no error, the file uploaded with success";
            case 1:
                return "The uploaded file exceeds the upload_max_filesize directive in php.ini";
            case 2:
                return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
            case 3:
                return "The uploaded file was only partially uploaded";
            case 4:
                return "No file was uploaded";
            case 5:
                return "Missing a temporary folder";
            case 6:
                return "Failed to write file to disk";
            case 7:
                return "A PHP extension stopped the file upload";
            default:
                return "Unknown upload error";
        }
    }
}
