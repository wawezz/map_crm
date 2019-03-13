<?php

namespace backend\controllers\api\v1;

class IndexController extends BaseController
{
    public function getVerbs(): array
    {
        return ['index' => ['GET']];
    }

    public function actionIndex()
    {
        return $this->asJson([
            'env' => YII_ENV,
            'date' => new \DateTimeImmutable('now', new \DateTimeZone('UTC')),
        ]);
    }
}
