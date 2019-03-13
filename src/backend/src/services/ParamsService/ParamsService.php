<?php

namespace backend\services\ParamsService;

use backend\db\repositories\db\DbCountryRepository;
use backend\db\repositories\db\DbGroupRepository;
use backend\db\repositories\db\DbRoleRepository;
use backend\db\repositories\db\DbStatusRepository;
use backend\db\repositories\db\DbCurrencyRepository;
use backend\db\repositories\db\DbParamsRepository;
use yii\caching\ArrayCache;
use yii\helpers\ArrayHelper;

class ParamsService
{
    /**
     * @var \backend\db\repositories\DbCountryRepository
     */
    private $dbCountryRepository;

    /**
     * @var \backend\db\repositories\DbRoleRepository
     */
    private $dbRoleRepository;

    /**
     * @var \backend\db\repositories\DbGroupRepository
     */
    private $dbGroupRepository;

    /**
     * @var \backend\db\repositories\DbStatusRepository
     */
    private $dbStatusRepository;

    /**
     * @var \backend\db\repositories\DbCurrencyRepository
     */
    private $dbCurrencyRepository;

    /**
     * @var \backend\db\repositories\DbParamsRepository
     */
    private $dbParamsRepository;

    /**
     * @var \yii\caching\ArrayCache
     */
    private $cache;

    public function __construct(
        DbCountryRepository $dbCountryRepository,
        DbRoleRepository $dbRoleRepository,
        DbGroupRepository $dbGroupRepository,
        DbStatusRepository $dbStatusRepository,
        DbParamsRepository $dbParamsRepository,
        DbCurrencyRepository $dbCurrencyRepository
    ) {
        $this->dbCountryRepository = $dbCountryRepository;
        $this->dbRoleRepository = $dbRoleRepository;
        $this->dbGroupRepository = $dbGroupRepository;
        $this->dbStatusRepository = $dbStatusRepository;
        $this->dbParamsRepository = $dbParamsRepository;
        $this->dbCurrencyRepository = $dbCurrencyRepository;
        $this->cache = new ArrayCache;
    }

    public function getAllParams($filters = array()): array
    {

        if (!empty($filters)) {
            $response = array();
            foreach ($filters as $filter) {
                $func = "get" . ucfirst($filter);
                $response[$filter] = $this->$func();
            }
        }else{
            $response = array(
                'roles' => $this->getRoles(),
                'groups' => $this->getGroups(),
            );
        }

        return $response;
    }

    public function getGroups(): array
    {

        $groups = $this->dbGroupRepository->findAll();

        $id = ArrayHelper::getColumn($groups, 'id');

        return $groups;
    }

    public function getRoles(): array
    {

        $roles = $this->dbRoleRepository->findAll();

        $id = ArrayHelper::getColumn($roles, 'id');

        return $roles;
    }

    public function getCountries(): array
    {

        $countries = $this->dbCountryRepository->findAll();

        $id = ArrayHelper::getColumn($countries, 'id');

        return $countries;
    }

    public function getStatuses(): array
    {

        $statuses = $this->dbStatusRepository->findAll();

        $id = ArrayHelper::getColumn($statuses, 'id');

        return $statuses;
    }

    public function getCurrencies(): array
    {

        $currencies = $this->dbCurrencyRepository->findAll();

        $id = ArrayHelper::getColumn($currencies, 'id');

        return $currencies;
    }

    public function getElements($name): array
    {

        $elements = $this->dbParamsRepository->findElementsByName($name);

        $id = ArrayHelper::getColumn($elements, 'id');

        return $elements;
    }

}
