<?php

namespace backend\controllers\api\v1;

use backend\db\models\Lead;
use backend\db\repositories\LeadRepositoryInterface;
use backend\exceptions\FormValidationException;
use backend\forms\api\LeadAddFormModel;
use backend\forms\api\LeadUpdateFormModel;
use backend\db\models\Note;
use backend\db\repositories\db\DbNoteRepository;
use backend\services\JwtService\JwtBearerAuth;
use backend\services\LeadService\LeadService;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Class LeadController
 * @package backend\controllers\v1
 *
 * @property \backend\db\repositories\LeadRepositoryInterface $leadRepository
 * @property \backend\db\repositories\db\DbNoteRepository $dbNoteRepository
 */
class LeadController extends BaseController
{
    public function getVerbs(): array
    {
        return [
            'list' => ['POST', 'OPTIONS'],
            'get' => ['POST', 'OPTIONS'],
            'remove' => ['POST', 'OPTIONS'],
            'add' => ['POST', 'OPTIONS'],
            'update' => ['POST', 'OPTIONS'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => JwtBearerAuth::class,
            'only' => ['add', 'update', 'list', 'get', 'remove'],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['add', 'update', 'list', 'get', 'remove'],
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

        /** @var \backend\services\LeadService\LeadRepositoryInterface $leadService */
        $leadService = \Yii::$container->get(LeadRepositoryInterface::class);

        $result = $leadService->findByID($id);
        if(!$result) throw new HttpException(418, 'Lead not found.');
        $lead = $result->publicBundle();
        /** @var \backend\services\repositories\db\DbNoteRepository $noteService */
        $noteService = \Yii::$container->get(DbNoteRepository::class);

        $query = array(
            'elementId' => (int)$lead['id'],
            'elementType' => Note::ELEMENT_TYPE_LEAD
        );
        $lead['notes'] = $noteService->findAll($query);

        return $this->asJson($lead);
    }

    public function actionRemove()
    {
        $leads = json_decode($this->request->post('leads'));

        /** @var \backend\services\LeadService\LeadRepositoryInterface $leadService */
        $leadService = \Yii::$container->get(LeadRepositoryInterface::class);

        if (!$leadService->remove($leads)) {
            throw new HttpException(418, 'Error deleting leads from DB.');
        }

        /** @var \backend\services\repositories\db\DbNoteRepository $noteService */
        $noteService = \Yii::$container->get(DbNoteRepository::class);

        $elements = array();

        for( $i =0; $i < count( $leads ); $i++ ){ $elements[] = (int)$leads[$i];}

        $query = array(
            'elementId' => array('$in' => $elements),
            'elementType' => Note::ELEMENT_TYPE_LEAD
        );

        $noteService->deleteAll($query);
        // if (!$noteService->deleteAll($query)) {
        //     throw new HttpException(418, 'Error deleting leads notes from DB.');
        // }

        $result = array();
        $result['code'] = '0';
        $result['status'] = 'success';
        return $this->asJson(['result' => $result]);
    }

    public function actionList()
    {
        $filter = $this->request->get('filter');
        $sort = $this->request->get('sort');
        $offset = (int) $this->request->get('offset', 0);
        $limit = (int) $this->request->get('limit', 25);

        $filter = empty($filter) ? null : Json::decode($filter);
        $sort = empty($sort) ? null : Json::decode($sort);

        /** @var \backend\services\LeadService\LeadService $leadService */
        $leadService = \Yii::$container->get(LeadService::class);

        $total = $leadService->countAllLeads($filter);

        $this->response->headers->add('X-Pagination-Total', $total);

        return $this->asJson($leadService->getAllLeads($limit, $offset, $filter, $sort));
    }

    public function actionAdd()
    {
        sleep($this->getSleepSeconds());

        /** @var \backend\forms\api\LeadAddFormModel $Form */
        $Form = \Yii::$container->get(LeadAddFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson(['result' => $result]);
    }

    public function actionUpdate()
    {

        sleep($this->getSleepSeconds());

        /** @var LeadUpdateFormModel $Form */
        $Form = \Yii::$container->get(LeadUpdateFormModel::class);

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
     * @return \backend\db\repositories\LeadRepositoryInterface
     */
    private function getLeadRepository()
    {
        return \Yii::$container->get(LeadRepositoryInterface::class);
    }

}
