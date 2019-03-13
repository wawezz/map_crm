<?php

namespace backend\services\WebsocketService;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use yii\helpers\Json;

class WebsocketService
{
    private const ENDPOINT = 'http://nginx:8867/stream';

    public function pub($channels, $message)
    {
        $uri = static::ENDPOINT . '/pub?id=' . $channels;

        $request = new Request('POST', $uri, [], Json::encode($message));

        $this->getClient()->send($request);
    }

    /**
     * @return \GuzzleHttp\Client
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function getClient()
    {
        return \Yii::$container->get(Client::class);
    }
}
