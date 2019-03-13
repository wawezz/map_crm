<?php

namespace backend\db\models;

use backend\db\normalizers\ClientNormalizer;
use yii\helpers\Json;

class Client
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
    public $email;

    /**
     * @var int
     */
    public $phone;

    /**
     * @var string
     */
    public $workPhone;

    /**
     * @var string
     */
    public $otherPhone;

    /**
     * @var int
     */
    public $countryId;

    /**
     * @var string
     */
    public $state;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $building;

    /**
     * @var string
     */
    public $flat;

    /**
     * @var string
     */
    public $zip;

    /**
     * @var string
     */
    public $skype;

    /**
     * @var int
     */
    public $createdBy;

    /**
     * @var int
     */
    public $createdBySecret;

    /**
     * @var int
     */
    public $responsible;

    /**
     * @var int
     */
    public $responsibleSecret;

    /**
     * @var string
     */
    public $surname;

    /**
     * @var bool
     */
    public $emailVerified;

    /**
     * @var bool
     */
    public $phoneVerified;

    /**
     * @var \DateTimeImmutable
     */
    public $createdAt;

    /**
     * @var \DateTimeImmutable
     */
    public $updatedAt;

    /**
     * Client constructor.
     * @throws \Exception
     */
    public function __construct()
    {

        // Compress properties after pdo hydration
        ClientNormalizer::normalize($this);

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
            'email' => $this->email,
            'name' => $this->name,
            'workPhone' => $this->workPhone,
            'countryId' => $this->countryId,
            'createdBy' => ($this->createdBySecret)?$this->createdBy.'-'.$this->createdBySecret:$this->createdBy,
            'responsible' => ($this->responsibleSecret)?$this->responsible.'-'.$this->responsibleSecret:$this->responsible,
            'surname' => $this->surname,
            'emailVerified' => $this->emailVerified,
            'phoneVerified' => $this->phoneVerified,
            'otherPhone' => $this->otherPhone,
            'zip' => $this->zip,
            'flat' => $this->flat,
            'building' => $this->building,
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'skype' => $this->skype
        ];
    }

}
