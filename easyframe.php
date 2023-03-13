#!/usr/bin/env php
<?php
if (isset($GLOBALS['_composer_autoload_path'])) {
    define('PHPUNIT_COMPOSER_INSTALL', $GLOBALS['_composer_autoload_path']);

    unset($GLOBALS['_composer_autoload_path']);
} else {
    foreach (array(__DIR__ . '/../../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php') as $file) {
        if (file_exists($file)) {
            define('PHPUNIT_COMPOSER_INSTALL', $file);

            break;
        }
    }

    unset($file);
}

require PHPUNIT_COMPOSER_INSTALL;
