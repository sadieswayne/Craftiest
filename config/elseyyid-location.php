<?php

return [
    'name' => 'Localization Manager',
    /**
     * Views
     */
    'layout'                   => 'langs::layouts.app',
    'content_section'          => 'content_translation',
    'scripts_section'          => 'scripts',
    'message_success_variable' => 'flash_success',
    'message_flash_variable'   => 'flash_info',
    /**
     * Routes
     */
    'prefix'      => '/dashboard/admin/translations',
    'middlewares' => ['web', 'admin'],
];
