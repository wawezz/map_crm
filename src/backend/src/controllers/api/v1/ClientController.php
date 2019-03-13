<?php

namespace backend\controllers\api\v1;

use backend\db\models\Client;
use backend\db\repositories\ClientRepositoryInterface;
use backend\db\models\Note;
use backend\db\repositories\db\DbNoteRepository;
use backend\exceptions\FormValidationException;
use backend\forms\api\ClientAddFormModel;
use backend\forms\api\ClientUpdateFormModel;
use backend\services\JwtService\JwtBearerAuth;
use backend\services\ClientService\ClientService;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ClientController
 * @package backend\controllers\v1
 *
 * @property \backend\db\repositories\ClientRepositoryInterface $clientRepository
 * @property \backend\db\repositories\db\DbNoteRepository $dbNoteRepository
 */
class ClientController extends BaseController
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

        /** @var \backend\services\ClientService\ClientRepositoryInterface $clientService */
        $clientService = \Yii::$container->get(ClientRepositoryInterface::class);

        $result = $clientService->findByID($id);
        if(!$result) throw new HttpException(418, 'Client not found.');
        $client = $result->publicBundle();
        /** @var \backend\services\repositories\db\DbNoteRepository $noteService */
        $noteService = \Yii::$container->get(DbNoteRepository::class);

        $query = array(
            'elementId' => (int)$client['id'],
            'elementType' => Note::ELEMENT_TYPE_CLIENT
        );
        $client['notes'] = $noteService->findAll($query);

        return $this->asJson($client);
    }

    public function actionRemove()
    {
        $clients = json_decode($this->request->post('clients'));

        /** @var \backend\services\ClientService\ClientRepositoryInterface $clientService */
        $clientService = \Yii::$container->get(ClientRepositoryInterface::class);

        if (!$clientService->remove($clients)) {
            throw new HttpException(418, 'Error deleting client from DB.');
        }

        /** @var \backend\services\repositories\db\DbNoteRepository $noteService */
        $noteService = \Yii::$container->get(DbNoteRepository::class);

        $elements = array();

        for( $i =0; $i < count( $clients ); $i++ ){ $elements[] = (int)$clients[$i];}

        $query = array(
            'elementId' => array('$in' => $elements),
            'elementType' => Note::ELEMENT_TYPE_CLIENT
        );

        $noteService->deleteAll($query);
        // if (!$noteService->deleteAll($query)) {
        //     throw new HttpException(418, 'Error deleting client notes from DB.');
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

        /** @var \backend\services\ClientService\ClientService $clientService */
        $clientService = \Yii::$container->get(ClientService::class);

        $total = $clientService->countAllClients($filter);

        $this->response->headers->add('X-Pagination-Total', $total);

        return $this->asJson($clientService->getAllClients($limit, $offset, $filter, $sort));
    }

    public function actionAdd()
    {
        sleep($this->getSleepSeconds());

        /** @var \backend\forms\api\ClientAddFormModel $Form */
        $Form = \Yii::$container->get(ClientAddFormModel::class);

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

        /** @var ClientUpdateFormModel $Form */
        $Form = \Yii::$container->get(ClientUpdateFormModel::class);

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
     * @return \backend\db\repositories\ClientRepositoryInterface
     */
    private function getClientRepository()
    {
        return \Yii::$container->get(ClientRepositoryInterface::class);
    }

}
