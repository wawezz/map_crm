<?php

namespace backend\controllers\api\v1;

use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use backend\services\ParamsService\ParamsService;
use yii\filters\AccessControl;

class ParamsController extends BaseController
{
    public function getVerbs(): array
    {
        return [
            'all' => ['GET'],
            'elements' => ['GET']
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['all', 'elements'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionAll()
    {

        $filter = $this->request->get('filter');

        $filter = empty($filter) ? array() : Json::decode($filter);

        /** @var \backend\services\ParamsService\ParamsService $paramsService */
        $paramsService = \Yii::$container->get(ParamsService::class);

        return $this->asJson($paramsService->getAllParams($filter));
    }

    public function actionElements()
    {
        $name = $this->request->get('name');

        /** @var \backend\services\ParamsService\ParamsService $paramsService */
        $paramsService = \Yii::$container->get(ParamsService::class);

        return $this->asJson($paramsService->getElements($name));
    }
}
