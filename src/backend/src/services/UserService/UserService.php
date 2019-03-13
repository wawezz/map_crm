<?php

namespace backend\services\UserService;

use backend\db\repositories\UserRepositoryInterface;
use yii\caching\ArrayCache;
use yii\helpers\ArrayHelper;

class UserService
{
    /**
     * @var \backend\db\repositories\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var \yii\caching\ArrayCache
     */
    private $cache;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->cache = new ArrayCache;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array $filter
     * @param array $sort
     * @return \backend\db\models\User[]
     */
    public function getAllUsers(int $limit, int $offset, array $filter = [], array $sort = []): array
    {
        $total = $this->countAllUsers($filter);
        $maxOffset = $total > 0 ? ($total - 1) : 0;

        $offset = $offset < 0 ? 25 : $offset;
        $offset = $offset > $maxOffset ? $maxOffset : $offset;

        $limit = $limit < 0 ? 25 : $limit;
        $limit = $limit > 1000 ? 1000 : $limit;

        $users = $this->userRepository->findAll($limit, $offset, $filter, $sort);

        $usersId = ArrayHelper::getColumn($users, 'id');

        return $users;
    }

    public function countAllUsers(array $filter = null): int
    {
        $result = $this->cache->get(__METHOD__);

        if (!$result) {
            $result = $this->userRepository->countAll($filter);

            $this->cache->set(__METHOD__, $result);
        }

        return $result;
    }

}
