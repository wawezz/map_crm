<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\Task;
use backend\db\models\Note;
use backend\db\repositories\db\DbNoteRepository;
use backend\db\repositories\TaskRepositoryInterface;
use backend\db\repositories\UserRepositoryInterface;
use backend\db\common\generator\UuidGenerator;

/**
 * Class TaskCloseFormModel
 * @package backend\forms\api
 */
class TaskCloseFormModel extends AbstractFormModel
{

    /**
     * @var int
     */
    public $id;
    
    /**
     * @var int
     */
    public $createdBy = 0;

     /**
     * @var string
     */
    public $result;

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
            [['id'], 'required', 'message' => 'Incorrect ID.'],
            [['id'], 'validateTask'],        
            [['createdBy'], 'validateUser'],
            [['result'], 'required', 'message' => 'Incorrect result.'],
            [
                ['result'],
                'string',
                'min' => 1,
                'max' => 999,
                'message' => 'Incorrect result.',
                'tooShort' => 'Incorrect result.',
                'tooLong' => 'Incorrect result, too long.',
            ],
        ];
    }

    private $user;
    private $task;
    public function validateTask($attribute)
    {
        $this->task = $this->taskRepository->findByID($this->id);
        if (!$this->task) {
            $this->addError($attribute, 'Task not found.');
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

        if(($this->task->responsible || $this->task->createdBy) != $this->user->id){
            $this->addError($attribute, 'Only responsible or author can edit task.');
        }
    }

    public function execute(): array
    {

        $task = $this->task;

        $nextId = $this->uuidGenerator->generate();
        $note = new Note;
        $note->id = $nextId;
        $note->elementId = (int)$task->elementId;
        $note->elementType = (int)$task->elementType;
        $note->noteType = Note::NOTE_TYPE_TASK_RESULT;
        $note->createdBy = $this->user->id.'-'.$this->user->secret;
        $note->dataValue = array(
            'by' => $this->user->name,
            'data' => array(
                'task' => $task->publicBundle(),
                'result' => $this->result
            )
        );

        if (!$this->dbNoteRepository->insert($note)) {
            throw new \ErrorException('Failed to insert note.');
        }

        if (!$this->taskRepository->delete($task)) {
            throw new \ErrorException('Failed to delete task.');
        }

        return $note->publicBundle();
    }
}
