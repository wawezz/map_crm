<?php

use yii\redis\Connection;

return 
[
    'class' => Connection::class,
        'hostname' => 'redis',
        'port' => 6379,
        'database' => 0,
];
