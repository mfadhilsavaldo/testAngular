<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use App\Model\Connection\DB;
use App\Model\Connection\DBApp;
use App\Model\Connection\DBCore;
use App\Model\Connection\DBZiswaf;
use App\Model\Connection\DBZiswafManual;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        DB::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $config = $settings->get('DB');

            $host = $config['host'];
            $database = $config['database'];
            $user = $config['user'];
            $password = $config['password'];
            $charset = $config['charset'];

            $dsn = "mysql:host=" . $host . ";dbname=" . $database . ";charset=" . $charset;
            $pdo = new DB($dsn, $user, $password);

            $pdo->setAttribute(DB::ATTR_ERRMODE, DB::ERRMODE_EXCEPTION);
            $pdo->setAttribute(DB::ATTR_DEFAULT_FETCH_MODE, DB::FETCH_ASSOC);

            return $pdo;
        },

    ]);
};
