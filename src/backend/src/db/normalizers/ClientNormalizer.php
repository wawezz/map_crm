<?php

namespace backend\db\normalizers;

class ClientNormalizer extends BaseNormalizer
{
    /**
     * @param \backend\db\models\Client $object
     * @throws \Exception
     */
    public static function normalize($object): void
    {

        $object->createdAt = static::normalizeDateTime($object->createdAt);
        $object->updatedAt = static::normalizeDateTime($object->updatedAt);
    }

    /**
     * @param \backend\db\models\Client $object
     * @return array
     */
    public static function serialize($object): array
    {
        
        return [ 
            'id' => $object->id,
            'email' => $object->email,
            'emailVerified' => $object->emailVerified,
            'name' => $object->name,
            'surname' => $object->surname,
            'phone' => $object->phone,
            'phoneVerified' => $object->phoneVerified,
            'workPhone' => $object->workPhone,
            'otherPhone' => $object->otherPhone,
            'countryId' => (int)$object->countryId,
            'state' => $object->state,
            'city' => $object->city,
            'street' => $object->street,
            'building' => $object->building,
            'flat' => $object->flat,
            'createdBy' => $object->createdBy,
            'responsible' => $object->responsible,
            'createdAt' => static::serializeDateTime($object->createdAt),
            'updatedAt' => static::serializeDateTime($object->updatedAt),
            'zip' => $object->zip,
            'skype' => $object->skype
        ];
    }
}
