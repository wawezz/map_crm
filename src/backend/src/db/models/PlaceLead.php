<?php

namespace backend\db\models;

use backend\db\normalizers\PlaceLeadNormalizer;
use yii\helpers\Json;

class PlaceLead
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
    public $address;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $status;

    /**
     * @var int
     */
    public $price;

    /**
     * @var int
     */
    public $rating;

    /**
     * @var string
     */
    public $review;
    
    /**
     * @var string
     */
    public $website;
    
    /**
     * @var string
     */
    public $geometry;

    /**
     * @var string
     */
    public $geo;

    /**
     * @var string
     */
    public $data;

    /**
     * @var bool
     */
    public $toSync;

    /**
     * @var int
     */
    public $campaignCode;

    /**
     * @var bool
     */
    public $isImportant;

    /**
     * @var int
     */
    public $createdBy;

    /**
     * @var int
     */
    public $createdBySecret;

    /**
    * @var string
    */      
    public $statusName;

    /**
     * @var \DateTimeImmutable
     */
    public $createdAt;

    /**
     * @var \DateTimeImmutable
     */
    public $updatedAt;

    /**
     * @var \DateTimeImmutable
     */
    public $contractAt;

    /**
     * @var \DateTimeImmutable
     */
    public $nextFollowupDate;

    /**
     * PlaceLead constructor.
     * @throws \Exception
     */
    public function __construct()
    {

        // Compress properties after pdo hydration
        PlaceLeadNormalizer::normalize($this);

        if (null === $this->createdAt) {
            $this->createdAt = new \DateTimeImmutable;
        }

        if (null === $this->updatedAt) {
            $this->updatedAt = new \DateTimeImmutable;
        }

        if (null === $this->contractAt) {
            $this->contractAt = new \DateTimeImmutable;
        }
        
    }

    public function publicBundle(): array
    {

        return [ 
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'type' => $this->type,
            'status' => $this->status,
            'statusName' => $this->statusName,
            'price' => $this->price,
            'rating' => $this->rating,
            'review' => $this->review,
            'website' => $this->website,
            'geometry' => $this->geometry,
            'geo' => $this->geo,
            'data' => $this->data,
            'toSync' => $this->toSync,
            'campaignCode' => $this->campaignCode,
            'isImportant' => $this->isImportant,
            'createdBy' => ($this->createdBySecret)?$this->createdBy.'-'.$this->createdBySecret:$this->createdBy,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'contractAt' => $this->contractAt,
            'nextFollowupDate' => $this->nextFollowupDate
        ];
    }

}
