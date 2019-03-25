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
 * Class LeadAddFormModel
 * @package backend\forms\api
 */
class LeadAddFormModel extends AbstractFormModel
{

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
    public $createdBy = 0;

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
            [['createdBy'], 'validateUser'],
            [['client'], 'required', 'message' => 'Incorrect client.'],
            [
                ['client'],
                'integer',
                'min' => 1,
                'message' => 'Incorrect client id.',
                'tooSmall' => 'Incorrect client id.',
            ],
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
            [['client'], 'validateClient'],
            [['postOrder'], 'boolean'],
            [['completedAt', 'firstCallAt'], 'date', 'format' => 'yyyy-M-d H:m:s'],
            [['rejectionReason'], 'string'],
            [['budget', 'product', 'productCount', 'productPrice', 'shippingPrice'], 'integer'],
            [['name'], 'trim'],
        ];
    }

    private $user;
    private $userResponsible;

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

        if (null !== $this->firstCallAt) {
            $this->firstCallAt = new \DateTimeImmutable($this->firstCallAt);
        }

        if (null !== $this->completedAt) {
            $this->completedAt = new \DateTimeImmutable($this->completedAt);
        }

        $lead = new Lead;

        $lead->name = $this->name;
        $lead->responsible = $this->userResponsible->id;
        $lead->createdBy = $this->user->id;
        $lead->client = $this->client;
        $lead->status = $this->status;
        $lead->budget = $this->budget;
        $lead->orderId = strtotime("now");
        $lead->firstCallAt = $this->firstCallAt;
        $lead->countryId = $this->countryId;
        $lead->currency = $this->currency;
        $lead->product = $this->product;
        $lead->productCount = $this->productCount;
        $lead->productPrice = $this->productPrice;
        $lead->shippingPrice = $this->shippingPrice;
        $lead->postOrder = $this->postOrder;
        $lead->completedAt = $this->completedAt;
        $lead->rejectionReason = $this->rejectionReason;
        $lead->id = $this->leadRepository->insert($lead);

        if (!$lead->id) {
            throw new \ErrorException('Failed to insert lead.');
        }

        $result = $lead->publicBundle();

        $nextId = $this->uuidGenerator->generate();
        $note = new Note;
        $note->id = $nextId;
        $note->elementId = $lead->id;
        $note->elementType = Note::ELEMENT_TYPE_LEAD;
        $note->noteType = Note::NOTE_TYPE_LEAD_CREATED;
        $note->createdBy = $this->user->id;
        $note->dataValue = array(
            'by' => $this->user->name,
            'name' => $lead->name,
            'data' => $result
        );

        if (!$this->dbNoteRepository->insert($note)) {
            throw new \ErrorException('Failed to insert note.');
        }

        return $result;
    }
}
