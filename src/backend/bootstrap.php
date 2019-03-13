<?php

defined('YII_DEBUG') or define('YII_DEBUG', getenv('_DEBUG') === 'true');
defined('YII_ENV') or define('YII_ENV', getenv('_ENV'));

define('BASE_PATH', __DIR__);

$scheme = getenv('USE_SSL') === 'true' ? 'https' : 'http';
$host = getenv('HTTP_HOSTNAME');

define('BASE_URL', "$scheme://$host");

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

require __DIR__ . '/config/container.php';
require __DIR__ . '/config/listeners.php';
