<?php

namespace backend\events\api;

use yii\base\Event;

class UserChangePasswordRequestEvent extends Event
{
    /**
     * @var \backend\db\models\User
     */
    public $user;
}
