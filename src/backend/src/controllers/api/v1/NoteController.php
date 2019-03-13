<?php

namespace backend\controllers\api\v1;

use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use backend\db\models\Note;
use backend\db\repositories\db\DbNoteRepository;
use backend\forms\api\NoteAddFormModel;
use backend\services\JwtService\JwtBearerAuth;
use backend\exceptions\FormValidationException;
use yii\filters\AccessControl;

class NoteController extends BaseController
{
    public function getVerbs(): array
    {
        return [
            'comment' => ['POST', 'OPTIONS'],
            'list' => ['POST', 'OPTIONS'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => JwtBearerAuth::class,
            'only' => ['comment', 'list'],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['list'],
                    'allow' => true,
                    'roles' => ['1', '2'],
                ],
                [
                    'actions' => ['comment'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionComment()
    {
        sleep($this->getSleepSeconds());

        /** @var \backend\forms\api\NoteAddFormModel $Form */
        $Form = \Yii::$container->get(NoteAddFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        $Form->noteType = Note::NOTE_TYPE_COMMON;

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson($result);
    }

    public function actionList()
    {
        $options = array('sort' => array('createdAt'=> -1));
        $filter = $this->request->get('filter');
        if($skip = $this->request->get('skip')){
            $options['skip'] = $skip;
        }

        if($limit = $this->request->get('limit')){
            $options['limit'] = $limit;
        }

        $filter = empty($filter) ? null : Json::decode($filter);

        /** @var \backend\services\repositories\db\DbNoteRepository $noteService */
        $noteService = \Yii::$container->get(DbNoteRepository::class);

        $total = $noteService->countAllNotes($filter);

        $this->response->headers->add('X-Pagination-Total', $total);

        $result = array('count' => $total, 'result' => $noteService->findAll($filter, array(), $options));

        return $this->asJson($result);
    }

}
