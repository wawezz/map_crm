<?php

use yii\base\InvalidConfigException;
use yii\web\Application;

require __DIR__ . '/../bootstrap.php';

$config = require __DIR__ . '/../config/web.php';

try {
    (new Application($config))->run();
} catch (InvalidConfigException $e) {
    throw $e;
}
