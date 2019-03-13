<?php

namespace backend\controllers\api\v1;

use backend\db\models\Product;
use backend\db\repositories\ProductRepositoryInterface;
use backend\exceptions\FormValidationException;
use backend\services\JwtService\JwtBearerAuth;
use backend\services\ProductService\ProductService;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ProductController
 * @package backend\controllers\v1
 *
 * @property \backend\db\repositories\ProductRepositoryInterface $productRepository
 */
class ProductController extends BaseController
{
    public function getVerbs(): array
    {
        return [
            'list' => ['POST', 'OPTIONS'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => JwtBearerAuth::class,
            'only' => ['list'],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['list'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionList()
    {
        $filter = $this->request->get('filter');
        $sort = $this->request->get('sort');
        $offset = (int) $this->request->get('offset', 0);
        $limit = (int) $this->request->get('limit', 25);

        $filter = empty($filter) ? null : Json::decode($filter);
        $sort = empty($sort) ? null : Json::decode($sort);

        /** @var \backend\services\ProductService\ProductService $productService */
        $productService = \Yii::$container->get(ProductService::class);

        $total = $productService->countAllProducts($filter);

        $this->response->headers->add('X-Pagination-Total', $total);

        return $this->asJson($productService->getAllProducts($limit, $offset, $filter, $sort));
    }

    /**
     * @return \backend\db\repositories\ProductRepositoryInterface
     */
    private function getProductRepository()
    {
        return \Yii::$container->get(ProductRepositoryInterface::class);
    }

}
