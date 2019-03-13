<?php

namespace backend\events\api;

use yii\base\Event;

class UserLoginEvent extends Event
{
    /**
     * @var \backend\db\models\User
     */
    public $user;
}
