<?php

namespace backend\services\ClientService;

use backend\db\repositories\ClientRepositoryInterface;
use yii\caching\ArrayCache;
use yii\helpers\ArrayHelper;

class ClientService
{
    /**
     * @var \backend\db\repositories\ClientRepositoryInterface
     */
    private $clientRepository;

    /**
     * @var \yii\caching\ArrayCache
     */
    private $cache;

    public function __construct(
        ClientRepositoryInterface $clientRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->cache = new ArrayCache;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array $filter
     * @param array $sort
     * @return \backend\db\models\Client[]
     */
    public function getAllClients(int $limit, int $offset, array $filter = [], array $sort = []): array
    {
        $total = $this->countAllClients($filter);
        $maxOffset = $total > 0 ? ($total - 1) : 0;

        $offset = $offset < 0 ? 25 : $offset;
        $offset = $offset > $maxOffset ? $maxOffset : $offset;

        $limit = $limit < 0 ? 25 : $limit;
        $limit = $limit > 1000 ? 1000 : $limit;

        $clients = $this->clientRepository->findAll($limit, $offset, $filter, $sort);

        $clientsId = ArrayHelper::getColumn($clients, 'id');

        return $clients;
    }

    public function countAllClients(array $filter = null): int
    {
        $result = $this->cache->get(__METHOD__);

        if (!$result) {
            $result = $this->clientRepository->countAll($filter);

            $this->cache->set(__METHOD__, $result);
        }

        return $result;
    }

}
