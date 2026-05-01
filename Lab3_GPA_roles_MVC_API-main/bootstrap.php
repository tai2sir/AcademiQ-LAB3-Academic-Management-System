<?php

/**
 * Application Bootstrap - Initialize core services and autoloader.
 */

declare(strict_types=1);

// Load application configuration
$config = require __DIR__ . '/config/config.php';

// Load core framework classes
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Flash.php';
require_once __DIR__ . '/core/Validator.php';

// Autoload models and controllers by convention
spl_autoload_register(static function (string $class): void {
    $paths = [
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

// Initialize authentication and database
Auth::bootstrap($config);
