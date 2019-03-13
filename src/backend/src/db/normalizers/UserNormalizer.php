<?php

namespace backend\db\normalizers;

class UserNormalizer extends BaseNormalizer
{
    /**
     * @param \backend\db\models\User $object
     * @throws \Exception
     */
    public static function normalize($object): void
    {
        // $object->utm = static::normalizeJson($object->utm);

        $object->createdAt = static::normalizeDateTime($object->createdAt);
        $object->updatedAt = static::normalizeDateTime($object->updatedAt);
    }

    /**
     * @param \backend\db\models\User $object
     * @return array
     */
    public static function serialize($object): array
    {
        
        return [ 
            'id' => $object->id,
            'email' => $object->email,
            'name' => $object->name,
            'roleId' => (int)$object->roleId,
            'groupId' => (int)$object->groupId,
            'avatarId' => (int)$object->avatarId,
            'sipId' => (int)$object->sipId,
            'sipPass' => $object->sipPass,
            'createdAt' => static::serializeDateTime($object->createdAt),
            'updatedAt' => static::serializeDateTime($object->updatedAt),
            'passwordHash' => $object->passwordHash,
            'secret' => $object->secret
        ];
    }
}
