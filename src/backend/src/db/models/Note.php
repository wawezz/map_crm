<?php

namespace backend\db\models;

use backend\db\normalizers\NoteNormalizer;
use yii\helpers\Json;

class Note
{

    public const ELEMENT_TYPE_CLIENT = 1; //Client
    public const ELEMENT_TYPE_LEAD = 2; //Lead
    public const ELEMENT_TYPE_TASK = 3; //Task
    public const ELEMENT_TYPE_PLACE_LEAD = 16; //Place Lead
    public const NOTE_TYPE_LEAD_CREATED = 1; //Lead created
    public const NOTE_TYPE_LEAD_FIELD_UPDATE = 2; //Lead field update
    public const NOTE_TYPE_LEAD_STATUS_CHANGED = 3; //Lead status update
    public const NOTE_TYPE_PLACE_LEAD_CREATED = 13; //Place Lead created
    public const NOTE_TYPE_PLACE_LEAD_FIELD_UPDATE = 14; //Place Lead field update
    public const NOTE_TYPE_PLACE_LEAD_STATUS_CHANGED = 15; //Place Lead status update
    public const NOTE_TYPE_CLIENT_CREATED = 4; //Client created
    public const NOTE_TYPE_CLIENT_FIELD_UPDATE = 5; //Client field update
    public const NOTE_TYPE_COMMON = 6; //Common note
    public const NOTE_TYPE_CALL_IN = 7; //Incoming call
    public const NOTE_TYPE_CALL_OUT = 8; //Outgoing call
    public const NOTE_TYPE_TASK_CREATED = 9; //Task created
    public const NOTE_TYPE_TASK_RESULT = 10; //Task result
    public const NOTE_TYPE_SMS_IN = 11; //Incoming SMS
    public const NOTE_TYPE_SMS_OUT = 12; //Outgoing SMS
    public const NOTE_TYPE_SYSTEM = 25; //System message

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $elementId;

    /**
     * @var int
     */
    public $elementType;

    /**
     * @var int
     */
    public $noteType;

    /**
     * @var int
     */
    public $createdBy;

    /**
     * @var int
     */
    public $createdAt;

    /**
     * @var int
     */
    public $updatedAt;

    /**
     * @var string
     */
    public $dataValue;

    /**
     * Note constructor.
     */
    public function __construct()
    {
        NoteNormalizer::normalize($this);

        if (null === $this->createdAt) {
            $this->createdAt = strtotime("now");
        }

        if (null === $this->updatedAt) {
            $this->updatedAt = strtotime("now");
        }
    }

    public function publicBundle(): array
    {

        return [ 
            '_id' => $this->id,
            'elementId' => $this->elementId,
            'elementType' => $this->elementType,
            'noteType' => $this->noteType,
            'createdBy' => $this->createdBy,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'dataValue' => $this->dataValue
        ];
    }

}
