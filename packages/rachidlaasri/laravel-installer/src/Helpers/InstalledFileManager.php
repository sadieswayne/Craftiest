<?php

namespace RachidLaasri\LaravelInstaller\Helpers;

class InstalledFileManager
{
    /**
     * Create installed file.
     */
    public function create(): int|string
    {
        $installedLogFile = storage_path('installed');

        $dateStamp = date('Y/m/d h:i:sa');

        if (! file_exists($installedLogFile)) {
            $message = trans('installer_messages.installed.success_log_message') . $dateStamp . "\n";

            file_put_contents($installedLogFile, $message);
        } else {
            $message = trans('installer_messages.updater.log.success_message') . $dateStamp;

            file_put_contents($installedLogFile, $message . PHP_EOL, FILE_APPEND|LOCK_EX);
        }

        return $message;
    }

    /**
     * Update installed file.
     */
    public function update(): int|string
    {
        return $this->create();
    }
}
