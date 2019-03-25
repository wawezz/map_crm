<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\Lead;
use backend\db\models\Note;
use backend\db\repositories\db\DbNoteRepository;
use backend\db\repositories\ClientRepositoryInterface;
use backend\db\repositories\LeadRepositoryInterface;
use backend\db\repositories\UserRepositoryInterface;
use backend\db\common\generator\UuidGenerator;

/**
 * Class LeadUpdateFormModel
 * @package backend\forms\api
 */
class LeadUpdateFormModel extends AbstractFormModel
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
     * @var int
     */
    public $responsible;

    /**
     * @var int
     */
    public $client;

    /**
     * @var int
     */
    public $status;

    /**
     * @var int
     */
    public $budget;

    /**
     * @var int
     */
    public $updatedBy = 0;
    
    /**
     * @var \DateTimeImmutable
     */
    public $firstCallAt;

    /**
     * @var \DateTimeImmutable
     */
    public $completedAt;

    /**
     * @var int
     */
    public $countryId;

    /**
     * @var int
     */
    public $currency;

    /**
     * @var int
     */
    public $product;

    /**
     * @var int
     */
    public $productCount;

    /**
     * @var int
     */
    public $productPrice;

    /**
     * @var int
     */
    public $shippingPrice;

    /**
     * @var bool
     */
    public $postOrder;

    /**
     * @var string
     */
    public $rejectionReason;

    /**
     * @var \backend\db\repositories\LeadRepositoryInterface
     */
    private $leadRepository;

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
        LeadRepositoryInterface $leadRepository,
        ClientRepositoryInterface $clientRepository,
        DbNoteRepository $dbNoteRepository,
        UuidGenerator $uuidGenerator,
        UserRepositoryInterface $userRepository
    ) {
        parent::__construct();

        $this->leadRepository = $leadRepository;
        $this->clientRepository = $clientRepository;
        $this->dbNoteRepository = $dbNoteRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->userRepository = $userRepository;

    }

    public function rules()
    {
        return [
            [['id'], 'required', 'message' => 'Incorrect ID.'],
            [['id'], 'validateLead'],
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
            [['responsible'], 'required', 'message' => 'Incorrect responsible.'],
            [
                ['responsible'],
                'string',
                'min' => 49,
                'max' => 49,
                'message' => 'Incorrect responsible id.',
                'tooShort' => 'Incorrect responsible id.',
                'tooLong' => 'Incorrect responsible id.',
            ],
            [['responsible'], 'validateUserResponsible'],
            [['client'], 'required', 'message' => 'Incorrect client.'],
            [
                ['client'],
                'integer',
                'min' => 1,
                'message' => 'Incorrect client id.',
                'tooSmall' => 'Incorrect client id.',
            ],
            [['client'], 'validateClient'],
            [['countryId'], 'required', 'message' => 'Incorrect country id.'],
            [
                ['countryId'],
                'integer',
                'min' => 1,
                'message' => 'Incorrect country id.',
                'tooSmall' => 'Incorrect country id.',
            ],
            [['currency'], 'required', 'message' => 'Incorrect currency id.'],
            [
                ['currency'],
                'integer',
                'min' => 1,
                'message' => 'Incorrect currency id.',
                'tooSmall' => 'Incorrect currency id.',
            ],
            [['updatedBy'], 'validateUser'],
            [['postOrder'], 'boolean'],
            [['completedAt', 'firstCallAt'], 'date', 'format' => 'yyyy-M-d H:m:s'],
            [['rejectionReason'], 'string'],
            [['budget', 'product', 'productCount', 'productPrice', 'shippingPrice'], 'integer'],
            [['name'], 'trim'],
        ];
    }

    private $user;
    private $userResponsible;
    private $lead;

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

    public function validateLead($attribute)
    {
        $this->lead = $this->leadRepository->findByID($this->id);
        if (!$this->lead) {
            $this->addError($attribute, 'Lead not found.');
        }
    }

    public function validateUserResponsible($attribute)
    {

        $this->userResponsible = $this->userRepository->findByID($this->responsible);
        if (!$this->userResponsible) {
            $this->addError($attribute, 'User responsible not found.');
        }
    }

    public function validateClient($attribute)
    {

        $client = $this->clientRepository->findByID($this->client);
        if (!$client) {
            $this->addError($attribute, 'Client not found.');
        }
    }

    public function execute(): array
    {
        $lead = $this->lead;
        $changes = array();
        $responsible = $this->responsible;

        if ($this->responsible) {
            if (preg_match('/-([a-f0-9]+)$/i', $this->responsible, $m)) {
                $secret = $m[1];
                $this->responsible = str_replace("-$secret", '', $this->responsible);
            }
        }

        if ($this->name && $lead->name != $this->name) {
            $changes['name'] = array('from' => $lead->name, 'to' => $this->name );
            $lead->name = $this->name;
        }

        if ($this->responsible && $lead->responsible != $this->responsible) {
            $changes['responsible'] = array('from' => $lead->responsible.'-'.$lead->responsibleSecret, 'to' => $responsible );
            $lead->responsible = $this->responsible;
        }

        if ($this->client && $lead->client != $this->client) {
            $changes['client'] = array('from' => $lead->client, 'to' => $this->client );
            $lead->client = $this->client;
        }

        if ($this->status && $lead->status != $this->status) {
            $changes['status'] = array('from' => $lead->status, 'to' => $this->status );
            $lead->status = $this->status;
        }

        if ($this->countryId && $lead->countryId != $this->countryId) {
            $changes['countryId'] = array('from' => $lead->countryId, 'to' => $this->countryId );
            $lead->countryId = $this->countryId;
        }

        if ($this->currency && $lead->currency != $this->currency) {
            $changes['currency'] = array('from' => $lead->currency, 'to' => $this->currency );
            $lead->currency = $this->currency;
        }

        if ($lead->postOrder !== $this->postOrder) {
            $changes['postOrder'] = array('from' => $lead->postOrder, 'to' => $this->postOrder );
            $lead->postOrder = $this->postOrder;
        }

        if ($lead->rejectionReason != $this->rejectionReason) {
            $changes['rejectionReason'] = array('from' => $lead->rejectionReason, 'to' => $this->rejectionReason );
            $lead->rejectionReason = $this->rejectionReason;
        }

        if ($lead->budget != $this->budget) {
            $changes['budget'] = array('from' => $lead->budget, 'to' => $this->budget );
            $lead->budget = $this->budget?$this->budget:null;
        }

        if ($lead->product != $this->product) {
            $changes['product'] = array('from' => $lead->product, 'to' => $this->product );
            $lead->product = $this->product;
        }

        if ($lead->productCount != $this->productCount) {
            $changes['productCount'] = array('from' => $lead->productCount, 'to' => $this->productCount );
            $lead->productCount = $this->productCount;
        }

        if ($lead->productPrice != $this->productPrice) {
            $changes['productPrice'] = array('from' => $lead->productPrice, 'to' => $this->productPrice );
            $lead->productPrice = $this->productPrice?$this->productPrice:null;
        }

        if ($lead->shippingPrice != $this->shippingPrice) {
            $changes['shippingPrice'] = array('from' => $lead->shippingPrice, 'to' => $this->shippingPrice );
            $lead->shippingPrice = $this->shippingPrice?$this->shippingPrice:null;
        }

        if ($this->completedAt && $lead->completedAt->format('Y-m-d H:i:s') != $this->completedAt) {
            $changes['completedAt'] = array('from' => $lead->completedAt->format('Y-m-d H:i:s'), 'to' => $this->completedAt );
            $lead->completedAt = $this->completedAt;
        }

        if ($this->firstCallAt && $lead->firstCallAt->format('Y-m-d H:i:s') != $this->firstCallAt) {
            $changes['firstCallAt'] = array('from' => $lead->firstCallAt->format('Y-m-d H:i:s'), 'to' => $this->firstCallAt );
            $lead->firstCallAt = $this->firstCallAt;
        }

        $notes = array();

        foreach($changes as $key => $value){
            $nextId = $this->uuidGenerator->generate();
            $note = new Note;
            $note->id = $nextId;
            $note->elementId = (int)$lead->id;
            $note->elementType = Note::ELEMENT_TYPE_LEAD;
            $note->noteType = ($key == 'status')?Note::NOTE_TYPE_LEAD_STATUS_CHANGED:Note::NOTE_TYPE_LEAD_FIELD_UPDATE;
            $note->createdBy = $this->user->id.'-'.$this->user->secret;
            $note->dataValue = array(
                'by' => $this->user->name,
                'name' => $lead->name,
                'data' => array('field' => $key, 'values' => $value)
            );

            $notes[] = $note->publicBundle();

        }
        
        if (empty($changes)) {
            throw new \ErrorException('No changes detected.', 418);
        }

        if (!$this->leadRepository->update($lead)) {
            throw new \ErrorException('Failed to update lead.');
        }

        $result = $lead->publicBundle();

        if (!$this->dbNoteRepository->insertMass($notes)) {
            throw new \ErrorException('Failed to insert note.');
        }else{
            $result['notes'] = $notes;
        }

        return $result;
    }
}
