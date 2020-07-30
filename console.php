#!/usr/bin/env php

<?php

use Hj\Command\ExtractCommand;
use Hj\Command\ResetFlagAdminErrorOccured;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Application;

require_once __DIR__ . "/vendor/autoload.php";

$logger = new Logger("console");
$logger->setTimezone(new DateTimeZone("Europe/Paris"));
$output = "[%datetime%] %level_name% : %message%\n";
$formatter = new LineFormatter($output, "d/m/Y H:i:s");

try {
    $streamHandler = new StreamHandler('php://stdout');
    $rotatingHandler = new \Monolog\Handler\RotatingFileHandler("logs/spreadsheet-etl.log");
    $streamHandler->setFormatter($formatter);
    $rotatingHandler->setFormatter($formatter);
    $logger->pushHandler($streamHandler);
    $logger->pushHandler($rotatingHandler);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

// add commands
$application = new Application();
$application->add(new ExtractCommand($logger));
$application->add(new ResetFlagAdminErrorOccured($logger));
$application->add(new \Hj\Command\MigrateFileFromDistant($logger));

try {
    $application->run();
} catch (Exception $e) {
    $logger->error($e->getMessage());
}