<?php

namespace backend\controllers\api\v1;

use yii\base\ActionEvent;
use yii\filters\VerbFilter;
use yii\web\Controller;

abstract class BaseController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @var \yii\web\Request
     */
    protected $request;

    /**
     * @var \yii\web\Response
     */
    protected $response;

    public function init()
    {
        parent::init();

        $this->request = \Yii::$app->request;
        $this->response = \Yii::$app->response;

        $this->on(static::EVENT_BEFORE_ACTION, [$this, 'optionsAction']);
    }

    abstract public function getVerbs(): array;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verb'] = [
            'class' => VerbFilter::class,
            'actions' => $this->getVerbs(),
        ];

        return $behaviors;
    }

    public function optionsAction(ActionEvent $event)
    {
        if (\Yii::$app->request->method !== 'OPTIONS') {
            return;
        }

        $event->action->id;

        $verbs = $this->getVerbs();

        $methods = $verbs[$event->action->id] ?? [];

        $headers = \Yii::$app->getResponse()->getHeaders();
        $headers->set('Allow', implode(', ', $methods));
        $headers->set('Access-Control-Allow-Methods', implode(', ', $methods));

        \Yii::$app->end(200);
    }

    /**
     * @return int
     */
    protected function getSleepSeconds(): int
    {
        return (int)$this->request->get('s', 0);
    }
}
