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
 * Class PlaceLeadUpdateFormModel
 * @package backend\forms\api
 */
class PlaceLeadUpdateFormModel extends AbstractFormModel
{

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $placeId;

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
    public $zipCode;

    /**
     * @var string
     */
    public $city;

    /**
     * @var int
     */
    public $alexaRank;

    /**
     * @var string
     */
    public $onlineSince;

    /**
     * @var int
     */
    public $ypReviews;

    /**
     * @var int
     */
    public $multiLocation;

    /**
     * @var string
     */
    public $lastRemark;

    /**
     * @var int
     */
    public $bbbRating;

    /**
     * @var int
     */
    public $ypRating;

    /**
     * @var int
     */
    public $dataScore;

    /**
     * @var string
     */
    public $carrier;

    /**
     * @var string
     */
    public $callerIdName;

    /**
     * @var int
     */
    public $rn;

    /**
     * @var int
     */
    public $updatedBy = 0;

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
            [['id'], 'validatePlaceLead'],
            [['placeId'], 'required', 'message' => 'Incorrect place id.'],
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
            [
                ['phone'],
                'string',
                'max' => 128,
                'message' => 'Incorrect additional phone.',
                'tooLong' => 'Incorrect additional phone.',
            ],
            [['phone'], 'checkPhone'],
            [['updatedBy'], 'validateUser'],
            [['toSync', 'isImportant'], 'boolean'],
            [['contractAt', 'nextFollowupDate'], 'date', 'format' => 'yyyy-M-d H:m:s', 'skipOnEmpty' => true],
            [['address', 'review', 'website', 'geo', 'data', 'type', 'zipCode', 'city', 'onlineSince', 'lastRemark', 'carrier', 'callerIdName'], 'string'],
            [['price', 'campaignCode', 'alexaRank', 'ypReviews', 'multiLocation', 'bbbRating', 'ypRating', 'dataScore', 'rn'], 'integer'],
            [['placeId', 'name', 'address', 'phone', 'review', 'website', 'rating', 'zipCode', 'city', 'onlineSince', 'carrier', 'callerIdName'], 'trim'],
        ];
    }

    private $user;
    private $placeLead;

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

    public function validatePlaceLead($attribute)
    {
        $this->placeLead = $this->placeLeadRepository->findByID($this->id);
        if (!$this->placeLead) {
            $this->addError($attribute, 'Place lead not found.');
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
        $placeLead = $this->placeLead;
        $changes = array();

        if($this->geo == ''){
            $this->geo = 'POINT(0 0)';
        }

        if ($placeLead->placeId && $placeLead->placeId != $this->placeId) {
            $changes['placeId'] = array('from' => $placeLead->placeId, 'to' => $this->placeId );
            $placeLead->placeId = $this->placeId;
        }

        if ($placeLead->name && $placeLead->name != $this->name) {
            $changes['name'] = array('from' => $placeLead->name, 'to' => $this->name );
            $placeLead->name = $this->name;
        }

        if ($placeLead->address != $this->address) {
            $changes['address'] = array('from' => $placeLead->address, 'to' => $this->address );
            $placeLead->address = $this->address;
        }

        if ($placeLead->phone != $this->phone) {
            $changes['phone'] = array('from' => $placeLead->phone, 'to' => $this->phone );
            $placeLead->phone = $this->phone;
        }

        if ($placeLead->status &&  $placeLead->status != $this->status) {
            $changes['status'] = array('from' => $placeLead->status, 'to' => $this->status );
            $placeLead->status = $this->status;
        }

        if ($placeLead->type != $this->type) {
            $changes['type'] = array('from' => $placeLead->type, 'to' => $this->type );
            $placeLead->type = $this->type;
        }

        if ($placeLead->price != $this->price) {
            $changes['price'] = array('from' => $placeLead->price, 'to' => $this->price );
            $placeLead->price = $this->price;
        }

        if ($placeLead->rating != (float)$this->rating) {
            $changes['rating'] = array('from' => $placeLead->rating, 'to' => (float)$this->rating );
            $placeLead->rating = (float)$this->rating;
        }
        
        if ($placeLead->review != $this->review) {
            $changes['review'] = array('from' => $placeLead->review, 'to' => $this->review );
            $placeLead->review = $this->review;
        }

        if ($placeLead->website != $this->website) {
            $changes['website'] = array('from' => $placeLead->website, 'to' => $this->website );
            $placeLead->website = $this->website;
        }

        if ($placeLead->geo != $this->geo) {
            $changes['geo'] = array('from' => $placeLead->geo, 'to' => $this->geo );
            $placeLead->geo = $this->geo;
        }

        if ($placeLead->data != $this->data) {
            $changes['data'] = array('from' => $placeLead->data, 'to' => $this->data );
            $placeLead->data = $this->data;
        }

        if ($placeLead->toSync !== $this->toSync) {
            $changes['toSync'] = array('from' => $placeLead->toSync, 'to' => $this->toSync );
            $placeLead->toSync = $this->toSync;
        }

        if ($placeLead->isImportant !== $this->isImportant) {
            $changes['isImportant'] = array('from' => $placeLead->isImportant, 'to' => $this->isImportant );
            $placeLead->isImportant = $this->isImportant;
        }

        if ($placeLead->campaignCode != $this->campaignCode) {
            $changes['campaignCode'] = array('from' => $placeLead->campaignCode, 'to' => $this->campaignCode );
            $placeLead->campaignCode = $this->campaignCode;
        }

        if ($placeLead->zipCode != $this->zipCode) {
            $changes['zipCode'] = array('from' => $placeLead->zipCode, 'to' => $this->zipCode );
            $placeLead->zipCode = $this->zipCode;
        }

        if ($placeLead->city != $this->city) {
            $changes['city'] = array('from' => $placeLead->city, 'to' => $this->city );
            $placeLead->city = $this->city;
        }

        if ($placeLead->alexaRank != $this->alexaRank) {
            $changes['alexaRank'] = array('from' => $placeLead->alexaRank, 'to' => $this->alexaRank );
            $placeLead->alexaRank = $this->alexaRank;
        }

        if ($placeLead->onlineSince != $this->onlineSince) {
            $changes['onlineSince'] = array('from' => $placeLead->onlineSince, 'to' => $this->onlineSince );
            $placeLead->onlineSince = $this->onlineSince;
        }

        if ($placeLead->ypReviews != $this->ypReviews) {
            $changes['ypReviews'] = array('from' => $placeLead->ypReviews, 'to' => $this->ypReviews );
            $placeLead->ypReviews = $this->ypReviews;
        }

        if ($placeLead->multiLocation != $this->multiLocation) {
            $changes['multiLocation'] = array('from' => $placeLead->multiLocation, 'to' => $this->multiLocation );
            $placeLead->multiLocation = $this->multiLocation;
        }

        if ($placeLead->lastRemark != $this->lastRemark) {
            $changes['lastRemark'] = array('from' => $placeLead->lastRemark, 'to' => $this->lastRemark );
            $placeLead->lastRemark = $this->lastRemark;
        }

        if ($placeLead->bbbRating != $this->bbbRating) {
            $changes['bbbRating'] = array('from' => $placeLead->bbbRating, 'to' => $this->bbbRating );
            $placeLead->bbbRating = $this->bbbRating;
        }

        if ($placeLead->ypRating != $this->ypRating) {
            $changes['ypRating'] = array('from' => $placeLead->ypRating, 'to' => $this->ypRating );
            $placeLead->ypRating = $this->ypRating;
        }

        if ($placeLead->dataScore != $this->dataScore) {
            $changes['dataScore'] = array('from' => $placeLead->dataScore, 'to' => $this->dataScore );
            $placeLead->dataScore = $this->dataScore;
        }

        if ($placeLead->carrier != $this->carrier) {
            $changes['carrier'] = array('from' => $placeLead->carrier, 'to' => $this->carrier );
            $placeLead->carrier = $this->carrier;
        }

        if ($placeLead->callerIdName != $this->callerIdName) {
            $changes['callerIdName'] = array('from' => $placeLead->callerIdName, 'to' => $this->callerIdName );
            $placeLead->callerIdName = $this->callerIdName;
        }

        if ($placeLead->rn != $this->rn) {
            $changes['rn'] = array('from' => $placeLead->rn, 'to' => $this->rn );
            $placeLead->rn = $this->rn;
        }

        if ($this->contractAt && $placeLead->contractAt->format('Y-m-d H:i:s') != $this->contractAt) {
            $changes['contractAt'] = array('from' => $placeLead->contractAt->format('Y-m-d H:i:s'), 'to' => $this->contractAt );
            $placeLead->contractAt = $this->contractAt;
        }

        if (($placeLead->nextFollowupDate?$placeLead->nextFollowupDate->format('Y-m-d H:i:s'):$placeLead->nextFollowupDate) != $this->nextFollowupDate) {
            $changes['nextFollowupDate'] = array('from' => $placeLead->nextFollowupDate?$placeLead->nextFollowupDate->format('Y-m-d H:i:s'):$placeLead->nextFollowupDate, 'to' => $this->nextFollowupDate );
            $placeLead->nextFollowupDate = $this->nextFollowupDate;
        }

        $notes = array();

        foreach($changes as $key => $value){
            $nextId = $this->uuidGenerator->generate();
            $note = new Note;
            $note->id = $nextId;
            $note->elementId = (int)$placeLead->id;
            $note->elementType = Note::ELEMENT_TYPE_PLACE_LEAD;
            $note->noteType = ($key == 'status')?Note::NOTE_TYPE_PLACE_LEAD_STATUS_CHANGED:Note::NOTE_TYPE_PLACE_LEAD_FIELD_UPDATE;
            $note->createdBy = $this->user->id.'-'.$this->user->secret;
            $note->dataValue = array(
                'by' => $this->user->name,
                'name' => $placeLead->name,
                'data' => array('field' => $key, 'values' => $value)
            );

            $notes[] = $note->publicBundle();

        }
        
        if (empty($changes)) {
            throw new \ErrorException('No changes detected.', 418);
        }

        if (!$this->placeLeadRepository->update($placeLead)) {
            throw new \ErrorException('Failed to update place lead.');
        }

        $result = $placeLead->publicBundle();

        if (!$this->dbNoteRepository->insertMass($notes)) {
            throw new \ErrorException('Failed to insert note.');
        }else{
            $result['notes'] = $notes;
        }

        return $result;
    }
}
