<?php

namespace MagicAI\Updater\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MagicAI\Updater\Updater
 *
 * @method static array checker()
 * @method void downloadNewUpdater()
 * @method string backup()
 * @method bool updateNewVersion(string $backupFileName)
 * @method array forPanel()
 * @method string backupFileNameGetFromCache()
 * @method bool versionCheck()
 * @method array backupView()
 */
class Updater extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MagicAI\Updater\Updater::class;
    }
}
