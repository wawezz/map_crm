<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\Client;
use backend\db\models\Note;
use backend\db\repositories\db\DbNoteRepository;
use backend\db\repositories\ClientRepositoryInterface;
use backend\db\repositories\UserRepositoryInterface;
use backend\db\common\generator\UuidGenerator;

/**
 * Class ClientAddFormModel
 * @package backend\forms\api
 */
class ClientAddFormModel extends AbstractFormModel
{

    /**
     * @var string
     */
    public $email;
    
    /**
     * @var bool
     */
    public $emailVerified;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $surname;

    /**
     * @var string
     */
    public $phone;
    
    /**
     * @var string
     */
    public $otherPhone;

    /**
     * @var bool
     */
    public $phoneVerified;

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
    public $createdBy = 0;

    /**
     * @var int
     */
    public $responsible;

    /**
     * @var \backend\db\repositories\ClientRepositoryInterface
     */
    private $clientRepository;

    /**
     * @var \backend\db\repositories\UserRepositoryInterface
     */
    private $userRepository;

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
        UserRepositoryInterface $userRepository,
        DbNoteRepository $dbNoteRepository,
        UuidGenerator $uuidGenerator
    ) {
        parent::__construct();

        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->dbNoteRepository = $dbNoteRepository;
        $this->uuidGenerator = $uuidGenerator;

    }

    public function rules()
    {
        return [
            [
                ['email'],
                'string',
                'min' => 6,
                'max' => 128,
                'message' => 'Incorrect email.',
                'tooShort' => 'Incorrect email.',
                'tooLong' => 'Incorrect email.',
            ],
            [['email'], 'email', 'checkDNS' => true, 'message' => 'Incorrect email.'],
            [['phone'], 'required', 'message' => 'Incorrect phone.'],
            [
                ['phone'],
                'string',
                'min' => 1,
                'max' => 128,
                'message' => 'Incorrect phone.',
                'tooShort' => 'Incorrect phone.',
                'tooLong' => 'Incorrect phone.',
            ],
            [['phone'], 'checkPhone'],
            [['phone'], 'validateClient'],
            [
                ['otherPhone'],
                'string',
                'max' => 128,
                'message' => 'Incorrect additional phone.',
                'tooLong' => 'Incorrect additional phone.',
            ],
            [['otherPhone'], 'checkPhone'],
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
            [['countryId'], 'required', 'message' => 'Incorrect country id.'],
            [
                ['countryId'],
                'integer',
                'min' => 1,
                'message' => 'Incorrect country id.',
                'tooSmall' => 'Incorrect country id.',
            ],
            [['createdBy'], 'validateUser'],
            [
                ['responsible'],
                'string',
                'min' => 49,
                'max' => 49,
                'message' => 'Incorrect responsible id.',
                'tooShort' => 'Incorrect responsible id.',
                'tooLong' => 'Incorrect responsible id.',
            ],
            [['emailVerified', 'phoneVerified'], 'boolean'],
            [['surname', 'zip', 'flat', 'building', 'street', 'city', 'state', 'skype'], 'string'],
            [['email', 'phone', 'surname', 'zip', 'flat', 'building', 'street', 'city', 'state', 'skype', 'otherPhone'], 'trim'],
        ];
    }

    private $user;
    private $phoneID;

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

    public function checkPhone($attribute){

        $toClear = [" ", "(", ")", "-"];
        $clear = ["", "", "", ""];

        $this[$attribute] = str_replace($toClear, $clear, trim($this[$attribute]));

        $toInt = str_replace("+", "", $this[$attribute]);

        if (!is_numeric($toInt)) {
            $this->addError($attribute, 'Wrong '.$this[$attribute].' number.');
        }

        if($attribute == 'phone'){
            $this->phoneID = $toInt;
        }
    }

    public function validateClient($attribute)
    {

        if ($this->clientRepository->findByPhone($this->phoneID)) {
            $this->addError($attribute, 'Client already registered.');
        }
    }

    public function execute(): array
    {
        if ($this->responsible) {
            if (preg_match('/-([a-f0-9]+)$/i', $this->responsible, $m)) {
                $secret = $m[1];
                $this->responsible = str_replace("-$secret", '', $this->responsible);
            }
        } else {
            $this->responsible = $this->user->id;
        }

        $client = new Client;
        $client->email = $this->email;
        $client->emailVerified = $this->emailVerified;
        $client->name = $this->name;
        $client->surname = $this->surname;
        $client->phone = $this->phoneID;
        $client->phoneVerified = $this->phoneVerified;
        $client->workPhone = $this->phone;
        $client->otherPhone = $this->otherPhone;
        $client->countryId = $this->countryId;
        $client->state = $this->state;
        $client->city = $this->city;
        $client->street = $this->street;
        $client->building = $this->building;
        $client->flat = $this->flat;
        $client->zip = $this->zip;
        $client->skype = $this->skype;
        $client->createdBy = $this->user->id;
        $client->responsible = $this->responsible;
        $client->id = $this->clientRepository->insert($client);

        if (!$client->id) {
            throw new \ErrorException('Failed to insert client.');
        }

        $result = $client->publicBundle();

        $nextId = $this->uuidGenerator->generate();
        $note = new Note;
        $note->id = $nextId;
        $note->elementId = $client->id;
        $note->elementType = Note::ELEMENT_TYPE_CLIENT;
        $note->noteType = Note::NOTE_TYPE_CLIENT_CREATED;
        $note->createdBy = $this->user->id;
        $note->dataValue = array(
            'by' => $this->user->name,
            'name' => $client->name,
            'data' => $result
        );

        if (!$this->dbNoteRepository->insert($note)) {
            throw new \ErrorException('Failed to insert note.');
        }

        return $result;
    }
}
