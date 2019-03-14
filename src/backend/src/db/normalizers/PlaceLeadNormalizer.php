<?php

namespace backend\db\normalizers;

class PlaceLeadNormalizer extends BaseNormalizer
{
    /**
     * @param \backend\db\models\PlaceLead $object
     * @throws \Exception
     */
    public static function normalize($object): void
    {

        $object->createdAt = static::normalizeDateTime($object->createdAt);
        $object->updatedAt = static::normalizeDateTime($object->updatedAt);
        $object->contractAt = static::normalizeDateTime($object->contractAt);
        if($object->nextFollowupDate) $object->nextFollowupDate = static::normalizeDateTime($object->nextFollowupDate);
    }

    /**
     * @param \backend\db\models\PlaceLead $object
     * @return array
     */
    public static function serialize($object): array
    {
        
        return [ 
            'id' => $object->id,
            'name' => $object->name,
            'address' => $object->address,
            'phone' => $object->phone,
            'type' => $object->type,
            'status' => $object->status,
            'price' => $object->price,
            'rating' => $object->rating,
            'review' => $object->review,
            'website' => $object->website,
            'geometry' => $object->geometry,
            'geo' => $object->geo,
            'data' => $object->data,
            'toSync' => $object->toSync,
            'campaignCode' => $object->campaignCode,
            'isImportant' => $object->isImportant,
            'createdBy' => $object->createdBy,
            'createdAt' => static::serializeDateTime($object->createdAt),
            'updatedAt' => static::serializeDateTime($object->updatedAt),
            'contractAt' => static::serializeDateTime($object->contractAt),
            'nextFollowupDate' => static::serializeDateTime($object->nextFollowupDate)
        ];
    }
}
