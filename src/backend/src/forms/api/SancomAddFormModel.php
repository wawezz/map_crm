<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\Client;
use backend\db\models\Note;
use backend\db\models\User;
use backend\db\repositories\db\DbNoteRepository;
use backend\db\repositories\ClientRepositoryInterface;
use backend\db\repositories\UserRepositoryInterface;
use backend\db\common\generator\UuidGenerator;
use yii\helpers\Json;

/**
 * Class SancomAddFormModel
 * @package backend\forms\api
 */
class SancomAddFormModel extends AbstractFormModel
{

    /**
     * @var int
     */
    public $clid;
    
    /**
     * @var string
     */
    public $date;

    /**
     * @var time
     */
    public $time;

    /**
     * @var int
     */
    public $src;

    /**
     * @var int
     */
    public $dst;

    /**
     * @var int
     */
    public $duration;

    /**
     * @var string
     */
    public $hangup_cause;

    /**
     * @var int
     */
    public $device_id;

    /**
     * @var int
     */
    public $uniqueid;

    /**
     * @var int
     */
    public $hangupcause_code;

    /**
     * @var int
     */
    public $callback_id;

    /**
     * @var string
     */
    public $call_rate;

    /**
     * @var string
     */
    public $call_billsec;

    /**
     * @var string
     */
    public $call_price;

    /**
     * @var string
     */
    public $call_type;

    /**
     * @var int
     */
    public $noteType;

    /**
     * @var \backend\db\repositories\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var \backend\db\repositories\ClientRepositoryInterface
     */
    private $clientRepository;

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
            [['src'], 'required', 'message' => 'Incorrect src.'],
            [
                ['src'],
                'string',
                'min' => 1,
                'max' => 128,
                'message' => 'Incorrect src.',
                'tooShort' => 'Incorrect src.',
                'tooLong' => 'Incorrect src.',
            ],
            [['src'], 'validateUser'], 
            [['date'], 'required', 'message' => 'Incorrect date.'],
            [['date'], 'date', 'format' => 'yyyy-M-d'],
            [['time'], 'required', 'message' => 'Incorrect time.'],
            [['time'], 'date', 'format' => 'H:m:s'],
            [['clid'], 'required', 'message' => 'Incorrect clid.'],
            [['dst'], 'required', 'message' => 'Incorrect dst.'],
            [['call_type'], 'required', 'message' => 'Incorrect call type.'],
            [['call_type'], 'setCallType'],
            [['duration', 'hangup_cause', 'device_id', 'uniqueid', 'hangupcause_code', 'callback_id', 'call_rate', 'call_billsec', 'call_price'], 'trim'],
        ];
    }

    private $user;
    private $client;

    public function validateUser($attribute)
    {

        if($this->src && $this->call_type == 'outgoing'){
            $this->user = $this->userRepository->findBySipID($this->src);
            if (!$this->user) {
                $this->addError($attribute, 'User not found.');
            }
        }else{
            $this->user = \Yii::$app->user->identity;
        }
    }

    public function setCallType($attribute)
    {
        if($this->call_type == 'incoming'){
            $this->client = $this->clientRepository->findPhoneLike($this->clid);
            $this->noteType = Note::NOTE_TYPE_CALL_IN;
        }elseif($this->call_type == 'outgoing'){
            $this->client = $this->clientRepository->findPhoneLike($this->dst);
            $this->noteType = Note::NOTE_TYPE_CALL_OUT;
        }else{
            $this->addError($attribute, 'Undefined call type.');            
        }
    }

    public function execute(): array
    {

        $data = array(
            "date" => $this->date,
            "time" => $this->time,
            "clid" => $this->clid,
            "src" => $this->src,
            "dst" => $this->dst,
            "duration" => $this->duration,
            "call_type" => $this->call_type,
            "hangup_cause" => $this->hangup_cause,
            "device_id" => $this->device_id,
            "uniqueid" => $this->uniqueid,
            "hangupcause_code" => $this->hangupcause_code,
            "callback_id" => $this->callback_id,
            "call_rate" => $this->call_rate,
            "call_billsec" => $this->call_billsec,
            "call_price" => $this->call_price
        );

        $nextId = $this->uuidGenerator->generate();

        $note = new Note;
        $note->id = $nextId;
        $note->elementId = $this->client?(int)$this->client->id:0;
        $note->elementType = (int)Note::ELEMENT_TYPE_CLIENT;
        $note->noteType = (int)$this->noteType;
        $note->createdBy = $this->user->id.'-'.$this->user->secret;
        $note->createdAt = strtotime($this->date." ".$this->time);
        $note->dataValue = array('by' => $this->user->name, 'data' => $data);

        if (!$this->dbNoteRepository->insert($note)) {
            throw new \ErrorException('Failed to insert note.');
        }

        $result = $note->publicBundle();

        return $result;
    }
}
