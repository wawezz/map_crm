<?php

namespace backend\db\models;

use backend\db\repositories\UserRepositoryInterface;

trait UserIdentityTrait
{
    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return \yii\web\IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public static function findIdentity($id)
    {
        /** @var \backend\db\repositories\UserRepositoryInterface $userRepository */
        $userRepository = \Yii::$container->get(UserRepositoryInterface::class);

        return $userRepository->findById($id);
    }

    /**
     * Finds an identity by the given token.
     * @param \Lcobucci\JWT\Token $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return \yii\web\IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $uid = $token->getClaim('uid');
        $tokenType = $token->getClaim('type');

        return $tokenType === 'access' ? static::findIdentity($uid) : null;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     * @throws \Exception
     */
    public function getAuthKey()
    {
        throw new \Exception('Unsupported method ' . __METHOD__);
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }
}
