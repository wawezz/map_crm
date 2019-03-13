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
 * Class ClientUpdateFormModel
 * @package backend\forms\api
 */
class ClientUpdateFormModel extends AbstractFormModel
{

    /**
     * @var int
     */
    public $id;

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
    public $updatedBy = 0;

    /**
     * @var int
     */
    public $responsible;

    /**
     * @var array
     */
    public $toClear = [" ", "(", ")", "-"];

    /**
     * @var array
     */
    public $clear = ["", "", "", ""];

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
        UuidGenerator $uuidGenerator, 
        DbNoteRepository $dbNoteRepository
    )
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
        $this->dbNoteRepository = $dbNoteRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->userRepository = $userRepository;
    }

    public function rules()
    {
        return [
            [['id'], 'required', 'message' => 'Incorrect ID.'],
            [['id'], 'validateClient'],
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
            [['updatedBy'], 'validateUser'],
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
            [['surname', 'zip', 'flat', 'building', 'street', 'city', 'state', 'skype', 'otherPhone'], 'string'],
            [['otherPhone'], 'checkPhone'],
            [['email', 'phone', 'surname', 'zip', 'flat', 'building', 'street', 'city', 'state', 'skype', 'otherPhone'], 'trim'],
        ];
    }

    private $user;
    private $client;

    public function validateUser($attribute)
    {
        if($this->updatedBy){
            $this->user = $this->userRepository->findByID($this->updatedBy);
            if (!$this->user) {
                $this->addError($attribute, 'User not found.');
            }
        }else{
            $this->user = \Yii::$app->user->identity;
        }
    }

    public function validateClient($attribute)
    {

        $this->client = $this->clientRepository->findByID($this->id);
        if (!$this->client) {
            $this->addError($attribute, 'Client not found.');
        }
    }

    public function checkPhone($attribute)
    {

        $toClear = [" ", "(", ")", "-"];
        $clear = ["", "", "", ""];
        if ($this[$attribute]) {
            $this[$attribute] = str_replace($toClear, $clear, trim($this[$attribute]));

            $toInt = str_replace("+", "", $this[$attribute]);

            if (!is_numeric($toInt)) {
                $this->addError($attribute, 'Wrong ' . $this[$attribute] . ' number.');
            }
        }
    }

    public function execute(): array
    {

        $client = $this->client;
        $changes = [];

        $responsible = $this->responsible;
        if ($this->responsible) {
            if (preg_match('/-([a-f0-9]+)$/i', $this->responsible, $m)) {
                $secret = $m[1];
                $this->responsible = str_replace("-$secret", '', $this->responsible);
            }
        }

        if ($this->email && $client->email != $this->email) {
            $changes['email'] = array('from' => $client->email, 'to' => $this->email );
            $client->email = $this->email;
        }

        if ($this->name && $client->name != $this->name) {
            $changes['name'] = array('from' => $client->name, 'to' => $this->name );
            $client->name = $this->name;
        }

        if ($client->surname != $this->surname) {
            $changes['surname'] = array('from' => $client->surname, 'to' => $this->surname );
            $client->surname = $this->surname;
        }

        if ($client->emailVerified !== $this->emailVerified) {
            $changes['emailVerified'] = array('from' => $client->emailVerified, 'to' => $this->emailVerified );
            $client->emailVerified = $this->emailVerified;
        }

        if ($client->phoneVerified !== $this->phoneVerified) {
            $changes['phoneVerified'] = array('from' => $client->phoneVerified, 'to' => $this->phoneVerified );
            $client->phoneVerified = $this->phoneVerified;
        }

        if ($this->phone && $client->workPhone != $this->phone) {
            $changes['workPhone'] = array('from' => $client->workPhone, 'to' => $this->phone );
            $client->workPhone = $this->phone;
        }

        if ($client->otherPhone != $this->otherPhone) {
            $changes['otherPhone'] = array('from' => $client->otherPhone, 'to' => $this->otherPhone );
            $client->otherPhone = $this->otherPhone;
        }

        if ($this->countryId && $client->countryId != $this->countryId) {
            $changes['countryId'] = array('from' => $client->countryId, 'to' => $this->countryId );
            $client->countryId = $this->countryId;
        }

        if ($this->responsible && $client->responsible != $this->responsible) {
            $changes['responsible'] = array('from' => $client->responsible.'-'.$client->responsibleSecret, 'to' => $responsible );
            $client->responsible = $this->responsible;
        }

        if ($client->zip != $this->zip) {
            $changes['zip'] = array('from' => $client->zip, 'to' => $this->zip );
            $client->zip = $this->zip;
        }

        if ($client->flat != $this->flat) {
            $changes['flat'] = array('from' => $client->flat, 'to' => $this->flat );
            $client->flat = $this->flat;
        }

        if ($client->building != $this->building) {
            $changes['building'] = array('from' => $client->building, 'to' => $this->building );
            $client->building = $this->building;
        }

        if ($client->street != $this->street) {
            $changes['street'] = array('from' => $client->street, 'to' => $this->street );
            $client->street = $this->street;
        }

        if ($client->city != $this->city) {
            $changes['city'] = array('from' => $client->city, 'to' => $this->city );
            $client->city = $this->city;
        }

        if ($client->state != $this->state) {
            $changes['state'] = array('from' => $client->state, 'to' => $this->state );
            $client->state = $this->state;
        }

        if ($client->skype != $this->skype) {
            $changes['skype'] = array('from' => $client->skype, 'to' => $this->skype );
            $client->skype = $this->skype;
        }
        
        $notes = array();

        foreach($changes as $key => $value){
            $nextId = $this->uuidGenerator->generate();
            $note = new Note;
            $note->id = $nextId;
            $note->elementId = (int)$client->id;
            $note->elementType = Note::ELEMENT_TYPE_CLIENT;
            $note->noteType = Note::NOTE_TYPE_CLIENT_FIELD_UPDATE;
            $note->createdBy = $this->user->id.'-'.$this->user->secret;
            $note->dataValue = array(
                'by' => $this->user->name,
                'name' => $client->name,
                'data' => array('field' => $key, 'values' => $value)
            );

            $notes[] = $note->publicBundle();

        }
        
        if (empty($changes)) {
            throw new \ErrorException('No changes detected.', 418);
        }

        if (!$this->clientRepository->update($client)) {
            throw new \ErrorException('Failed to update client.');
        }

        $result = $client->publicBundle();

        if (!$this->dbNoteRepository->insertMass($notes)) {
            throw new \ErrorException('Failed to insert note.');
        }else{
            $result['notes'] = $notes;
        }

        return $result;
    }
}
