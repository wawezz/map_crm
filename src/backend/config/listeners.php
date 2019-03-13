<?php

use yii\base\Event;

$listeners = [
    \backend\listeners\EmailEventListener::class,
    \backend\listeners\WebsocketEventListener::class,
];

foreach ($listeners as $listener) {
    /** @var \backend\listeners\EventListenerInterface $instance */
    $instance = \Yii::$container->get($listener);

    $rules = $instance->rules();

    foreach ($rules as $rule) {
        Event::on($rule[0], $rule[1], $rule[2]);
    }
}
