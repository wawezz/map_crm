<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\PlaceLead;
use backend\db\models\Note;
use backend\db\repositories\db\DbNoteRepository;
use backend\db\repositories\PlaceLeadRepositoryInterface;
use backend\db\repositories\UserRepositoryInterface;
use backend\db\common\generator\UuidGenerator;

/**
 * Class PlaceLeadAddFormModel
 * @package backend\forms\api
 */
class PlaceLeadAddFormModel extends AbstractFormModel
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
     * @var int
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
    public $createdBy = 0;

    /**
     * @var \DateTimeImmutable
     */
    public $contractAt;

    /**
     * @var \DateTimeImmutable
     */
    public $nextFollowupDate;

    /**
     * @var \backend\db\repositories\DbNoteRepository
     */
    private $dbNoteRepository;

    /**
     * @var \backend\db\repositories\PlaceLeadRepositoryInterface
     */
    private $placeLeadRepository;

    /**
     * @var \backend\db\repositories\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var \backend\db\repositories\UuidGenerator
     */
    private $uuidGenerator;

    public function __construct(
        PlaceLeadRepositoryInterface $placeLeadRepository,
        DbNoteRepository $dbNoteRepository,
        UuidGenerator $uuidGenerator,
        UserRepositoryInterface $userRepository
    ) {
        parent::__construct();

        $this->placeLeadRepository = $placeLeadRepository;
        $this->dbNoteRepository = $dbNoteRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->userRepository = $userRepository;

    }

    public function rules()
    {
        return [
            [['id'], 'required', 'message' => 'Incorrect ID.'],
            [['name'], 'required', 'message' => 'Incorrect name.'],
            [
                ['name'],
                'string',
                'min' => 3,
                'max' => 128,
                'message' => 'Incorrect name.',
                'tooShort' => 'Incorrect name.',
                'tooLong' => 'Incorrect name.',
            ],
            [['status'], 'required', 'message' => 'Incorrect status id.'],
            [
                ['status'],
                'integer',
                'min' => 1,
                'message' => 'Incorrect status id.',
                'tooSmall' => 'Incorrect status id.',
            ],
            [['type'], 'required', 'message' => 'Incorrect type id.'],
            [
                ['type'],
                'integer',
                'min' => 1,
                'message' => 'Incorrect type id.',
                'tooSmall' => 'Incorrect type id.',
            ],
            [
                ['phone'],
                'string',
                'max' => 128,
                'message' => 'Incorrect additional phone.',
                'tooLong' => 'Incorrect additional phone.',
            ],
            [['phone'], 'checkPhone'],
            [['createdBy'], 'validateUser'],
            [['toSync', 'isImportant'], 'boolean'],
            [['contractAt', 'nextFollowupDate'], 'date', 'format' => 'yyyy-M-d H:m:s'],
            [['address', 'review', 'website', 'geometry', 'geo', 'data'], 'string'],
            [['price', 'rating', 'campaignCode'], 'integer'],
            [['name', 'address', 'phone', 'review', 'website'], 'trim'],
        ];
    }

    private $user;

    public function checkPhone($attribute){

        $toClear = [" ", "(", ")", "-"];
        $clear = ["", "", "", ""];

        $this[$attribute] = str_replace($toClear, $clear, trim($this[$attribute]));

        $toInt = str_replace("+", "", $this[$attribute]);

        if (!is_numeric($toInt)) {
            $this->addError($attribute, 'Wrong '.$this[$attribute].' number.');
        }

    }

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

    public function execute(): array
    {

        if (null !== $this->contractAt) {
            $this->contractAt = new \DateTimeImmutable($this->contractAt);
        }

        if (null !== $this->nextFollowupDate) {
            $this->nextFollowupDate = new \DateTimeImmutable($this->nextFollowupDate);
        }

        $placeLead = new PlaceLead;

        $placeLead->id = $this->id;
        $placeLead->name = $this->name;
        $placeLead->address = $this->address;
        $placeLead->phone = $this->phone;
        $placeLead->type = $this->type;
        $placeLead->status = $this->status;
        $placeLead->price = $this->price;
        $placeLead->rating  = $this->rating;
        $placeLead->review  = $this->review;
        $placeLead->website  = $this->website;
        $placeLead->geometry  = $this->geometry;
        $placeLead->geo  = $this->geo;
        $placeLead->data  = $this->data;
        $placeLead->toSync  = $this->toSync;
        $placeLead->campaignCode  = $this->campaignCode;
        $placeLead->isImportant  = $this->isImportant;
        $placeLead->createdBy = $this->user->id;
        $placeLead->contractAt = $this->contractAt;
        $placeLead->nextFollowupDate = $this->nextFollowupDate;
        
        if (!$this->placeLeadRepository->insert($placeLead)) {
            throw new \ErrorException('Failed to insert place lead.');
        }

        $result = $placeLead->publicBundle();

        $nextId = $this->uuidGenerator->generate();
        $note = new Note;
        $note->id = $nextId;
        $note->elementId = $placeLead->id;
        $note->elementType = Note::ELEMENT_TYPE_PLACE_LEAD;
        $note->noteType = Note::NOTE_TYPE_PLACE_LEAD_CREATED;
        $note->createdBy = $this->user->id;
        $note->dataValue = array(
            'by' => $this->user->name,
            'name' => $placeLead->name,
            'data' => $result
        );

        if (!$this->dbNoteRepository->insert($note)) {
            throw new \ErrorException('Failed to insert note.');
        }

        return $result;
    }
}
