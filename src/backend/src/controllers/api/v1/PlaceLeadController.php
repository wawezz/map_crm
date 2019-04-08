<?php

namespace backend\controllers\api\v1;

use backend\db\models\PlaceLead;
use backend\db\repositories\PlaceLeadRepositoryInterface;
use backend\exceptions\FormValidationException;
use backend\forms\api\PlaceLeadAddFormModel;
use backend\forms\api\PlaceLeadUpdateFormModel;
use backend\db\models\Note;
use backend\db\repositories\db\DbNoteRepository;
use backend\services\JwtService\JwtBearerAuth;
use backend\services\PlaceLeadService\PlaceLeadService;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Class PlaceLeadController
 * @package backend\controllers\v1
 *
 * @property \backend\db\repositories\PlaceLeadRepositoryInterface $placeLeadRepository
 * @property \backend\db\repositories\db\DbNoteRepository $dbNoteRepository
 */
class PlaceLeadController extends BaseController
{
    public function getVerbs(): array
    {
        return [
            'list' => ['POST', 'OPTIONS'],
            'get' => ['POST', 'OPTIONS'],
            'get_by_place_id' => ['POST', 'OPTIONS'],
            'remove' => ['POST', 'OPTIONS'],
            'massupdate' => ['POST', 'OPTIONS'],
            'add' => ['POST', 'OPTIONS'],
            'update' => ['POST', 'OPTIONS'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => JwtBearerAuth::class,
            'only' => ['add', 'get-by-place-id', 'update', 'massupdate', 'list', 'get', 'remove'],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['add', 'update', 'massupdate', 'list', 'get', 'get-by-place-id', 'remove'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionGetByPlaceId()
    {
        $placeId = $this->request->get('placeId');

        /** @var \backend\services\PlaceLeadService\PlaceLeadRepositoryInterface $placeLeadService */
        $placeLeadService = \Yii::$container->get(PlaceLeadRepositoryInterface::class);

        $result = $placeLeadService->findByPlaceId($placeId);
        if(!$result) throw new HttpException(418, 'Place lead not found.');
        $placeLead = $result->publicBundle();
        /** @var \backend\services\repositories\db\DbNoteRepository $noteService */
        $noteService = \Yii::$container->get(DbNoteRepository::class);

        $query = array(
            'elementId' => (int) $placeLead['id'],
            'elementType' => Note::ELEMENT_TYPE_PLACE_LEAD
        );
        $placeLead['notes'] = $noteService->findAll($query);

        return $this->asJson($placeLead);
    }

    public function actionGet()
    {
        $id = $this->request->get('id');

        /** @var \backend\services\PlaceLeadService\PlaceLeadRepositoryInterface $placeLeadService */
        $placeLeadService = \Yii::$container->get(PlaceLeadRepositoryInterface::class);

        $result = $placeLeadService->findByID($id);
        if(!$result) throw new HttpException(418, 'Place lead not found.');
        $placeLead = $result->publicBundle();
        /** @var \backend\services\repositories\db\DbNoteRepository $noteService */
        $noteService = \Yii::$container->get(DbNoteRepository::class);

        $query = array(
            'elementId' => (int) $placeLead['id'],
            'elementType' => Note::ELEMENT_TYPE_PLACE_LEAD
        );
        $placeLead['notes'] = $noteService->findAll($query);

        return $this->asJson($placeLead);
    }

    public function actionRemove()
    {
        $placeLeads = json_decode($this->request->post('placeLeads'));

        /** @var \backend\services\PlaceLeadService\PlaceLeadRepositoryInterface $placeLeadService */
        $placeLeadService = \Yii::$container->get(PlaceLeadRepositoryInterface::class);

        if (!$placeLeadService->remove($placeLeads)) {
            throw new HttpException(418, 'Error deleting place leads from DB.');
        }

        /** @var \backend\services\repositories\db\DbNoteRepository $noteService */
        $noteService = \Yii::$container->get(DbNoteRepository::class);

        $elements = array();

        for( $i =0; $i < count( $placeLeads ); $i++ ){ $elements[] = (int)$placeLeads[$i];}

        $query = array(
            'elementId' => array('$in' => $elements),
            'elementType' => Note::ELEMENT_TYPE_PLACE_LEAD
        );

        if (!$noteService->deleteAll($query)) {
            throw new HttpException(418, 'Error deleting place leads notes from DB.');
        }

        $result = array();
        $result['code'] = '0';
        $result['status'] = 'success';
        return $this->asJson(['result' => $result]);
    }

    public function actionList()
    {
        $query = $this->request->get('query');
        $filter = $this->request->get('filter');
        $sort = $this->request->get('sort');
        $offset = (int) $this->request->get('offset', 0);
        $limit = (int) $this->request->get('limit', 25);

        $filter = empty($filter) ? null : Json::decode($filter);
        $sort = empty($sort) ? null : Json::decode($sort);
        $query = !$query ? null : $query;

        /** @var \backend\services\PlaceLeadService\PlaceLeadService $placeLeadService */
        $placeLeadService = \Yii::$container->get(PlaceLeadService::class);

        $total = $placeLeadService->countAllPlaceLeads($filter, $query);

        $this->response->headers->add('X-Pagination-Total', $total);

        $response = $placeLeadService->getAllPlaceLeads($limit, $offset, $filter, $sort, $query);
        /** @var \backend\services\repositories\db\DbNoteRepository $noteService */
        $noteService = \Yii::$container->get(DbNoteRepository::class);

        foreach ($response as &$data) {
            $data = (array) $data;
            $query = array(
                'elementId' => (int) $data['id'],
                'elementType' => Note::ELEMENT_TYPE_PLACE_LEAD
            );
            $data['notesCount'] = $noteService->countAllNotes($query);
        }    

        return  $this->asJson($response);
    }

    public function actionAdd()
    {
        sleep($this->getSleepSeconds());

        /** @var \backend\forms\api\PlaceLeadAddFormModel $Form */
        $Form = \Yii::$container->get(PlaceLeadAddFormModel::class);

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

        /** @var PlaceLeadUpdateFormModel $Form */
        $Form = \Yii::$container->get(PlaceLeadUpdateFormModel::class);

        if (!$Form->load($this->request->post(), 'form')) {
            throw new HttpException(418, 'You must send a form object.');
        }

        if (!$Form->validate()) {
            throw new FormValidationException($Form);
        }

        $result = $Form->execute();

        return $this->asJson(['result' => $result]);
    }

    public function actionMassupdate()
    {
        sleep($this->getSleepSeconds());
        /** @var PlaceLeadUpdateFormModel $Form */
        $Form = \Yii::$container->get(PlaceLeadUpdateFormModel::class);

        $placeLeads = $this->request->post('forms');

        if(empty($placeLeads)){
            throw new HttpException(418, 'No place leads in forms array.');
        }

        $result = array();
        foreach ($placeLeads as $placeLead) {

            if (!$Form->load($placeLead, 'form')) {
                throw new HttpException(418, 'You must send a form object.');
            }
    
            if (!$Form->validate()) {
                throw new FormValidationException($Form);
            }
    
            $result[] = $Form->execute();
        }  

        return $this->asJson(['result' => $result]);
    }

    /**
     * @return \backend\db\repositories\PlaceLeadRepositoryInterface
     */
    private function getPlaceLeadRepository()
    {
        return \Yii::$container->get(PlaceLeadRepositoryInterface::class);
    }

}
