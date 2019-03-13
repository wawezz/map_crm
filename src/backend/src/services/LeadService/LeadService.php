<?php

namespace backend\services\LeadService;

use backend\db\repositories\LeadRepositoryInterface;
use yii\caching\ArrayCache;
use yii\helpers\ArrayHelper;

class LeadService
{
    /**
     * @var \backend\db\repositories\LeadRepositoryInterface
     */
    private $leadRepository;

    /**
     * @var \yii\caching\ArrayCache
     */
    private $cache;

    public function __construct(
        LeadRepositoryInterface $leadRepository
    ) {
        $this->leadRepository = $leadRepository;
        $this->cache = new ArrayCache;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array $filter
     * @param array $sort
     * @return \backend\db\models\Lead[]
     */
    public function getAllLeads(int $limit, int $offset, array $filter = [], array $sort = []): array
    {
        $total = $this->countAllLeads($filter);
        $maxOffset = $total > 0 ? ($total - 1) : 0;

        $offset = $offset < 0 ? 25 : $offset;
        $offset = $offset > $maxOffset ? $maxOffset : $offset;

        $limit = $limit < 0 ? 25 : $limit;
        $limit = $limit > 1000 ? 1000 : $limit;

        $leads = $this->leadRepository->findAll($limit, $offset, $filter, $sort);

        $leadsId = ArrayHelper::getColumn($leads, 'id');

        return $leads;
    }

    public function countAllLeads(array $filter = null): int
    {
        $result = $this->cache->get(__METHOD__);

        if (!$result) {
            $result = $this->leadRepository->countAll($filter);

            $this->cache->set(__METHOD__, $result);
        }

        return $result;
    }

}
