<?php

namespace backend\db\normalizers;

use yii\helpers\Json;

abstract class BaseNormalizer implements NormalizerInterface
{
    /**
     * @param \DateTimeInterface|string|null $value
     * @return \DateTimeInterface|null
     * @throws \Exception
     */
    protected static function normalizeDateTime($value): ?\DateTimeInterface
    {
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if (null !== $value && \is_string($value)) {
            return new \DateTimeImmutable($value);
        }

        return null;
    }

    /**
     * @param \DateTimeInterface|string|null $value
     * @return null|string
     */
    protected static function serializeDateTime($value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (!empty($value) && \is_string($value)) {
            return $value;
        }

        return null;
    }

    /**
     * @param string|null $value
     * @return mixed
     */
    protected static function normalizeJson($value)
    {
        if (null !== $value) {
            return Json::decode($value);
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return null|string
     */
    protected static function serializeJson($value): ?string
    {
        if (null !== $value) {
            return Json::encode($value);
        }

        return null;
    }
}
