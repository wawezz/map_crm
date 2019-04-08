<?php

namespace backend\db\normalizers;

class TaskNormalizer extends BaseNormalizer
{
    /**
     * @param \backend\db\models\Task $object
     * @throws \Exception
     */
    public static function normalize($object): void
    {
        $object->eventDate = static::normalizeDateTime($object->eventDate);
        $object->createdAt = static::normalizeDateTime($object->createdAt);
        $object->updatedAt = static::normalizeDateTime($object->updatedAt);
    }

    /**
     * @param \backend\db\models\Task $object
     * @return array
     */
    public static function serialize($object): array
    {
        
        return [ 
            'id' => $object->id,
            'elementId' => $object->elementId,
            'elementType' => $object->elementType,
            'type' => $object->type,
            'responsible' => $object->responsible,
            'createdBy' => $object->createdBy,
            'comment' => $object->comment,
            'eventDate' => static::serializeDateTime($object->eventDate),
            'createdAt' => static::serializeDateTime($object->createdAt)
        ];
    }
}
