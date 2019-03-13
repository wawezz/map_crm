<?php

namespace backend\events\api;

use yii\base\Event;

class TaskAddEvent extends Event
{
    /**
     * @var \backend\db\models\User
     */
    public $user;

    /**
     * @var \backend\db\models\Task
     */
    public $task;
}
