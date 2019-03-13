<?php

namespace backend\db\normalizers;

interface NormalizerInterface
{
    /**
     * @param object $object
     */
    public static function normalize($object): void;

    /**
     * @param object $object
     * @return array
     */
    public static function serialize($object): array;
}
