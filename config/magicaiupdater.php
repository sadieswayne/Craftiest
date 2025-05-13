<?php

return [

    'tmp_folder_name' => 'tmp',

    'script_filename' => 'upgrade.php',

    'update_baseurl' => 'https://api.liquid-themes.com/magicai/updater-v2',

    'version_baseurl' => 'https://api.liquid-themes.com/magicai/updater-v2/magicaiupdater.json',

    'update_new_base_url' => env('MAGICAI_USER_TYPE', 'customer') == 'tester' ? 'https://api.liquid-themes.com/magicai/update-test' : 'https://api.liquid-themes.com/magicai/updater-v2',

    'version_new_base_url' => env('MAGICAI_USER_TYPE', 'customer') == 'tester' ? 'https://api.liquid-themes.com/magicai/update-test/magicaiupdater.json' : 'https://api.liquid-themes.com/magicai/updater-v2/magicaiupdater.json',
];
