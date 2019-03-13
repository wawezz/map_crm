<?php

namespace backend\services\ProductService;

use backend\db\repositories\ProductRepositoryInterface;
use yii\caching\ArrayCache;
use yii\helpers\ArrayHelper;

class ProductService
{
    /**
     * @var \backend\db\repositories\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \yii\caching\ArrayCache
     */
    private $cache;

    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
        $this->cache = new ArrayCache;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array $filter
     * @param array $sort
     * @return \backend\db\models\Product[]
     */
    public function getAllProducts(int $limit, int $offset, array $filter = [], array $sort = []): array
    {
        $total = $this->countAllProducts($filter);
        $maxOffset = $total > 0 ? ($total - 1) : 0;

        $offset = $offset < 0 ? 25 : $offset;
        $offset = $offset > $maxOffset ? $maxOffset : $offset;

        $limit = $limit < 0 ? 25 : $limit;
        $limit = $limit > 1000 ? 1000 : $limit;

        $products = $this->productRepository->findAll($limit, $offset, $filter, $sort);

        $productsId = ArrayHelper::getColumn($products, 'id');

        return $products;
    }

    public function countAllProducts(array $filter = null): int
    {
        $result = $this->cache->get(__METHOD__);

        if (!$result) {
            $result = $this->productRepository->countAll($filter);

            $this->cache->set(__METHOD__, $result);
        }

        return $result;
    }

}
