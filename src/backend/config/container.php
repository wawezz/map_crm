<?php

use backend\db\common\Command as AppSqlCommand;
use backend\db\common\generator\UuidGenerator;
use backend\db\common\IdGeneratorInterface;
use backend\db\repositories\db\DbUserRepository; 
use backend\db\repositories\db\DbFileRepository; 
use backend\db\repositories\db\DbClientRepository; 
use backend\db\repositories\db\DbProductRepository; 
use backend\db\repositories\db\DbLeadRepository; 
use backend\db\repositories\db\DbTaskRepository; 
use backend\db\repositories\UserRepositoryInterface;
use backend\db\repositories\FileRepositoryInterface;
use backend\db\repositories\ClientRepositoryInterface;
use backend\db\repositories\ProductRepositoryInterface;
use backend\db\repositories\LeadRepositoryInterface;
use backend\db\repositories\TaskRepositoryInterface;
use backend\services\Session;
use yii\db\Command as YiiSqlCommand;
use yii\db\Connection;
 
$dbConfig = require __DIR__ . '/db.php';

$container = \Yii::$container;

$container->setDefinitions([
    Connection::class => $dbConfig,
    YiiSqlCommand::class => AppSqlCommand::class,
    IdGeneratorInterface::class => UuidGenerator::class,
    UserRepositoryInterface::class => DbUserRepository::class,
    ClientRepositoryInterface::class => DbClientRepository::class,
    ProductRepositoryInterface::class => DbProductRepository::class,
    LeadRepositoryInterface::class => DbLeadRepository::class,
    TaskRepositoryInterface::class => DbTaskRepository::class,
    FileRepositoryInterface::class => DbFileRepository::class,
]);

$container->set(\PDO::class, function () use ($dbConfig) {
    return new \PDO(
        $dbConfig['dsn'],
        $dbConfig['username'],
        $dbConfig['password']
    );
});
