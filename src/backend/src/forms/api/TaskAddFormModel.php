<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\Task;
use backend\db\models\Note;
use backend\db\repositories\db\DbNoteRepository;
use backend\db\repositories\TaskRepositoryInterface;
use backend\db\repositories\UserRepositoryInterface;
use backend\events\api\TaskAddEvent;
use backend\db\common\generator\UuidGenerator;
use yii\base\Event;

/**
 * Class TaskAddFormModel
 * @package backend\forms\api
 */
class TaskAddFormModel extends AbstractFormModel
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
    public $createdBy = 0;

     /**
     * @var string
     */
    public $comment;

    /**
     * @var \DateTimeImmutable
     */
    public $eventDate;

    /**
     * @var \backend\db\repositories\DbNoteRepository
     */
    private $dbNoteRepository;

    /**
     * @var \backend\db\repositories\UuidGenerator
     */
    private $uuidGenerator;

    /**
     * @var \backend\db\repositories\TaskRepositoryInterface
     */
    private $taskRepository;

    /**
     * @var \backend\db\repositories\UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(
        TaskRepositoryInterface $taskRepository,
        DbNoteRepository $dbNoteRepository,
        UuidGenerator $uuidGenerator,
        UserRepositoryInterface $userRepository
    ) {
        parent::__construct();

        $this->taskRepository = $taskRepository;
        $this->dbNoteRepository = $dbNoteRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->userRepository = $userRepository;

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
            [['type'], 'required', 'message' => 'Incorrect type.'],
            [
                ['type'],
                'string',
                'min' => 1,
                'max' => 128,
                'message' => 'Incorrect type.',
                'tooShort' => 'Incorrect type.',
                'tooLong' => 'Incorrect type.',
            ],          
            [['createdBy'], 'validateUser'],
            [['responsible'], 'required', 'message' => 'Incorrect responsible id.'],
            [
                ['responsible'],
                'string',
                'min' => 49,
                'max' => 49,
                'message' => 'Incorrect responsible id.',
                'tooShort' => 'Incorrect responsible id.',
                'tooLong' => 'Incorrect responsible id.',
            ],
            [['responsible'], 'validateResponsible'], 
            [['comment'], 'required', 'message' => 'Incorrect comment.'],
            [
                ['comment'],
                'string',
                'min' => 1,
                'max' => 999,
                'message' => 'Incorrect comment.',
                'tooShort' => 'Incorrect comment.',
                'tooLong' => 'Incorrect comment, too long.',
            ],
            [['eventDate'], 'date', 'format' => 'yyyy-M-d H:m:s'],
        ];
    }

    private $user;
    private $responsibleUser;

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

    public function validateResponsible($attribute)
    {

        if($this->responsible){
            $this->responsibleUser = $this->userRepository->findByID($this->responsible);
            if (!$this->responsibleUser) {
                $this->addError($attribute, 'Responsible not found.');
            }
        }else{
            $this->responsibleUser = $this->user;
        }
    }

    public function execute(): array
    {

        $task = new Task;
        $task->elementId = $this->elementId;
        $task->elementType = $this->elementType;
        $task->type = $this->type;
        $task->responsible = $this->responsibleUser->id;
        $task->createdBy = $this->user->id;
        $task->comment = $this->comment;
        $task->eventDate = $this->eventDate;
        $task->id = $this->taskRepository->insert($task);

        if (!$task->id) {
            throw new \ErrorException('Failed to insert task.');
        }

        $result = $task->publicBundle();

        $nextId = $this->uuidGenerator->generate();
        $note = new Note;
        $note->id = $nextId;
        $note->elementId = (int)$this->elementId;
        $note->elementType = (int)$this->elementType;
        $note->noteType = Note::NOTE_TYPE_TASK_CREATED;
        $note->createdBy = $this->user->id;
        $note->dataValue = array(
            'by' => $this->user->name,
            'data' => $result
        );

        if (!$this->dbNoteRepository->insert($note)) {
            throw new \ErrorException('Failed to insert note.');
        }

        $event = new TaskAddEvent;
        $event->task = $task;
        $event->user = $this->user;

        Event::trigger(Task::class, TaskAddEvent::class, $event);


        return $result;
    }
}
