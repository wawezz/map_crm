<?php

namespace backend\common\web;

use backend\db\repositories\UserRepositoryInterface;
use yii\rbac\CheckAccessInterface;

class UserAccessChecker implements CheckAccessInterface
{
    /**
     * @var \backend\db\repositories\UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Checks if the user has the specified permission.
     * @param string|int $userId the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param string $permissionId the name of the permission to be checked against
     * @param array $params name-value pairs that will be passed to the rules associated
     * with the role and permissions assigned to the user.
     * @return bool whether the user has the specified permission.
     * @throws \yii\base\InvalidParamException if $permissionId does not refer to an existing permission
     */
    public function checkAccess($userId, $permissionId, $params = [])
    {
        $user = null === $userId ? null : $this->userRepository->findById($userId);

        return null !== $user && null !== $user->roleId
            ? ($permissionId == $user->roleId)
            : false;
    }
}
