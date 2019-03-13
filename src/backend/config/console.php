<?php

use notamedia\sentry\SentryTarget;
use yii\caching\ArrayCache;
use yii\console\controllers\MigrateController;

$params = require __DIR__ . '/params.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'viewPath' => dirname(__DIR__) . '/src/views',
    'aliases' => require __DIR__ . '/aliases.php',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'backend\commands',
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                'backend\db\migrations',
            ],
        ],
        'mongodb-migrate' => [
            'class' => 'yii\mongodb\console\controllers\MigrateController',
            'migrationNamespaces' => [
                'backend\db\migrationsMongo',
            ],
        ],
    ],
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'mongodb' => require __DIR__ . '/mongo.php',
        'mailer' => require __DIR__ . '/mailer.php',
        'cache' => [
            'class' => ArrayCache::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => SentryTarget::class,
                    'dsn' => getenv('SENTRY_DSN'),
                    'levels' => ['error', 'warning'],
                    'context' => true,
                ],
            ],
        ],
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

return $config;
