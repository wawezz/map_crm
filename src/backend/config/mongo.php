<?php

use yii\mongodb\Connection;

$user = getenv('MONGODB_USERNAME');
$pass = getenv('MONGODB_PASSWORD');
$database = getenv('MONGODB_DATABASE');

return 
[
    'class' => Connection::class,
    'dsn' => "mongodb://@mongo/$database",
    'options' => [
        "username" => $user,
        "password" => $pass
    ]
];
