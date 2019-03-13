<?php

namespace backend\db\models;

use backend\db\normalizers\ProductNormalizer;
use yii\helpers\Json;

class Product
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
     * @var \DateTimeImmutable
     */
    public $createdAt;

    /**
     * @var \DateTimeImmutable
     */
    public $updatedAt;

    /**
     * Product constructor.
     * @throws \Exception
     */
    public function __construct()
    {

        // Compress properties after pdo hydration
        ProductNormalizer::normalize($this);

        if (null === $this->createdAt) {
            $this->createdAt = new \DateTimeImmutable;
        }

        if (null === $this->updatedAt) {
            $this->updatedAt = new \DateTimeImmutable;
        }
        
    }

    public function publicBundle(): array
    {

        return [ 
            'id' => $this->id,
            'name' => $this->name
        ];
    }

}
