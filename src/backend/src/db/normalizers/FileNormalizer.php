<?php

namespace backend\db\normalizers;

use backend\db\models\File;

class FileNormalizer
{
    /**
     * @param File $object
     */
    public static function normalize(File $object): void
    {
        if (property_exists($object, 'createdAt')) {
            $object->createdAt = new \DateTimeImmutable($object->createdAt);
        }
    }
}
