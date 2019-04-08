<?php

namespace backend\db\normalizers;

class LeadNormalizer extends BaseNormalizer
{
    /**
     * @param \backend\db\models\Lead $object
     * @throws \Exception
     */
    public static function normalize($object): void
    {

        $object->createdAt = static::normalizeDateTime($object->createdAt);
        $object->updatedAt = static::normalizeDateTime($object->updatedAt);
        if($object->completedAt) $object->completedAt = static::normalizeDateTime($object->completedAt);
        if($object->firstCallAt) $object->firstCallAt = static::normalizeDateTime($object->firstCallAt);
    }

    /**
     * @param \backend\db\models\Lead $object
     * @return array
     */
    public static function serialize($object): array
    {
        
        return [ 
            'id' => $object->id,
            'name' => $object->name,
            'client' => $object->client,
            'responsible' => $object->responsible,
            'createdBy' => $object->createdBy,
            'status' => $object->status,
            'createdAt' => static::serializeDateTime($object->createdAt),
            'completedAt' => static::serializeDateTime($object->completedAt),
            'budget' => $object->budget,
            'orderId' => $object->orderId,
            'firstCallAt' => static::serializeDateTime($object->firstCallAt),
            'countryId' => $object->countryId,
            'currency' => $object->currency,
            'product' => $object->product,
            'productCount' => $object->productCount,
            'productPrice' => $object->productPrice,
            'shippingPrice' => $object->shippingPrice,
            'postOrder' => $object->postOrder,
            'rejectionReason' => $object->rejectionReason
        ];
    }
}
