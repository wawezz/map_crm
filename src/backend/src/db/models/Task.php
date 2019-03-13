<?php

namespace backend\db\models;
use backend\db\normalizers\TaskNormalizer;

class Task
{
    public const TASK_TYPE_CONNECT = 'connect'; //Связаться с клиентом
    public const TASK_TYPE_MEETING = 'meeting'; //Встреча
    
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
     * @var string
     */
    public $type;

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
    public $responsibleName;

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
    public $createdByName;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var \DateTimeImmutable
     */
    public $eventDate;

    /**
     * @var \DateTimeImmutable
     */
    public $createdAt;

    /**
     * @var \DateTimeImmutable
     */
    public $updatedAt;

    /**
     * Task constructor.
     * @throws \Exception
     */
    public function __construct()
    {

        // Compress properties after pdo hydration
        TaskNormalizer::normalize($this);

        if (null === $this->createdAt) {
            $this->createdAt = new \DateTimeImmutable;
        }

        if (null === $this->updatedAt) {
            $this->updatedAt = new \DateTimeImmutable;
        }

        if (null === $this->eventDate) {
            $this->eventDate = new \DateTimeImmutable;
        }
        
    }

    public function publicBundle(): array
    {

        return [ 
            'id' => $this->id,
            'elementId' => $this->elementId,
            'elementType' => $this->elementType,
            'type' => $this->type,
            'responsible' => ($this->responsibleSecret)?$this->responsible.'-'.$this->responsibleSecret:$this->responsible,
            'createdBy' => ($this->createdBySecret)?$this->createdBy.'-'.$this->createdBySecret:$this->createdBy,
            'comment' => $this->comment,
            'eventDate' => $this->eventDate,
            'createdByName' => $this->createdByName,
            'responsibleName' => $this->responsibleName
        ];
    }
}
