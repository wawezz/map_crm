<?php

use yii\db\Connection;

$host = getenv('MYSQL_HOST');
$database = getenv('MYSQL_DATABASE');

return [
    'class' => Connection::class,
    'dsn' => "mysql:host=$host;dbname=$database",
    'username' => getenv('MYSQL_USER'),
    'password' => getenv('MYSQL_USER_PASSWORD'),
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
    'tablePrefix' => 'app_',
];
