<?php

namespace backend\controllers\api\v1;

use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use backend\db\models\Note;
use backend\db\repositories\db\DbNoteRepository;
use backend\forms\api\NoteAddFormModel;
use backend\forms\api\SancomAddFormModel;
use backend\services\JwtService\JwtBearerAuth;
use backend\exceptions\FormValidationException;
use yii\filters\AccessControl;

class CallController extends BaseController
{
    private $baseTypes = array(
        Note::NOTE_TYPE_CALL_IN,
        Note::NOTE_TYPE_CALL_OUT
    );

    public function getVerbs(): array
    {
        return [
            'add' => ['POST', 'OPTIONS'],
            'sancom' => ['POST', 'OPTIONS'],
            'list' => ['POST', 'OPTIONS'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => JwtBearerAuth::class,
            'only' => ['add', 'list', 'sancom'],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['list'],
                    'allow' => true,
                    'roles' => ['1'],
                ],
                [
                    'actions' => ['sancom'],
                    'allow' => true,
                    'roles' => ['3'],
                ],
                [
                    'actions' => ['add'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionAdd()
    {
        sleep($this->getSleepSeconds());

        /** @var \backend\forms\api\NoteAddFormModel $Form */
        $Form = \Yii::$container->get(NoteAddFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        $this->checkNoteType($this->request->post()['form']['noteType']);

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson($result);
    }

    public function actionSancom()
    {
        sleep($this->getSleepSeconds());

        /** @var \backend\forms\api\SancomAddFormModel $Form */
        $Form = \Yii::$container->get(SancomAddFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        // $this->checkNoteType($this->request->post()['form']['noteType']);

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

        $baseFilter['noteType'] = array(
            '$in' => $this->baseTypes
        );

        $filter = empty($filter) ? array() : Json::decode($filter);

        if(!in_array('noteType', $filter)){
            $filter = array_merge($filter, $baseFilter);
        }else{
            if(!is_array($filter['noteType'])) throw new HttpException(418, 'Wrong noteType value in filter.');
            if(!in_array('$in', $filter['noteType'])) throw new HttpException(418, 'Wrong noteType value in filter.');
            foreach($filter['noteType']['$in'] as $type){
                $this->checkNoteType($type);
            }
        }

        /** @var \backend\services\repositories\db\DbNoteRepository $noteService */
        $noteService = \Yii::$container->get(DbNoteRepository::class);

        $total = $noteService->countAllNotes($filter);

        $this->response->headers->add('X-Pagination-Total', $total);

        $result = array('count' => $total, 'result' => $noteService->findAll($filter, array(), $options));

        return $this->asJson($result);
    }

    private function checkNoteType($type)
    {
        if(!in_array($type, $this->baseTypes)) throw new HttpException(418, 'Wrong noteType value in filter.');
    }

}
