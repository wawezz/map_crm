<?php

namespace backend\events\api;

use yii\base\Event;

class UserCreatedEvent extends Event
{
    /**
     * @var \backend\db\models\User
     */
    public $user;

    /**
     * @var string|null
     */
    public $password;
}
