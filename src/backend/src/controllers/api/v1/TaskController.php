<?php

namespace backend\controllers\api\v1;

use backend\db\models\Task;
use backend\db\repositories\TaskRepositoryInterface;
use backend\exceptions\FormValidationException;
use backend\services\JwtService\JwtBearerAuth;
use backend\services\TaskService\TaskService;
use backend\forms\api\TaskAddFormModel;
use backend\forms\api\TaskCloseFormModel;
use backend\db\repositories\db\DbNoteRepository;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Class TaskController
 * @package backend\controllers\v1
 *
 * @property \backend\db\repositories\TaskRepositoryInterface $taskRepository
 * @property \backend\db\repositories\db\DbNoteRepository $dbNoteRepository
 */
class TaskController extends BaseController
{
    public function getVerbs(): array
    {
        return [
            'list' => ['POST', 'OPTIONS'],
            'get' => ['POST', 'OPTIONS'],
            'delete' => ['POST', 'OPTIONS'],
            'add' => ['POST', 'OPTIONS'],
            'close' => ['POST', 'OPTIONS'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => JwtBearerAuth::class,
            'only' => ['add', 'close', 'list', 'get', 'delete'],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['add', 'close', 'list', 'get', 'delete'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionGet()
    {
        $id = $this->request->get('id');

        /** @var \backend\services\TaskService\TaskRepositoryInterface $taskService */
        $taskService = \Yii::$container->get(TaskRepositoryInterface::class);

        $task = $taskService->findByID($id);
        if(!$task) throw new HttpException(418, 'Task not found.');
        return $this->asJson($task->publicBundle());
    }

    public function actionList()
    {
        $filter = $this->request->get('filter');
        $sort = $this->request->get('sort');
        $offset = (int) $this->request->get('offset', 0);
        $limit = (int) $this->request->get('limit', 25);

        $filter = empty($filter) ? null : Json::decode($filter);
        $sort = empty($sort) ? null : Json::decode($sort);

        /** @var \backend\services\TaskService\TaskService $taskService */
        $taskService = \Yii::$container->get(TaskService::class);

        $total = $taskService->countAllTasks($filter);

        $this->response->headers->add('X-Pagination-Total', $total);

        return $this->asJson($taskService->getAllTasks($limit, $offset, $filter, $sort));
    }

    public function actionDelete()
    {
        $id = $this->request->get('id');
        $createdBy = $this->request->get('createdBy');

        /** @var \backend\services\TaskService\TaskRepositoryInterface $taskService */
        $taskService = \Yii::$container->get(TaskRepositoryInterface::class);

        $task = $taskService->findByID($id);

        if(!$task) throw new HttpException(418, 'Task not found.');

        if (($task->responsible.'-'.$task->responsibleSecret || $task->createdBy.'-'.$task->createdBySecret) != $createdBy) {
            throw new HttpException(418, 'Only responsible or author can delete task.');
        }

        /** @var \backend\services\repositories\db\DbNoteRepository $noteService */
        $noteService = \Yii::$container->get(DbNoteRepository::class);

        $query = array(
            'elementId' => (int)$task->elementId,
            'elementType' => (int)$task->elementType,
            'createdAt' => strtotime($task->createdAt->format('Y-m-d H:i:s'))
        );

        $noteService->deleteAll($query);

        return $this->asJson($taskService->delete($task));
    }

    public function actionAdd()
    {
        sleep($this->getSleepSeconds());

        /** @var \backend\forms\api\TaskAddFormModel $Form */
        $Form = \Yii::$container->get(TaskAddFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson(['result' => $result]);
    }

    public function actionClose()
    {

        sleep($this->getSleepSeconds());

        /** @var TaskCloseFormModel $Form */
        $Form = \Yii::$container->get(TaskCloseFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson(['result' => $result]);
    }

    /**
     * @return \backend\db\repositories\TaskRepositoryInterface
     */
    private function getTaskRepository()
    {
        return \Yii::$container->get(TaskRepositoryInterface::class);
    }

}
