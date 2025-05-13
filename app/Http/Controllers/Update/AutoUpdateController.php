<?php

namespace App\Http\Controllers\Update;

use App\Domains\Marketplace\Repositories\Contracts\ExtensionRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use ZipArchive;

class AutoUpdateController extends Controller
{
    private ?string $tmpBackupDir = null;

    private string $responseHtml = '';

    private string $basePath;

    private string $versionUrl;

    private string $updateUrl;

    private const VERSION_FILENAME = 'version.txt';

    private const SYSTEM_VERSION = 'SYSTEM_VERSION';

    private const ALREADY_UPDATED = 'ALREADY_UPDATED';

    private const INSTALLATION_ERROR = 'INSTALLATION_ERROR';

    private const MAINTENANCE_MODE_ON = 'MAINTENANCE_MODE_ON';

    private const INSTALLATION_SUCCESS = 'INSTALLATION_SUCCESS';

    private const MAINTENANCE_MODE_OFF = 'MAINTENANCE_MODE_OFF';

    private const CHANGELOG = 'CHANGELOG';

    private const DIRECTORY_CREATED = 'DIRECTORY_CREATED';

    private const FILE_EXIST = 'FILE_EXIST';

    private const FILE_COPIED = 'FILE_COPIED';

    private const EXECUTE_UPDATE_SCRIPT = 'EXECUTE_UPDATE_SCRIPT';

    private const TEMP_CLEANED = 'TEMP_CLEANED';

    private const DOWNLOADING = 'DOWNLOADING';

    private const DOWNLOADING_ERROR = 'DOWNLOADING_ERROR';

    private const DOWNLOADING_SUCCESS = 'DOWNLOADING_SUCCESS';

    private const EXCEPTION = 'EXCEPTION';

    private const RECOVERY = 'RECOVERY';

    private const BACKUP_FOUND = 'BACKUP_FOUND';

    private const RECOVERY_ERROR = 'RECOVERY_ERROR';

    private const RECOVERY_SUCCESS = 'RECOVERY_SUCCESS';

    public function __construct()
    {
        $this->configurePhp()
            ->setBasePath()
            ->setVersionUrl()
            ->setUpdateUrl();
    }

    private function configurePhp(): static
    {
        set_time_limit(0); // unlimited max execution time
        ini_set('memory_limit', '-1'); // increase memory_limit to 1GB

        // Register shutdown function to handle fatal errors
        register_shutdown_function([$this, 'handleShutdown']);

        return $this;
    }

    private function setBasePath(): static
    {
        $this->basePath = base_path();

        return $this;
    }

    private function setVersionUrl(): static
    {
        $this->versionUrl = config('magicaiupdater.version_new_base_url');

        return $this;
    }

    private function setUpdateUrl(): void
    {
        $this->updateUrl = config('magicaiupdater.update_new_base_url');
    }

    private function path(?string $append = null): string
    {
        if ($append) {
            $append = '/' . trim($append, '/');
        }

        return $this->basePath . $append;
    }

    private function versionFilePath(): string
    {
        return $this->path(self::VERSION_FILENAME);
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null) {
            $this->log('handleShutdown() Fatal error occurred: ' . $error['message'], '', 'err');
            Artisan::call('up'); // Maintenance mode OFF
            $this->recovery($error['message']);
        }
    }

    private function log(string $messageGroup, string $appendMsg = '', string $type = 'info'): void
    {
        // List of valid constant message groups
        $validMessageGroups = [
            self::VERSION_FILENAME,
            self::SYSTEM_VERSION,
            self::ALREADY_UPDATED,
            self::INSTALLATION_ERROR,
            self::MAINTENANCE_MODE_ON,
            self::INSTALLATION_SUCCESS,
            self::MAINTENANCE_MODE_OFF,
            self::CHANGELOG,
            self::DIRECTORY_CREATED,
            self::FILE_EXIST,
            self::FILE_COPIED,
            self::EXECUTE_UPDATE_SCRIPT,
            self::TEMP_CLEANED,
            self::DOWNLOADING,
            self::DOWNLOADING_ERROR,
            self::DOWNLOADING_SUCCESS,
            self::EXCEPTION,
            self::RECOVERY,
            self::BACKUP_FOUND,
            self::RECOVERY_ERROR,
            self::RECOVERY_SUCCESS,
        ];

        // Check if the message group is valid by referencing directly
        if (in_array($messageGroup, $validMessageGroups)) {
            $messageContent = __('magicaiupdater.' . $messageGroup);
        } else {
            $messageContent = $messageGroup;
        }

        // Create the log message
        $message = sprintf('MagicAI New Updater - [%s]: %s %s', $type, $messageContent, $appendMsg);
        $this->responseHtml .= $message . '<br>';

        // Log the message based on its type
        match ($type) {
            'err'   => Log::error($message),
            'warn'  => Log::warning($message),
            default => Log::info($message),
        };
    }

    /*
    * Download and Install Update.
    */
    public function update(): bool
    {
        // log the current version
        $this->log(self::SYSTEM_VERSION, $this->getCurrentVersion());
        $lastVersionInfo = $this->getLastVersion(); // get the last version
        $lastVersion = null;
        if ($lastVersionInfo['version'] <= $this->getCurrentVersion()) { // Check if the current version is the latest
            $this->log(self::ALREADY_UPDATED);

            return false;
        }

        try {
            if (($lastVersion = $this->download($lastVersionInfo['archive'])) === false) { // Download the latest version
                Artisan::call('up');
                $this->recovery(self::INSTALLATION_ERROR);

                return false;
            }
            $this->log(self::MAINTENANCE_MODE_ON);
            Artisan::call('down'); // Maintenance mode ON
            $this->backupEnLang(); // Backup en.json file

            DB::beginTransaction();
            // Install the latest version ----------------------------------------------
            if (($status = $this->install($lastVersion)) === false) {
                Artisan::call('up'); // Maintenance mode OFF
                $this->recovery(self::INSTALLATION_ERROR); // Recovery the backup files
                DB::rollBack();

                return false;
            }
            // ------------------------------------------------------------------------

            $this->mergeEnLang(); // Merge en.json file
            $this->setCurrentVersion($lastVersionInfo['version']); // update system version
            $this->log(self::INSTALLATION_SUCCESS);
            $this->log(self::SYSTEM_VERSION, $this->getCurrentVersion());

            $settings = Setting::getCache();
            $settings->update([
                'script_version' => $this->getCurrentVersion(),
            ]);
            DB::commit();

            Artisan::call('up'); // Maintenance mode OFF
            Artisan::call('optimize:clear'); // Clear cache after update
            $this->log(self::MAINTENANCE_MODE_OFF);

            $tmpFolderName = $this->path(config('magicaiupdater.tmp_folder_name'));
            if (File::isDirectory($tmpFolderName)) {
                $this->deleteDirectory($tmpFolderName, true);
            }

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $this->log('update() function catch method');
            $this->exceptionToLog($e);
            Artisan::call('up');
            $this->recovery($e->getMessage()); // Recovery the backup files

            return false;
        }
    }

    private function backupEnLang(): void
    {
        $backupDirectory = storage_path('app/lang_backup');
        $backupPath = storage_path('app/lang_backup/en.json');

        if (File::exists(base_path('lang/en.json'))) {
            if (! File::exists($backupDirectory)) {
                File::makeDirectory($backupDirectory, 0755, true, true);
            }
            File::copy(base_path('lang/en.json'), $backupPath);
        }
    }

    private function mergeEnLang(): void
    {
        $backupDirectory = storage_path('app/lang_backup');
        $langPath = base_path('lang/en.json');
        $backupPath = storage_path('app/lang_backup/en.json');

        if (! File::exists($backupDirectory) || ! File::exists($backupPath)) {
            return;
        }

        $backupData = json_decode(File::get($backupPath), true);
        $langData = json_decode(File::get($langPath), true);

        if ($backupData !== null && $langData !== null) {
            foreach ($langData as $langDataKey => $langDataValue) {
                if (! array_key_exists($langDataKey, $backupData)) {
                    $backupData[$langDataKey] = $langDataValue;
                }
            }

            File::put($langPath, json_encode($backupData, JSON_PRETTY_PRINT));
            File::delete($backupPath);
        }
    }

    private function install($archive): bool
    {
        try {
            if (empty($archive) || ! file_exists($archive)) {
                throw new Exception('Invalid archive path provided.');
            }

            $executeCommands = false;
            $updateScript = $this->path(config('magicaiupdater.tmp_folder_name') . '/' . config('magicaiupdater.script_filename'));

            $zip = new ZipArchive;
            if ($zip->open($archive)) {
                $archive = substr($archive, 0, -4);
                $this->checkOutSourceOldVendors();
                $this->log(self::CHANGELOG, '', 'info');

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $zip_item = $zip->statIndex($i);
                    $filename = $zip_item['name'];
                    $dirname = dirname($filename);

                    // Exclude files
                    if (str_ends_with($filename, '/') || dirname($filename) === $archive || str_starts_with($dirname, '__')) {
                        continue;
                    }

                    // Exclude the version.txt
                    if (str_contains($filename, 'version.txt')) {
                        continue;
                    }

                    if (str_starts_with($dirname, $archive)) {
                        $dirname = substr($dirname, (-1) * (strlen($dirname) - strlen($archive) - 1));
                    }

                    $filename = $dirname . '/' . basename($filename); // set new purify path for current file
                    $folderFullPath = $this->path($dirname);
                    if (! is_dir($folderFullPath)) {
                        // Make NEW directory (if it already exists in the current version, continue...)
                        if (file_exists($folderFullPath) && ! is_dir($folderFullPath)) {
                            // A file with the same name exists, rename it
                            $newFilePath = $folderFullPath . '_backup_' . time();
                            rename($folderFullPath, $newFilePath);
                        }
                        mkdir($folderFullPath, 0755, true);
                        $this->log(self::DIRECTORY_CREATED, $dirname, 'info');
                    }

                    $fileFullPath = $this->path($filename);
                    if (! is_dir($fileFullPath)) {
                        // Overwrite a file with its latest version
                        $contents = $zip->getFromIndex($i);

                        if (str_contains($filename, 'upgrade.php')) {
                            file_put_contents($updateScript, $contents);
                            $executeCommands = true;
                        } else {
                            $this->log(self::FILE_COPIED, $filename, 'info');
                            if (file_exists($fileFullPath)) {
                                $this->backup($filename); // backup current version
                            }
                            file_put_contents($fileFullPath, $contents, LOCK_EX);
                        }
                    }
                }

                $zip->close();
                echo '</ul>';
            } else {
                return false;
            }

            if ($executeCommands) {
                require_once $updateScript;
                // upgrade-VERSION.php contains the 'main()' method with a BOOL return to check its execution.
                beforeUpdate();
                afterUpdate();
                unlink($updateScript);
                $this->log(self::EXECUTE_UPDATE_SCRIPT, ' (\'upgrade.php\')');
            }

            $this->deleteFile($archive);
            $this->deleteDirectory($this->tmpBackupDir);
            $this->log(self::TEMP_CLEANED, 'info');
        } catch (Exception $e) {
            Artisan::call('up');
            $this->log('install() function catch method');
            $this->exceptionToLog($e);

            return false;
        }

        return true;
    }

    private function download($filename): string
    {
        $this->log(self::DOWNLOADING);
        $tmpFolderName = $this->path(config('magicaiupdater.tmp_folder_name'));

        if (! File::isDirectory($tmpFolderName)) {
            File::makeDirectory($tmpFolderName, 0755, true, true);
        }

        $localFile = $tmpFolderName . '/' . $filename;
        $remoteFileUrl = $this->updateUrl . '/' . $filename;

        try {
            Http::timeout(600)
                ->withOptions(['sink' => $localFile]) // Stream download directly to file
                ->get($remoteFileUrl);

            $this->log(self::DOWNLOADING_SUCCESS);
        } catch (Exception $e) {
            $this->log(self::DOWNLOADING_ERROR, '', 'err');
            $this->exceptionToLog($e);
            Artisan::call('up'); // Maintenance mode OFF

            return false;
        }

        return $localFile;
    }

    private function fileExists(string $path): bool
    {
        return File::exists($path);
    }

    private function deleteFile(string $path): bool
    {
        return File::delete($path);
    }

    private function deleteDirectory(string $path, bool $preserve = false): bool
    {
        return File::deleteDirectory($path, $preserve);
    }

    private function checkOutSourceOldVendors(): void
    {
        $packagesToRemove = ['pcinaglia/laraupdater', 'rachidlaasri/laravel-installer'];
        foreach ($packagesToRemove as $package) {
            $packagePath = $this->path('vendor/' . $package);
            if (! is_link($packagePath)) {
                if ($this->fileExists($packagePath)) {
                    // Check if the path exists and is not a symbolic link remove the main folder. if rachidlaasri/laravel-installer then remove rachidlaasri and etc
                    $mainFolder = dirname($packagePath, 1);
                    $this->log("Removing package: $packagePath");
                    $this->deleteDirectory($mainFolder);
                    $this->log("Package removed: $packagePath");
                } else {
                    // echo("Package not found: $package");
                    $this->log('Package not found', '', 'err');
                }
            } else {
                // echo("Package is a symbolic link: $package");
                $this->log('Package is a symbolic link');
            }
        }
    }

    public function getCurrentVersion(): string
    {
        return File::get($this->versionFilePath());
    }

    private function setCurrentVersion($version): void
    {
        File::put($this->versionFilePath(), $version);
    }

    private function exceptionToLog(Exception $exception): void
    {
        $this->log(
            self::EXCEPTION,
            '<small>' . $exception->getMessage() . '</small>',
            'err'
        );
    }

    public function check(): array
    {
        try {
            app(ExtensionRepositoryInterface::class)->request('post', 'request', [])->json();
        } catch (Exception $e) {
            $this->exceptionToLog($e);
        }

        $lastVersion = $this->getLastVersion();

        if (version_compare($lastVersion['version'], $this->getCurrentVersion(), '>')) {
            $lastVersion['update'] = 'yes'; // Trigger the new version available.
            $lastVersion['version_format'] = format_double($lastVersion['version']);
        }

        return $lastVersion; // Always return the json because of changelog data.
    }

    private function getLastVersion(): array
    {
        return Http::acceptJson()->get($this->versionUrl)->json();
    }

    private function backup($filename): void
    {
        if (is_null($this->tmpBackupDir)) {
            $this->tmpBackupDir = $this->path('backup_' . date('Ymd'));
        }

        $backupDir = $this->tmpBackupDir;

        if (! File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true, true);
        }

        $fileFullPath = $backupDir . '/' . dirname($filename);

        if (! File::isDirectory($fileFullPath)) {
            File::makeDirectory($fileFullPath, 0755, true, true);
        }

        File::copy($this->path($filename), $backupDir . '/' . $filename); // to backup folder
    }

    private function recovery(string $error): void
    {
        $this->log(self::RECOVERY, '<small>' . $error . '</small>');
        if (is_null($this->tmpBackupDir)) {
            $this->tmpBackupDir = $this->path('backup_' . date('Ymd'));
            // check if the backup folder exists
            if (! File::isDirectory($this->tmpBackupDir)) {
                $this->log(self::RECOVERY_ERROR, '', 'err');
                Artisan::call('up'); // Maintenance mode OFF

                throw new RuntimeException('Backup folder not found.');
            } else {
                $this->log(self::BACKUP_FOUND, '<small>' . $this->tmpBackupDir . '</small>');
            }
        }
        $backupDir = $this->tmpBackupDir;

        try {
            $backupFiles = File::allFiles($backupDir);
            foreach ($backupFiles as $file) {
                $filename = (string) $file;
                $filename = substr($filename, (strlen($filename) - strlen($backupDir) - 1) * (-1));
                File::copy($backupDir . '/' . $filename, $this->path($filename)); // to respective folder
            }
        } catch (Exception $e) {
            $this->log('recovery() function catch method');
            $this->log(self::RECOVERY_ERROR, '', 'err');
            $this->exceptionToLog($e);
            Artisan::call('up'); // Maintenance mode OFF

            throw new RuntimeException('An error occurred during the recovery.');
        }

        $this->log(self::RECOVERY_SUCCESS);
        Artisan::call('up'); // Maintenance mode OFF
        Artisan::call('optimize:clear'); // Clear cache after update
        File::deleteDirectory($this->tmpBackupDir);
    }
}
