<?php

namespace backend\db\models;
use backend\db\normalizers\FileNormalizer;

class File
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $type;

    /**
     * @var \DateTimeImmutable
     */
    public $createdAt;

    /**
     * File constructor.
     */
    public function __construct()
    {
        // Compress properties after pdo hydration
        FileNormalizer::normalize($this);

        if (null === $this->createdAt) {
            $this->createdAt = new \DateTimeImmutable;
        }
    }
}
