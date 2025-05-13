<?php

$path = env('MAGICAI_USER_TYPE', '') === 'tester' ? 'update-test/' : 'updater-v2/';

return [
    'base_url'             => 'https://api.liquid-themes.com/magicai/' . $path,
    'updater_download_url' => 'https://api.liquid-themes.com/magicai/' . $path . 'updater.php.zip',
    'version_url'          => 'https://api.liquid-themes.com/magicai/' . $path . 'magicaiupdater.json',
];
