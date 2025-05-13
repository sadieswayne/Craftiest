<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server Requirements
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel server requirements, you can add as many
    | as your application require, we check if the extension is enabled
    | by looping through the array and run "extension_loaded" on it.
    |
    */
    'core' => [
        'minPhpVersion' => '8.1.0',
    ],
    'final' => [
        'key'     => true,
        'publish' => false,
    ],
    'requirements' => [
        'php' => [
            'Ctype',
            'cURL',
            'DOM',
            'Fileinfo',
            'Filter',
            'Hash',
            'Mbstring',
            'OpenSSL',
            'PCRE',
            'PDO',
            'Session',
            'Tokenizer',
            'XML',
            'zip',
        ],
        'apache' => [
            'mod_rewrite',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Folders Permissions
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel folders permissions, if your application
    | requires more permissions just add them to the array list bellow.
    |
    */
    'permissions' => [
        'storage/framework/' => '666',
        'storage/logs/'      => '666',
        'bootstrap/cache/'   => '666',
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Form Wizard Validation Rules & Messages
    |--------------------------------------------------------------------------
    |
    | This are the default form field validation rules. Available Rules:
    | https://laravel.com/docs/5.4/validation#available-validation-rules
    |
    */
    'environment' => [
        'form' => [
            'rules' => [
                'app_name'            => 'required|string|max:50',
                'environment'         => 'required|string|max:50',
                'environment_custom'  => 'required_if:environment,other|max:50',
                'app_debug'           => 'required|string',
                'app_log_level'       => 'required|string|max:50',
                'app_url'             => 'required|url',
                'database_connection' => 'required|string|max:50',
                'database_hostname'   => 'required|string|max:50',
                'database_port'       => 'required|numeric',
                'database_name'       => 'required|string|max:50',
                'database_username'   => 'required|string|max:50',
                'database_password'   => 'nullable|string|max:50',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Installed Middleware Options
    |--------------------------------------------------------------------------
    | Different available status switch configuration for the
    | canInstall middleware located in `canInstall.php`.
    |
    */
    'installed' => [
        'redirectOptions' => [
            'route' => [
                'name' => '/',
                'data' => [],
            ],
            'abort' => [
                'type' => '404',
            ],
            'dump' => [
                'data' => 'Dumping a not found message.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Selected Installed Middleware Option
    |--------------------------------------------------------------------------
    | The selected option fo what happens when an installer instance has been
    | Default output is to `/resources/views/error/404.blade.php` if none.
    | The available middleware options include:
    | route, abort, dump, 404, default, ''
    |
    */
    'installedAlreadyAction' => '',

    /*
    |--------------------------------------------------------------------------
    | Updater Enabled
    |--------------------------------------------------------------------------
    | Can the application run the '/update' route with the migrations.
    | The default option is set to False if none is present.
    | Boolean value
    |
    */
    'updaterEnabled' => 'false',

];
