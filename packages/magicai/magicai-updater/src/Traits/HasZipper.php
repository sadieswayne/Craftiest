<?php

namespace MagicAI\Updater\Traits;

use MagicAI\Updater\Exceptions\ZipException;
use ZipArchive;

trait HasZipper
{
    public function unzip(string $zipFile, ?string $destination = null): void
    {
        $destination = $destination ?? base_path();

        $zip = new ZipArchive;

        if ($zip->open($zipFile) === true) {
            $zip->extractTo($destination);
            $zip->close();
        } else {
            throw new ZipException('Invalid zip file');
        }
    }
}
