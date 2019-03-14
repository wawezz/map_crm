<?php

namespace backend\services\PlaceLeadService;

use backend\db\repositories\PlaceLeadRepositoryInterface;
use yii\caching\ArrayCache;
use yii\helpers\ArrayHelper;

class PlaceLeadService
{
    /**
     * @var \backend\db\repositories\PlaceLeadRepositoryInterface
     */
    private $placeLeadRepository;

    /**
     * @var \yii\caching\ArrayCache
     */
    private $cache;

    public function __construct(
        PlaceLeadRepositoryInterface $placeLeadRepository
    ) {
        $this->placeLeadRepository = $placeLeadRepository;
        $this->cache = new ArrayCache;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array $filter
     * @param array $sort
     * @return \backend\db\models\PlaceLead[]
     */
    public function getAllPlaceLeads(int $limit, int $offset, array $filter = [], array $sort = []): array
    {
        $total = $this->countAllPlaceLeads($filter);
        $maxOffset = $total > 0 ? ($total - 1) : 0;

        $offset = $offset < 0 ? 25 : $offset;
        $offset = $offset > $maxOffset ? $maxOffset : $offset;

        $limit = $limit < 0 ? 25 : $limit;
        $limit = $limit > 1000 ? 1000 : $limit;

        $placeLeads = $this->placeLeadRepository->findAll($limit, $offset, $filter, $sort);

        $placeLeadsId = ArrayHelper::getColumn($placeLeads, 'id');

        return $placeLeads;
    }

    public function countAllPlaceLeads(array $filter = null): int
    {
        $result = $this->cache->get(__METHOD__);

        if (!$result) {
            $result = $this->placeLeadRepository->countAll($filter);

            $this->cache->set(__METHOD__, $result);
        }

        return $result;
    }

}
