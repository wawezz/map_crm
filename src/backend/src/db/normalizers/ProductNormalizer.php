<?php

namespace backend\db\normalizers;

class ProductNormalizer extends BaseNormalizer
{
    /**
     * @param \backend\db\models\Product $object
     * @throws \Exception
     */
    public static function normalize($object): void
    {

        $object->createdAt = static::normalizeDateTime($object->createdAt);
        $object->updatedAt = static::normalizeDateTime($object->updatedAt);
    }

    /**
     * @param \backend\db\models\Product $object
     * @return array
     */
    public static function serialize($object): array
    {
        
        return [ 
            'id' => $object->id,
            'name' => $object->name,
            'createdAt' => static::serializeDateTime($object->createdAt),
            'updatedAt' => static::serializeDateTime($object->updatedAt)
        ];
    }
}
