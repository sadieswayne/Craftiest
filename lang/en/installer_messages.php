<?php

return [

    /*
     *
     * Shared translations.
     *
     */
    'title'  => __('Laravel Installer'),
    'next'   => __('Next Step'),
    'back'   => __('Previous'),
    'finish' => __('Install'),
    'forms'  => [
        'errorTitle' => __('The Following errors occurred:'),
    ],

    /*
     *
     * Home page translations.
     *
     */
    'welcome' => [
        'templateTitle' => __('Welcome'),
        'title'         => __('Laravel Installer'),
        'message'       => __('Easy Installation and Setup Wizard.'),
        'next'          => __('Check Requirements'),
    ],

    /*
     *
     * Requirements page translations.
     *
     */
    'requirements' => [
        'templateTitle' => __('Step 1 | Server Requirements'),
        'title'         => __('Server Requirements'),
        'next'          => __('Check Permissions'),
    ],

    /*
     *
     * Permissions page translations.
     *
     */
    'permissions' => [
        'templateTitle' => __('Step 2 | Permissions'),
        'title'         => __('Permissions'),
        'next'          => __('Configure Environment'),
    ],

    /*
     *
     * Environment page translations.
     *
     */
    'environment' => [
        'menu' => [
            'templateTitle'  => __('Step 3 | Environment Settings'),
            'title'          => __('Environment Settings'),
            'desc'           => __('Please select how you want to configure the apps <code>.env</code> file.'),
            'wizard-button'  => __('Form Wizard Setup'),
            'classic-button' => __('Classic Text Editor'),
        ],
        'wizard' => [
            'templateTitle' => __('Step 3 | Environment Settings | Guided Wizard'),
            'title'         => __('Guided <code>.env</code> Wizard'),
            'tabs'          => [
                'environment' => __('Environment'),
                'database'    => __('Database'),
                'application' => __('Application'),
            ],
            'form' => [
                'name_required'                      => __('An environment name is required.'),
                'app_name_label'                     => __('App Name'),
                'app_name_placeholder'               => __('App Name'),
                'app_environment_label'              => __('App Environment'),
                'app_environment_label_local'        => __('Local'),
                'app_environment_label_developement' => __('Development'),
                'app_environment_label_qa'           => __('Qa'),
                'app_environment_label_production'   => __('Production'),
                'app_environment_label_other'        => __('Other'),
                'app_environment_placeholder_other'  => __('Enter your environment...'),
                'app_debug_label'                    => __('App Debug'),
                'app_debug_label_true'               => __('True'),
                'app_debug_label_false'              => __('False'),
                'app_log_level_label'                => __('App Log Level'),
                'app_log_level_label_debug'          => __('debug'),
                'app_log_level_label_info'           => __('info'),
                'app_log_level_label_notice'         => __('notice'),
                'app_log_level_label_warning'        => __('warning'),
                'app_log_level_label_error'          => __('error'),
                'app_log_level_label_critical'       => __('critical'),
                'app_log_level_label_alert'          => __('alert'),
                'app_log_level_label_emergency'      => __('emergency'),
                'app_url_label'                      => __('App URL'),
                'app_url_placeholder'                => __('App URL'),
                'db_connection_failed'               => __('Could not connect to the database.'),
                'db_connection_label'                => __('Database Connection'),
                'db_connection_label_mysql'          => __('mysql'),
                'db_connection_label_sqlite'         => __('sqlite'),
                'db_connection_label_pgsql'          => __('pgsql'),
                'db_connection_label_sqlsrv'         => __('sqlsrv'),
                'db_host_label'                      => __('Database Host'),
                'db_host_placeholder'                => __('Database Host'),
                'db_port_label'                      => __('Database Port'),
                'db_port_placeholder'                => __('Database Port'),
                'db_name_label'                      => __('Database Name'),
                'db_name_placeholder'                => __('Database Name'),
                'db_username_label'                  => __('Database User Name'),
                'db_username_placeholder'            => __('Database User Name'),
                'db_password_label'                  => __('Database Password'),
                'db_password_placeholder'            => __('Database Password'),

                'app_tabs' => [
                    'more_info'                     => __('More Info'),
                    'broadcasting_title'            => __('Broadcasting, Caching, Session, &amp; Queue'),
                    'broadcasting_label'            => __('Broadcast Driver'),
                    'broadcasting_placeholder'      => __('Broadcast Driver'),
                    'cache_label'                   => __('Cache Driver'),
                    'cache_placeholder'             => __('Cache Driver'),
                    'session_label'                 => __('Session Driver'),
                    'session_placeholder'           => __('Session Driver'),
                    'queue_label'                   => __('Queue Driver'),
                    'queue_placeholder'             => __('Queue Driver'),
                    'redis_label'                   => __('Redis Driver'),
                    'redis_host'                    => __('Redis Host'),
                    'redis_password'                => __('Redis Password'),
                    'redis_port'                    => __('Redis Port'),
                    'mail_label'                    => __('Mail'),
                    'mail_driver_label'             => __('Mail Driver'),
                    'mail_driver_placeholder'       => __('Mail Driver'),
                    'mail_host_label'               => __('Mail Host'),
                    'mail_host_placeholder'         => __('Mail Host'),
                    'mail_port_label'               => __('Mail Port'),
                    'mail_port_placeholder'         => __('Mail Port'),
                    'mail_username_label'           => __('Mail Username'),
                    'mail_username_placeholder'     => __('Mail Username'),
                    'mail_password_label'           => __('Mail Password'),
                    'mail_password_placeholder'     => __('Mail Password'),
                    'mail_encryption_label'         => __('Mail Encryption'),
                    'mail_encryption_placeholder'   => __('Mail Encryption'),
                    'pusher_label'                  => __('Pusher'),
                    'pusher_app_id_label'           => __('Pusher App Id'),
                    'pusher_app_id_palceholder'     => __('Pusher App Id'),
                    'pusher_app_key_label'          => __('Pusher App Key'),
                    'pusher_app_key_palceholder'    => __('Pusher App Key'),
                    'pusher_app_secret_label'       => __('Pusher App Secret'),
                    'pusher_app_secret_palceholder' => __('Pusher App Secret'),
                ],
                'buttons' => [
                    'setup_database'    => __('Setup Database'),
                    'setup_application' => __('Setup Application'),
                    'install'           => __('Install'),
                ],
            ],
        ],
        'classic' => [
            'templateTitle' => __('Step 3 | Environment Settings | Classic Editor'),
            'title'         => __('Classic Environment Editor'),
            'save'          => __('Save .env'),
            'back'          => __('Use Form Wizard'),
            'install'       => __('Save and Install'),
        ],
        'success' => __('Your .env file settings have been saved.'),
        'errors'  => __('Unable to save the .env file, Please create it manually.'),
    ],

    'install' => __('Install'),

    /*
     *
     * Installed Log translations.
     *
     */
    'installed' => [
        'success_log_message' => __('Laravel Installer successfully INSTALLED on '),
    ],

    /*
     *
     * Final page translations.
     *
     */
    'final' => [
        'title'         => __('Installation Finished'),
        'templateTitle' => __('Installation Finished'),
        'finished'      => __('Application has been successfully installed.'),
        'migration'     => __('Migration &amp; Seed Console Output:'),
        'console'       => __('Application Console Output:'),
        'log'           => __('Installation Log Entry:'),
        'env'           => __('Final .env File:'),
        'exit'          => __('Click here to exit'),
    ],

    /*
     *
     * Update specific translations
     *
     */
    'updater' => [
        /*
         *
         * Shared translations.
         *
         */
        'title' => 'Laravel Updater',

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'welcome' => [
            'title'   => 'Welcome To The Updater',
            'message' => 'Welcome to the update wizard.',
        ],

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'overview' => [
            'title'           => __('Overview'),
            'message'         => __('There is 1 update.|There are :number updates.'),
            'install_updates' => __('Install Updates'),
        ],

        /*
         *
         * Final page translations.
         *
         */
        'final' => [
            'title'    => __('Finished'),
            'finished' => __('Application\'s database has been successfully updated.'),
            'exit'     => __('Click here to exit'),
        ],

        'log' => [
            'success_message' => __('Laravel Installer successfully UPDATED on '),
        ],
    ],
];
