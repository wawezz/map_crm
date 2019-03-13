<?php

namespace backend\events\api;

use yii\base\Event;

class UserPasswordChangedEvent extends Event
{
    /**
     * @var \backend\db\models\User
     */
    public $user;

    /**
     * @var string
     */
    public $newPassword;

    /**
     * @var bool
     */
    public $isQuiet = false;
}
