<?php

namespace backend\commands;

use backend\db\models\User;
use backend\db\repositories\UserRepositoryInterface;
use yii\base\Event;
use yii\console\Controller;

class UserController extends Controller
{
    public function actionChangePassword(string $email)
    {
        /** @var \app\db\repositories\UserRepositoryInterface $userRepository */
        $userRepository = \Yii::$container->get(UserRepositoryInterface::class);

        if (empty($email)) {
            throw new \RuntimeException('Empty email.');
        }

        $password = $this->prompt('New password:');

        $user = $userRepository->findByEmail($email);

        if (null === $user) {
            throw new \RuntimeException('User not found.');
        }

        $user->password = $password;

        if ($userRepository->update($user)) {
            $this->stdout('Password saved.' . PHP_EOL);
        } else {
            $this->stderr('Password NOT saved.' . PHP_EOL);
        }
    }


}
