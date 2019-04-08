<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\Lead;
use backend\db\models\PlaceLead;
use backend\db\models\Client;
use backend\db\models\Note;
use backend\db\models\User;
use backend\db\repositories\db\DbNoteRepository;
use backend\db\repositories\ClientRepositoryInterface;
use backend\db\repositories\LeadRepositoryInterface;
use backend\db\repositories\PlaceLeadRepositoryInterface;
use backend\db\repositories\UserRepositoryInterface;
use backend\db\common\generator\UuidGenerator;

/**
 * Class NoteAddFormModel
 * @package backend\forms\api
 */
class NoteAddFormModel extends AbstractFormModel
{

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
    public $createdBy = 0;

    /**
     * @var string
     */
    public $dataValue;

    /**
     * @var \backend\db\repositories\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var \backend\db\repositories\ClientRepositoryInterface
     */
    private $clientRepository;

    /**
     * @var \backend\db\repositories\LeadRepositoryInterface
     */
    private $leadRepository;

    /**
     * @var \backend\db\repositories\PlaceLeadRepositoryInterface
     */
    private $placeLeadRepository;

    /**
     * @var \backend\db\repositories\DbNoteRepository
     */
    private $dbNoteRepository;

    /**
     * @var \backend\db\repositories\UuidGenerator
     */
    private $uuidGenerator;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        LeadRepositoryInterface $leadRepository,
        PlaceLeadRepositoryInterface $placeLeadRepository,
        UserRepositoryInterface $userRepository,
        DbNoteRepository $dbNoteRepository,
        UuidGenerator $uuidGenerator
    ) {
        parent::__construct();

        $this->clientRepository = $clientRepository;
        $this->leadRepository = $leadRepository;
        $this->placeLeadRepository = $placeLeadRepository;
        $this->userRepository = $userRepository;
        $this->dbNoteRepository = $dbNoteRepository;
        $this->uuidGenerator = $uuidGenerator;

    }

    public function rules()
    {
        return [
            [['elementId'], 'required', 'message' => 'Incorrect element id.'],
            [
                ['elementId'],
                'string',
                'min' => 1,
                'max' => 128,
                'message' => 'Incorrect element id.',
                'tooShort' => 'Incorrect element id.',
                'tooLong' => 'Incorrect element id.',
            ],
            [['elementType'], 'required', 'message' => 'Incorrect element type.'],
            [
                ['elementType'],
                'integer',
                'min' => 1,
                'message' => 'Incorrect element type.',
                'tooSmall' => 'Incorrect element type.',
            ],
            [['noteType'], 'required', 'message' => 'Incorrect note type.'],
            [
                ['noteType'],
                'integer',
                'min' => 1,
                'message' => 'Incorrect note type.',
                'tooSmall' => 'Incorrect note type.',
            ],
            [['elementId'], 'checkElement'],
            [['createdBy'], 'validateUser'],
            [['dataValue'], 'required', 'message' => 'Incorrect data value.'],
            [
                ['dataValue'],
                'string',
                'min' => 1,
                'max' => 999,
                'message' => 'Incorrect data value.',
                'tooShort' => 'Incorrect data value.',
                'tooLong' => 'Incorrect data value, too long.',
            ],
        ];
    }

    private $user;

    public function validateUser($attribute)
    {

        if($this->createdBy){
            $this->user = $this->userRepository->findByID($this->createdBy);
            if (!$this->user) {
                $this->addError($attribute, 'User not found.');
            }
        }else{
            $this->user = \Yii::$app->user->identity;
        }
    }

    public function checkElement($attribute){

        switch ($this->elementType) {
            case Note::ELEMENT_TYPE_CLIENT:
                if (!$this->clientRepository->updateByID($this->elementId)) {
                    $this->addError($attribute, 'Client not found.');
                }
                break;
            case Note::ELEMENT_TYPE_LEAD:
                if (!$this->leadRepository->updateByID($this->elementId)) {
                    $this->addError($attribute, 'Lead not found.');
                }
                break;
            case Note::ELEMENT_TYPE_PLACE_LEAD:
                if (!$this->placeLeadRepository->updateByID($this->elementId)) {
                    $this->addError($attribute, 'Place lead not found.');
                }
                break;
        }
    }

    public function execute(): array
    {

        $nextId = $this->uuidGenerator->generate();

        $note = new Note;
        $note->id = $nextId;
        $note->elementId = (int)$this->elementId;
        $note->elementType = (int)$this->elementType;
        $note->noteType = (int)$this->noteType;
        $note->createdBy = $this->user->id.'-'.$this->user->secret;
        $note->dataValue = array('by' => $this->user->name, 'text' => $this->dataValue);

        if (!$this->dbNoteRepository->insert($note)) {
            throw new \ErrorException('Failed to insert note.');
        }

        $result = $note->publicBundle();

        return $result;
    }
}
