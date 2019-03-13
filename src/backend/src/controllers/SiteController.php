<?php

namespace backend\controllers;

use backend\exceptions\FormValidationException;
use yii\web\Controller;
use yii\web\HttpException;

class SiteController extends Controller
{

    public function actionError()
    {
        $e = \Yii::$app->errorHandler->exception;

        $error = [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
        ];

        if ($e instanceof HttpException) {
            $error['code'] = $e->statusCode;
        }

        if ($e instanceof FormValidationException) {
            $error['errors'] = $e->getErrors();
        }

        // if (YII_DEBUG) {
        //     $error['trace'] = $e->getTrace();
        //
        //     array_unshift($error['trace'], [
        //         'file' => $e->getFile(),
        //         'line' => $e->getLine(),
        //     ]);
        // }

        return $this->asJson(['error' => $error]);
    }
}
