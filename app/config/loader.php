<?php

$loader = new \Phalcon\Loader();
$loader->registerDirs([
    APP_PATH . '/tasks',
    APP_PATH . '/models',
    APP_PATH . '/library',
]);
$loader->register();

// Setup Composer autoload
include BASE_PATH . '/vendor/autoload.php';
