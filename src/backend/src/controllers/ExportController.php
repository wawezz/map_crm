<?php

namespace backend\controllers;

use backend\services\ExcelExportService\ExcelExportService;
use yii\web\Controller;

/**
 * Class ExportController
 * @package backend\controllers
 */
class ExportController extends Controller
{

    /**
     * @return \backend\services\ExcelExportService\ExcelExportService
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function getExcelExportService()
    {
        return \Yii::$container->get(ExcelExportService::class);
    }
}
