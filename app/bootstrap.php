<?php

use Phalcon\Di\FactoryDefault\Cli as CliDi;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Config\Adapter\Yaml as YamlConfig;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

/**
 * The FactoryDefault Dependency Injector automatically registers the services that
 * provide a full stack framework. These default services can be overidden with custom ones.
 */
$di = new CliDi();

/**
 * Include Services
 */
include APP_PATH . '/config/services.php';

/**
 * Get config service for use in inline setup below
 */
$config = $di->getConfig();

/**
 * Include Autoloader
 */
include APP_PATH . '/config/loader.php';

/**
 * Create a console application
 */
$console = new ConsoleApp($di);


/**
 * Read command map.f
 */
$commands = new YamlConfig(BASE_PATH . '/commands.yml');

/**
 * Process the console arguments
 */
if (!isset($argv[1])) {
    list($task, $action) = explode(':', $commands->default);
    $params = [];
} else if (preg_match('/^([a-zA-Z]+)\:([a-zA-Z]+)$/', $argv[1], $matches)) {
    list($task, $action) = [$matches[1], $matches[2]];
    $params = array_slice($argv, 2);
} else if (isset($commands->map[$argv[1]])) {
    list($task, $action) = explode(':', $commands->map[$argv[1]]);
    $params = array_slice($argv, 2);
} else {
    list($task, $action) = explode(':', $commands->default);
    $params = array_slice($argv, 1);
}

try {

    /**
     * Handle
     */
    $console->handle([
        'task'   => $task,
        'action' => $action,
        'params' => $params,
    ]);

    /**
     * If configs is set to true, then we print a new line at the end of each execution
     *
     * If we dont print a new line,
     * then the next command prompt will be placed directly on the left of the output
     * and it is less readable.
     *
     * You can disable this behaviour if the output of your application needs to don't have a new line at end
     */
    if (isset($config["printNewLine"]) && $config["printNewLine"]) {
        echo PHP_EOL;
    }

} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(255);
}
