<?php

namespace backend\db\normalizers;

class NoteNormalizer extends BaseNormalizer
{
    /**
     * @param \backend\db\models\Note $object
     * @throws \Exception
     */
    public static function normalize($object): void
    {

    }

    /**
     * @param \backend\db\models\Note $object
     * @return array
     */
    public static function serialize($object): array
    {
        
        return [ 
            'id' => $object->id,
            'elementId' => $object->elementId,
            'elementType' => $object->elementType,
            'noteType' => $object->noteType,
            'createdBy' => $object->createdBy,
            'createdAt' => $object->createdAt,
            'updatedAt' => $object->updatedAt,
            'dataValue' => json_encode($object->dataValue)
        ];
    }
}
