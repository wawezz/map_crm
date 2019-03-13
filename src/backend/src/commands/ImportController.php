<?php

namespace backend\commands;

use backend\db\common\generator\UuidGenerator;
use backend\db\models\User;
use backend\db\repositories\db\AbstractDbRepository;
use backend\db\repositories\UserRepositoryInterface;
use yii\console\Controller;
use yii\db\Connection;
use yii\helpers\Console;
use yii\helpers\Json;

/**
 * Class ImportController
 * @package backend\commands
 * @deprecated !!!
 */
class ImportController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function actionStartico()
    {
        $this->stdout("Begin import users...\n");

        $start = microtime(true);

        \Yii::$app->db->transaction([$this, 'importUsers']);

        $spentTime = round(microtime(true) - $start, 0);

        $this->stdout("Import users cipants done ({$spentTime}s).\n");
    }

    public function importUsers(Connection $db)
    {
        $userRepository = clone $this->getUserRepository();

        if ($userRepository instanceof AbstractDbRepository) {
            $userRepository->setDb($db);
        }

        $usersData = file_get_contents(BASE_PATH . '/data/users.json');

        $users = Json::decode($usersData);

        unset($usersData);

        $total = \count($users);

        Console::startProgress(0, $total);

        foreach ((array)$users as $k => $userData) {
            Console::updateProgress($k, $total);

            $usersCount = $userRepository->countByEmail($userData['Email']);

            if ($usersCount > 0) {
                continue;
            }

            $user = $this->createUserObject($userData);

            $userRepository->insert($user);
        }

        unset($users);

        Console::endProgress();
    }

    /**
     * @param array $userData
     * @return \backend\db\models\User
     * @throws \Exception
     */
    private function createUserObject(array $userData): User
    {
        $createdDate = new \DateTime($userData['RegDate']);

        $wallets = $this->getEthMiddlewareService()->createWallets();

        $user = new User();
        $user->id = $userData['Id'];
        $user->email = mb_strtolower($userData['Email']);
        $user->name = trim($userData['name']);
        $user->passwordHash = 'asp:' . $userData['PasswordHash'];
        $user->status = User::STATUS_NEW;
        $user->createdAt = $createdDate;
        $user->updatedAt = $createdDate;

        return $user;
    }

    private function getUuidGenerator(): UuidGenerator
    {
        return \Yii::$container->get(UuidGenerator::class);
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        return \Yii::$container->get(UserRepositoryInterface::class);
    }

}
