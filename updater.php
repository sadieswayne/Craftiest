<?php

$array = [
    [
        'label'      => 'PHP version >= 8.2.0',
        'permission' => version_compare(phpversion(), '8.2.0', '>='),
    ],
    [
        'label'      => 'PHP cURL extension',
        'permission' => extension_loaded('curl'),
    ],
    [
        'label'      => 'PHP Zip extension',
        'permission' => extension_loaded('zip'),
    ],
    [
        'label'      => 'PHP max_execution_time >= 300',
        'permission' => ini_get('max_execution_time') >= 300 || ini_get('max_execution_time') == '-1',
    ],
    [
        'label'      => 'Disk space >= 100MB',
        'permission' => disk_free_space(base_path()) >= 100 * 1024 * 1024,
    ],
];

return [
    'list'    => $array,
    'version' => '1.0.0',
];
