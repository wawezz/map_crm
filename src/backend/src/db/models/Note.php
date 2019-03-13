<?php

namespace backend\db\models;

use backend\db\normalizers\NoteNormalizer;
use yii\helpers\Json;

class Note
{

    public const ELEMENT_TYPE_CLIENT = 1; //Клиент
    public const ELEMENT_TYPE_LEAD = 2; //Лид
    public const ELEMENT_TYPE_TASK = 3; //Задача
    public const NOTE_TYPE_LEAD_CREATED = 1; //Лид создана
    public const NOTE_TYPE_LEAD_FIELD_UPDATE = 2; //Отредактировано поле лида
    public const NOTE_TYPE_LEAD_STATUS_CHANGED = 3; //Статус лида изменен
    public const NOTE_TYPE_CLIENT_CREATED = 4; //Клиент создан
    public const NOTE_TYPE_CLIENT_FIELD_UPDATE = 5; //Отредактировано поле клиента
    public const NOTE_TYPE_COMMON = 6; //Обычное примечание
    public const NOTE_TYPE_CALL_IN = 7; //Входящий звонок
    public const NOTE_TYPE_CALL_OUT = 8; //Исходящий звонок
    public const NOTE_TYPE_TASK_CREATED = 9; //Задача создана
    public const NOTE_TYPE_TASK_RESULT = 10; //Результат по задаче
    public const NOTE_TYPE_SMS_IN = 11; //Входящее смс
    public const NOTE_TYPE_SMS_OUT = 12; //Исходящее смс
    public const NOTE_TYPE_SYSTEM = 25; //Системное сообщение

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
